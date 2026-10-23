<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Sanitizer;
use App\Core\Upload;
use App\Models\ApplicationTemplate;
use App\Models\ApplicationTemplateField;
use App\Models\Category;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobAlert;
use App\Models\JobApplication;
use App\Models\Notification;
use App\Models\PageTabVisibility;
use App\Models\SavedJob;
use App\Models\Setting;
use App\Models\User;

class JobController extends Controller
{
    /** Careers is one tabbed page — Job Board, Advanced Search, Companies Hiring, Saved Jobs, Job Alerts. */
    private const TABS = ['board', 'search', 'companies', 'saved', 'alerts'];

    public function index(): void
    {
        $this->requireVisibility('jobs');

        $tab = in_array($this->input('tab'), self::TABS, true) ? $this->input('tab') : 'board';
        if (!PageTabVisibility::isVisible('jobs', $tab)) {
            $fallback = PageTabVisibility::firstVisibleTab('jobs', self::TABS);
            if ($fallback === null) {
                http_response_code(404);
                (new ErrorController())->notFound();
                return;
            }
            $tab = $fallback;
        }
        if (in_array($tab, ['saved', 'alerts'], true) && !Auth::check()) {
            $this->redirect('login');
        }

        $data = ['title' => 'Careers', 'activeNav' => 'jobs', 'tab' => $tab];
        $data += match ($tab) {
            'search' => $this->searchTabData(),
            'companies' => $this->companiesTabData(),
            'saved' => $this->savedTabData(),
            'alerts' => $this->alertsTabData(),
            default => $this->boardTabData(),
        };

        $this->view('jobs.index', $data);
    }

    /** Old standalone URL — kept as a redirect so bookmarks/links still land on the right tab. */
    public function searchForm(): void
    {
        $this->redirect('jobs?tab=search');
    }

    private function boardTabData(): array
    {
        $filters = $this->parseFilters();

        return [
            'jobs' => Job::search($filters),
            'totalCount' => Job::countSearch($filters),
            'allCategories' => Category::forType('job'),
            'activeCategory' => $filters['category'] ?? null,
            'search' => $filters['q'] ?? '',
            'location' => $filters['location'] ?? '',
            'filters' => $filters,
            'savedJobIds' => $this->savedJobIds(),
            'appliedJobIds' => $this->appliedJobIds(),
            'resumeUrl' => Auth::check() ? (Auth::user()['resume_file'] ?? Auth::user()['resume_url'] ?? null) : null,
            'jobTypeCounts' => Job::countsByJobType(),
            'experienceCounts' => Job::countsByExperienceLevel(),
            'locationBreakdown' => Job::locationBreakdown(),
            'latestEmployers' => Company::withOpenJobCounts(null, null, 6),
        ];
    }

    private function searchTabData(): array
    {
        return [
            'allCategories' => Category::forType('job'),
            'industries' => Company::hiringIndustries(),
            'filters' => $this->parseFilters(),
        ];
    }

    private function companiesTabData(): array
    {
        $search = trim((string) $this->input('q', ''));
        $industry = (string) $this->input('industry', '');
        $page = max(1, (int) $this->input('page', 1));
        $perPage = 12;

        $total = Company::countWithOpenJobs($search !== '' ? $search : null, $industry !== '' ? $industry : null);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $totalPages);

