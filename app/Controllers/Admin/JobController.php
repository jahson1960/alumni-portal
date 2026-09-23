<?php

namespace App\Controllers\Admin;

use App\Core\Sanitizer;
use App\Core\Upload;
use App\Models\Category;
use App\Models\Company;
use App\Models\Job;
use App\Models\Notification;

class JobController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.jobs.index', [
            'title' => 'Jobs',
            'activeNav' => 'jobs',
            'jobs' => Job::all('created_at DESC'),
            'pendingCount' => Job::countPendingApproval(),
        ]);
    }

    public function pending(): void
    {
        $this->view('admin.jobs.pending', [
            'title' => 'Job Approval Queue',
            'activeNav' => 'jobs',
            'jobs' => Job::pendingApproval(),
        ]);
    }

    public function approve(string $id): void
    {
        $this->requireCsrf();
        $job = Job::find((int) $id);
        Job::approve((int) $id);
        if ($job && $job['posted_by']) {
            Notification::notify(
                (int) $job['posted_by'],
                'job_approved',
                'Your job post "' . $job['title'] . '" was approved and is now live.',
                'jobs/' . $id
            );
        }
        $this->flash('success', 'Job approved and published.');
        $this->redirect('admin/jobs/pending');
    }

    public function reject(string $id): void
    {
        $this->requireCsrf();
        $job = Job::find((int) $id);
        Job::reject((int) $id);
        if ($job && $job['posted_by']) {
            Notification::notify(
                (int) $job['posted_by'],
                'job_rejected',
                'Your job post "' . $job['title'] . '" was not approved. You can edit and resubmit it.',
                'jobs/mine'
            );
        }
        $this->flash('success', 'Job rejected.');
        $this->redirect('admin/jobs/pending');
    }

    public function create(): void
    {
        $this->view('admin.jobs.form', [
            'title' => 'New Job Listing',
            'activeNav' => 'jobs',
            'job' => null,
            'allCompanies' => Company::all(),
            'allCategories' => Category::forType('job'),
            'selectedCategoryIds' => [],
        ]);
    }

    public function store(): void
    {
        $this->save(null);
    }

    public function edit(string $id): void
    {
        $job = Job::find((int) $id);
        if (!$job) {
            $this->flash('error', 'Job not found.');
            $this->redirect('admin/jobs');
        }

        $this->view('admin.jobs.form', [
            'title' => 'Edit Job Listing',
            'activeNav' => 'jobs',
            'job' => $job,
            'allCompanies' => Company::all(),
            'allCategories' => Category::forType('job'),
            'selectedCategoryIds' => array_column(Category::forJob((int) $id), 'id'),
        ]);
    }

    public function update(string $id): void
    {
        $this->save((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        Job::delete((int) $id);
        $this->flash('success', 'Job listing deleted.');
        $this->redirect('admin/jobs');
    }

    private function save(?int $id): void
    {
        $this->requireCsrf();

        $title = trim((string) $this->input('title', ''));
        $company = trim((string) $this->input('company', ''));
        $companyId = $this->input('company_id') !== null && $this->input('company_id') !== '' ? (int) $this->input('company_id') : null;
        $companyLogoUrl = trim((string) $this->input('company_logo_url', ''));
        $companyAbout = trim((string) $this->input('company_about', ''));
        $saveCompany = (bool) $this->input('save_company');
        $categoryIds = array_map('intval', (array) $this->input('categories', []));
        $location = trim((string) $this->input('location', ''));
        $jobType = in_array($this->input('job_type'), Job::JOB_TYPES, true) ? $this->input('job_type') : 'Full-time';
        $experienceLevel = array_key_exists($this->input('experience_level'), Job::EXPERIENCE_LEVELS) ? $this->input('experience_level') : null;
        $workMode = array_key_exists($this->input('work_mode'), Job::WORK_MODES) ? $this->input('work_mode') : 'onsite';
        $salaryMin = $this->input('salary_min') !== '' && $this->input('salary_min') !== null ? (int) $this->input('salary_min') : null;
        $salaryMax = $this->input('salary_max') !== '' && $this->input('salary_max') !== null ? (int) $this->input('salary_max') : null;
        $description = trim((string) $this->input('description', ''));
        $responsibilities = trim((string) $this->input('responsibilities', ''));
        $requirements = trim((string) $this->input('requirements', ''));
        $companyBg = trim((string) $this->input('company_bg_color', '#f8fafc'));
        $companyText = trim((string) $this->input('company_text_color', '#091a2e'));
        $isFeatured = $this->input('is_featured') ? 1 : 0;
        $status = $this->input('status', 'open') === 'closed' ? 'closed' : 'open';
        $applyType = $this->input('apply_type', 'email') === 'link' ? 'link' : 'email';
        $applyValue = trim((string) $this->input('apply_value', ''));
        $closingDate = trim((string) $this->input('closing_date', ''));
        $logoDisplayMode = $this->input('logo_display_mode', 'logo') === 'badge' ? 'badge' : 'logo';

        $errors = [];
        if ($title === '') {
            $errors[] = 'Job title is required.';
        }
        if ($company === '') {
            $errors[] = 'Company is required.';
        }
        if ($applyValue === '') {
            $errors[] = 'Please provide an application email or link.';
        } elseif ($applyType === 'email' && !filter_var($applyValue, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please provide a valid application email address.';
        } elseif ($applyType === 'link' && !filter_var($applyValue, FILTER_VALIDATE_URL)) {
            $errors[] = 'Please provide a valid application URL (including https://).';
        }

        $description = Sanitizer::html($description);
        $responsibilities = Sanitizer::html($responsibilities);
        $requirements = Sanitizer::html($requirements);
        $companyAbout = Sanitizer::html($companyAbout);

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
            $this->redirect($id ? "admin/jobs/{$id}/edit" : 'admin/jobs/create');
        }

        if ($saveCompany) {
            $companyData = ['name' => $company, 'about' => $companyAbout];
            if ($companyLogo) {
                $companyData['logo'] = $companyLogo;
            }
            if ($companyId) {
                Company::update($companyId, $companyData);
            } else {
                $companyId = Company::create($companyData);
            }
        }

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
            'company_about' => $companyAbout !== '' ? $companyAbout : null,
            'company_bg_color' => $companyBg !== '' ? $companyBg : '#f8fafc',
            'company_text_color' => $companyText !== '' ? $companyText : '#091a2e',
            'apply_type' => $applyType,
            'apply_value' => $applyValue,
            'is_featured' => $isFeatured,
            'status' => $status,
            'approval_status' => 'approved',
            'closing_date' => $closingDate !== '' ? $closingDate : null,
            'logo_display_mode' => $logoDisplayMode,
        ];
        if ($companyLogo) {
            $data['company_logo'] = $companyLogo;
        }

        if ($id === null) {
            $data['posted_at'] = date('Y-m-d H:i:s');
            $id = Job::create($data);
            $this->flash('success', 'Job listing created.');
        } else {
            Job::update($id, $data);
            $this->flash('success', 'Job listing updated.');
        }
        Category::syncJobCategories($id, $categoryIds);

        $this->redirect('admin/jobs');
    }
}