        return [
            'companies' => Company::withOpenJobCounts(
                $search !== '' ? $search : null,
                $industry !== '' ? $industry : null,
                $perPage,
                $perPage * ($page - 1)
            ),
            'industries' => Company::hiringIndustries(),
            'search' => $search,
            'activeIndustry' => $industry,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ];
    }

    private function savedTabData(): array
    {
        return [
            'jobs' => Job::savedByUser((int) Auth::id()),
            'appliedIds' => JobApplication::appliedIdsFor((int) Auth::id()),
        ];
    }

    private function alertsTabData(): array
    {
        $alerts = JobAlert::forUser((int) Auth::id());
        foreach ($alerts as &$alert) {
            $alertFilters = JobAlert::toJobFilters($alert);
            $alert['match_count'] = Job::countSearch($alertFilters);
            $alert['match_query'] = http_build_query($alertFilters);
        }
        unset($alert);

        return ['alerts' => $alerts];
    }

    /** Reads and validates every Job Board / Advanced Search filter param from the request. */
    private function parseFilters(): array
    {
        $experienceLevel = (string) $this->input('experience_level', '');
        $salary = (string) $this->input('salary', '');
        $postedWithin = (string) $this->input('posted_within', '');

        $filters = [
            'q' => trim((string) $this->input('q', '')),
            'location' => trim((string) $this->input('location', '')),
            'category' => (string) $this->input('category', ''),
            'industry' => (string) $this->input('industry', ''),
            'company' => trim((string) $this->input('company', '')),
            'experience_level' => in_array($experienceLevel, array_keys(Job::EXPERIENCE_LEVELS), true) ? $experienceLevel : '',
            'job_type' => array_values(array_intersect((array) $this->input('job_type', []), Job::JOB_TYPES)),
            'work_mode' => array_values(array_intersect((array) $this->input('work_mode', []), array_keys(Job::WORK_MODES))),
            'salary' => isset(Job::SALARY_BUCKETS[$salary]) ? $salary : '',
            'posted_within' => isset(Job::POSTED_WITHIN[$postedWithin]) ? $postedWithin : '',
            'sort' => $this->input('sort') === 'salary' ? 'salary' : 'recent',
        ];

        return array_filter($filters, fn ($v) => $v !== '' && $v !== []);
    }

    public function show(string $id): void
    {
        $job = Job::find((int) $id);
        $isOwnerOrAdmin = $job && Auth::check() && (Auth::isAdmin() || (int) ($job['posted_by'] ?? 0) === (int) Auth::id());
        if (!$job || ($job['approval_status'] !== 'approved' && !$isOwnerOrAdmin)) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $poster = !empty($job['posted_by']) ? User::find((int) $job['posted_by']) : null;
        $company = !empty($job['company_id']) ? Company::find((int) $job['company_id']) : null;
        [$backUrl, $backLabel] = $this->smartBack(url('jobs'), 'Back to Jobs');

        $this->view('jobs.show', [
            'title' => $job['title'] . ' - ' . $job['company'],
            'activeNav' => 'jobs',
            'job' => $job,
            'jobUrl' => url('jobs/' . $id),
            'isClosed' => Job::isClosed($job),
            'isOwnerOrAdmin' => $isOwnerOrAdmin,
            'poster' => $poster ?: null,
            'categories' => Category::forJob((int) $id),
            'companyWebsite' => $company['website'] ?? null,
            'isSaved' => Auth::check() ? SavedJob::isSaved((int) Auth::id(), (int) $id) : false,
            'hasApplied' => Auth::check() ? JobApplication::hasApplied((int) Auth::id(), (int) $id) : false,
            'showApplyReveal' => $this->input('applied') === '1',
            'backUrl' => $backUrl,
            'backLabel' => $backLabel,
        ]);
    }

    public function postForm(): void
    {
        $this->guard();
        $this->view('jobs.post', [
            'title' => 'Post a Job',
            'activeNav' => 'jobs',
            'job' => null,
            'allCategories' => Category::forType('job'),
            'selectedCategoryIds' => [],
            'allCompanies' => Company::all(),
            'postingMode' => Setting::get('job_posting_mode', 'approval'),
            'needsRichEditor' => true,
            'myTemplates' => ApplicationTemplate::forUser((int) Auth::id()),
        ]);
    }

    public function postStore(): void
    {
        $this->requireCsrf();
        $this->guard();
        $this->savePosted(null);
    }

    public function mine(): void
    {
        $this->guard();

        $jobs = Job::postedByUser((int) Auth::id());
        $applicantCounts = JobApplication::countsForJobs(array_column($jobs, 'id'));
        $stats = ['draft' => 0, 'pending' => 0, 'active' => 0, 'closed' => 0];
        foreach ($jobs as &$job) {
            $job['status_group'] = $this->jobStatusGroup($job);
            $stats[$job['status_group']]++;
        }
        unset($job);

        $this->view('jobs.mine', [
            'title' => 'My Job Posts',
            'activeNav' => 'jobs',
            'jobs' => $jobs,
            'applicantCounts' => $applicantCounts,
            'stats' => $stats,
            'supportEmail' => Setting::get('support_email', 'support@rbsn.example.com'),
            'premiumHiringEmail' => Setting::get('premium_hiring_email', 'partnerships@rbsn.example.com'),
        ]);
    }

    /** Buckets a self-posted job into one of the "My Job Posts" tabs: draft, pending (Under Review), active, or closed. */
    private function jobStatusGroup(array $job): string
    {
        if ($job['approval_status'] === 'draft') {
            return 'draft';
        }
        if ($job['approval_status'] === 'pending') {
            return 'pending';
        }
        if ($job['approval_status'] === 'rejected' || Job::isClosed($job)) {
            return 'closed';
        }
        return 'active';
    }

    public function editMine(string $id): void
    {
        $this->guard();
        $job = Job::find((int) $id);
        if (!$job || (int) $job['posted_by'] !== (int) Auth::id()) {
            $this->flash('error', 'Job not found.');
            $this->redirect('jobs/mine');
        }

        $this->view('jobs.post', [
            'title' => 'Edit Job Post',
            'activeNav' => 'jobs',
            'job' => $job,
            'allCategories' => Category::forType('job'),
            'selectedCategoryIds' => array_column(Category::forJob((int) $id), 'id'),
            'allCompanies' => Company::all(),
            'postingMode' => Setting::get('job_posting_mode', 'approval'),
            'needsRichEditor' => true,
            'myTemplates' => ApplicationTemplate::forUser((int) Auth::id()),
        ]);
    }

    public function updateMine(string $id): void
    {
        $this->requireCsrf();
        $this->guard();
        $job = Job::find((int) $id);
        if (!$job || (int) $job['posted_by'] !== (int) Auth::id()) {
            $this->flash('error', 'Job not found.');
            $this->redirect('jobs/mine');
        }
        $this->savePosted((int) $id, $job);
    }

    public function deleteMine(string $id): void
    {
        $this->requireCsrf();
        $this->guard();
        $job = Job::find((int) $id);
        if ($job && (int) $job['posted_by'] === (int) Auth::id()) {
            Job::delete((int) $id);
            $this->flash('success', 'Job post removed.');
        }
        $this->redirect('jobs/mine');
    }

    /** Old standalone URL — kept as a redirect so bookmarks/links still land on the right tab. */
    public function saved(): void
    {
        if (!Auth::check()) {
            $this->redirect('login');
        }
        $this->redirect('jobs?tab=saved');
    }

    /**
     * Tracks the click-through. A link-type job redirects straight to the poster's external
     * application URL. An email-type job never launches a mailto: popup - the job page reveals
     * the email (and the poster's application instructions, if any) in place instead, so this
     * just records the click and reports back; the JS on the page does the revealing (with a
     * plain redirect-and-reveal fallback for non-JS requests). A form-type job's "Apply" button
     * is a link to the GET applyForm() page instead of this POST action, so by the time this
     * runs for a form-type job it's always the applicant's actual submitted answers.
     */
    public function apply(string $id): void
    {
        $this->requireCsrf();
        $job = Job::find((int) $id);
        if (!$job) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        if ($job['apply_type'] === 'form') {
            $this->submitApplicationForm($job);
            return;
        }

        if (!Auth::check() && Setting::get('require_login_to_apply', '0') === '1') {
            $this->redirect('login?redirect=' . rawurlencode(url('jobs/' . $id)));
        }

        if (Auth::check()) {
            JobApplication::record((int) Auth::id(), (int) $id);
        }

        if ($job['apply_type'] === 'link') {
            header('Location: ' . $job['apply_value']);
            exit;
        }

        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode(['ok' => true]);
            return;
        }

        $this->redirect('jobs/' . $id . '?applied=1');
    }

    /** GET /jobs/{id}/apply — the in-app application form for a 'form'-method job. */
    public function applyForm(string $id): void
    {
        $job = Job::find((int) $id);
        if (!$job || $job['apply_type'] !== 'form' || $job['approval_status'] !== 'approved') {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }
        if (!Auth::check()) {
            $this->redirect('login?redirect=' . rawurlencode(url('jobs/' . $id . '/apply')));
        }
        if (Job::isClosed($job)) {
            $this->flash('error', 'This job is no longer accepting applications.');
            $this->redirect('jobs/' . $id);
        }

        $fields = $job['application_template_id'] ? ApplicationTemplateField::forTemplate((int) $job['application_template_id']) : [];

        // Prefilling a resubmit matches previous answers back onto today's fields by label,
        // since responses are stored as a label/type/value snapshot rather than a live
        // reference to field rows the poster may have since edited.
        $previousByLabel = [];
        $existing = JobApplication::findForUserJob((int) Auth::id(), (int) $id) ?: null;
        if ($existing && $existing['responses']) {
            foreach (json_decode($existing['responses'], true) ?: [] as $response) {
                $previousByLabel[$response['label']] = $response['value'];
            }
        }

        $this->view('jobs.apply', [
            'title' => 'Apply - ' . $job['title'],
            'activeNav' => 'jobs',
            'job' => $job,
            'fields' => $fields,
            'previousByLabel' => $previousByLabel,
            'isResubmit' => $existing !== null,
        ]);
    }

    /** POST handler for a 'form'-method job, called from apply() above. */
    private function submitApplicationForm(array $job): void
    {
        $id = (int) $job['id'];
        if (!Auth::check()) {
            $this->redirect('login?redirect=' . rawurlencode(url('jobs/' . $id . '/apply')));
        }
        if (Job::isClosed($job)) {
            $this->flash('error', 'This job is no longer accepting applications.');
            $this->redirect('jobs/' . $id);
        }

        $fields = $job['application_template_id'] ? ApplicationTemplateField::forTemplate((int) $job['application_template_id']) : [];

        // Resubmitting keeps a file field's previously uploaded file when no new one is chosen,
        // rather than treating a blank file input as "clear it" or re-failing required-ness.
        $previousByLabel = [];
        $existing = JobApplication::findForUserJob((int) Auth::id(), $id);
        if ($existing && $existing['responses']) {
            foreach (json_decode($existing['responses'], true) ?: [] as $response) {
                $previousByLabel[$response['label']] = $response['value'];
            }
        }

        $errors = [];
        $responses = [];
        foreach ($fields as $field) {
            $inputName = 'field_' . $field['id'];
            if ($field['field_type'] === 'file') {
                $value = null;
                try {
                    $uploaded = Upload::document($this->file($inputName), 'applications');
                    if ($uploaded) {
                        $value = upload_url($uploaded);
                    }
                } catch (\RuntimeException $e) {
                    $errors[] = $field['label'] . ': ' . $e->getMessage();
                }
                if (!$value) {
                    $value = $previousByLabel[$field['label']] ?? null;
                }
                if (!$value && $field['is_required']) {
                    $errors[] = $field['label'] . ' is required.';
                }
            } else {
                $value = trim((string) $this->input($inputName, ''));
                if ($value === '' && $field['is_required']) {
                    $errors[] = $field['label'] . ' is required.';
                }
            }
            if ($value !== null && $value !== '') {
                $responses[] = ['label' => $field['label'], 'type' => $field['field_type'], 'value' => $value];
            }
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect('jobs/' . $id . '/apply');
        }

        JobApplication::submitForm((int) Auth::id(), $id, $responses);

        if (!empty($job['posted_by'])) {
            Notification::notify(
                (int) $job['posted_by'],
                'job_application',
                Auth::user()['name'] . ' applied to your job post "' . $job['title'] . '".',
                'jobs/' . $id . '/applicants'
            );
        }

        $this->flash('success', 'Your application was submitted. The poster can review it from their Applicants list.');
        $this->redirect('jobs/' . $id);
    }

    /** GET /jobs/{id}/applicants — the poster's (or an admin's) view of everyone who applied. */
    public function applicants(string $id): void
    {
        $job = Job::find((int) $id);
        $isOwnerOrAdmin = $job && Auth::check() && (Auth::isAdmin() || (int) ($job['posted_by'] ?? 0) === (int) Auth::id());
        if (!$job || !$isOwnerOrAdmin) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $fields = $job['application_template_id'] ? ApplicationTemplateField::forTemplate((int) $job['application_template_id']) : [];
        $applicants = JobApplication::forJob((int) $id);
        foreach ($applicants as &$applicant) {
            $applicant['responses'] = $applicant['responses'] ? json_decode($applicant['responses'], true) : [];
        }
        unset($applicant);

        $this->view('jobs.applicants', [
            'title' => 'Applicants - ' . $job['title'],
            'activeNav' => 'jobs',
            'job' => $job,
            'fields' => $fields,
            'applicants' => $applicants,
        ]);
    }

    public function updateApplicantStatus(string $id, string $appId): void
    {
        $this->requireCsrf();
        $job = Job::find((int) $id);
        $isOwnerOrAdmin = $job && Auth::check() && (Auth::isAdmin() || (int) ($job['posted_by'] ?? 0) === (int) Auth::id());
        if (!$job || !$isOwnerOrAdmin) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $application = JobApplication::find((int) $appId);
        $status = in_array($this->input('status'), ['new', 'reviewed', 'shortlisted', 'rejected'], true) ? $this->input('status') : 'new';
        if ($application && (int) $application['job_id'] === (int) $id) {
            JobApplication::updateStatus((int) $appId, $status);
        }

        $this->redirect('jobs/' . $id . '/applicants');
    }

    public function toggleSave(string $id): void
    {
        $this->requireCsrf();
        if (!Auth::check()) {
            $this->redirect('login');
        }

        SavedJob::toggle((int) Auth::id(), (int) $id);

        $fallback = Setting::get('save_job_redirects_to_details', '0') === '1' ? "jobs/{$id}" : 'jobs';
        $this->redirectBack($fallback);
    }

    private function savedJobIds(): array
    {
        if (!Auth::check()) {
            return [];
        }
        return array_column(Job::savedByUser((int) Auth::id()), 'id');
    }

    private function appliedJobIds(): array
    {
        if (!Auth::check()) {
            return [];
        }
        return JobApplication::appliedIdsFor((int) Auth::id());
    }

    private function guard(): void
    {
        if (!Auth::check() || !Auth::user()) {
            Auth::logout();
            header('Location: ' . url('login'));
            exit;
        }
    }

    /** Shared create/update handler for alumni self-posted jobs (postStore / updateMine). */
    private function savePosted(?int $id, ?array $existing = null): void
    {
        $title = trim((string) $this->input('title', ''));
        $company = trim((string) $this->input('company', ''));
        $companyId = $this->input('company_id') !== null && $this->input('company_id') !== '' ? (int) $this->input('company_id') : null;
        $companyLogoUrl = trim((string) $this->input('company_logo_url', ''));
        $location = trim((string) $this->input('location', ''));
        $jobType = in_array($this->input('job_type'), Job::JOB_TYPES, true) ? $this->input('job_type') : 'Full-time';
        $experienceLevel = array_key_exists($this->input('experience_level'), Job::EXPERIENCE_LEVELS) ? $this->input('experience_level') : null;
        $workMode = array_key_exists($this->input('work_mode'), Job::WORK_MODES) ? $this->input('work_mode') : 'onsite';
        $salaryMin = $this->input('salary_min') !== '' && $this->input('salary_min') !== null ? (int) $this->input('salary_min') : null;
        $salaryMax = $this->input('salary_max') !== '' && $this->input('salary_max') !== null ? (int) $this->input('salary_max') : null;
        $description = Sanitizer::html(trim((string) $this->input('description', '')));
        $responsibilities = Sanitizer::html(trim((string) $this->input('responsibilities', '')));
        $requirements = Sanitizer::html(trim((string) $this->input('requirements', '')));
        $categoryIds = array_slice(array_map('intval', (array) $this->input('categories', [])), 0, 3);
        $applyType = in_array($this->input('apply_type'), ['link', 'form'], true) ? $this->input('apply_type') : 'email';
        $applyValue = trim((string) $this->input('apply_value', ''));
        $applicationInstructions = trim((string) $this->input('application_instructions', ''));
        $applicationTemplateId = $this->input('application_template_id') !== null && $this->input('application_template_id') !== ''
            ? (int) $this->input('application_template_id') : null;
        $closingDate = trim((string) $this->input('closing_date', ''));
        $asDraft = $this->input('action') === 'draft';

        $errors = [];
        if ($title === '') {
            $errors[] = 'Job title is required.';
        }
        if ($company === '') {
            $errors[] = 'Company is required.';
        }
        if (!$asDraft) {
            if ($applyType === 'form') {
                if (!$applicationTemplateId || !ApplicationTemplate::findForUser($applicationTemplateId, (int) Auth::id())) {
                    $errors[] = 'Please choose an application template.';
                }
            } elseif ($applyValue === '') {
                $errors[] = 'Please provide an application email or link.';
            } elseif ($applyType === 'email' && !filter_var($applyValue, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please provide a valid application email address.';
            } elseif ($applyType === 'link' && !filter_var($applyValue, FILTER_VALIDATE_URL)) {
                $errors[] = 'Please provide a valid application URL (including https://).';
            }
        }

        $companyLogo = $companyLogoUrl !== '' ? $companyLogoUrl : null;
        try {
            $uploadedLogo = Upload::image($this->file('company_logo'), 'jobs');
            if ($uploadedLogo) {
                $companyLogo = upload_url($uploadedLogo);
            }
        } catch (\RuntimeException $e) {
            $errors[] = $e->getMessage();
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect($id ? "jobs/mine/{$id}/edit" : 'jobs/post');
        }

        $mode = Setting::get('job_posting_mode', 'approval');
        $data = [
            'title' => $title,
            'company' => $company,
            'company_id' => $companyId,
            'location' => $location,
            'job_type' => $jobType,
            'experience_level' => $experienceLevel,
            'work_mode' => $workMode,
            'salary_min' => $salaryMin,
            'salary_max' => $salaryMax,
            'description' => $description,
            'responsibilities' => $responsibilities !== '' ? $responsibilities : null,
            'requirements' => $requirements !== '' ? $requirements : null,
            'apply_type' => $applyType,
            'apply_value' => $applyValue,
            'application_instructions' => $applicationInstructions !== '' ? $applicationInstructions : null,
            'application_template_id' => $applyType === 'form' ? $applicationTemplateId : null,
            'closing_date' => $closingDate !== '' ? $closingDate : null,
        ];
        if ($companyLogo) {
            $data['company_logo'] = $companyLogo;
        }

        if ($id === null) {
            $data['posted_by'] = Auth::id();
            if ($asDraft) {
                $data['approval_status'] = 'draft';
                $data['posted_at'] = null;
            } else {
                $data['approval_status'] = $mode === 'direct' ? 'approved' : 'pending';
                $data['posted_at'] = $mode === 'direct' ? date('Y-m-d H:i:s') : null;
            }
            $id = Job::create($data);

            if ($asDraft) {
                $this->flash('success', 'Draft saved. Resume it any time from My Job Posts.');
            } elseif ($data['approval_status'] === 'pending') {
                foreach (User::adminIds() as $adminId) {
                    Notification::notify(
                        (int) $adminId,
                        'job_pending',
                        Auth::user()['name'] . ' submitted a job posting for review.',
                        'admin/jobs/pending'
                    );
                }
                $this->flash('success', 'Your job post was submitted and is awaiting admin approval.');
            } else {
                $this->flash('success', 'Your job post is now live.');
            }
        } else {
            $wasUnpublished = in_array($existing['approval_status'] ?? '', ['rejected', 'draft'], true);
            if ($asDraft) {
                $data['approval_status'] = 'draft';
                $this->flash('success', 'Draft updated.');
            } elseif ($wasUnpublished) {
                $data['approval_status'] = $mode === 'direct' ? 'approved' : 'pending';
                $data['posted_at'] = $mode === 'direct' ? date('Y-m-d H:i:s') : null;
                if ($data['approval_status'] === 'pending') {
                    foreach (User::adminIds() as $adminId) {
                        Notification::notify(
                            (int) $adminId,
                            'job_pending',
                            Auth::user()['name'] . ' submitted a job posting for review.',
                            'admin/jobs/pending'
                        );
                    }
                    $this->flash('success', 'Your job post was submitted and is awaiting admin approval.');
                } else {
                    $this->flash('success', 'Your job post is now live.');
                }
            } else {
                $this->flash('success', 'Job post updated.');
            }
            Job::update($id, $data);
        }
        Category::syncJobCategories($id, $categoryIds);

        $this->redirect('jobs/mine');
    }
}
