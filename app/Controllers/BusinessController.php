<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Upload;
use App\Models\Business;
use App\Models\SavedBusiness;

class BusinessController extends Controller
{
    private const FILTER_KEYS = ['q', 'category', 'business_type', 'location', 'founded_year'];
    private const SORTS = ['name_asc', 'newest', 'oldest'];
    private const PER_PAGE_OPTIONS = [9, 18, 36];

    public function index(): void
    {
        $this->requireVisibility('businesses');

        $filters = [];
        foreach (self::FILTER_KEYS as $key) {
            $value = trim((string) $this->input($key, ''));
            if ($value !== '') {
                $filters[$key] = $value;
            }
        }

        $sort = in_array($this->input('sort'), self::SORTS, true) ? $this->input('sort') : 'name_asc';
        $perPageInput = (int) $this->input('per_page', 9);
        $perPage = in_array($perPageInput, self::PER_PAGE_OPTIONS, true) ? $perPageInput : 9;
        $page = max(1, (int) $this->input('page', 1));

        $total = Business::countDirectory($filters);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $totalPages);

        $viewerLoggedIn = Auth::check();
        $savedIds = $viewerLoggedIn ? SavedBusiness::savedIdsFor((int) Auth::id()) : [];

        $this->view('businesses.index', [
            'title' => 'Alumni Businesses',
            'activeNav' => 'businesses',
            'businesses' => Business::directory($filters, $sort, $perPage, ($page - 1) * $perPage),
            'categories' => Business::distinctValues('category'),
            'businessTypeOptions' => Business::distinctValues('business_type'),
            'locationOptions' => Business::distinctValues('location'),
            'foundedYearOptions' => Business::distinctValues('founded_year'),
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
            'sort' => $sort,
            'filters' => $filters,
            'savedIds' => $savedIds,
        ]);
    }

    public function toggleSave(string $id): void
    {
        $this->requireCsrf();
        if (!Auth::check()) {
            $this->redirect('login');
        }

        SavedBusiness::toggle((int) Auth::id(), (int) $id);
        $this->redirectBack('businesses');
    }

    public function show(string $id): void
    {
        $business = Business::findWithOwner((int) $id);
        if (!$business) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        [$backUrl, $backLabel] = $this->smartBack(url('businesses'), 'Back to Businesses');

        $this->view('businesses.show', [
            'title' => $business['name'],
            'activeNav' => 'businesses',
            'business' => $business,
            'backUrl' => $backUrl,
            'backLabel' => $backLabel,
        ]);
    }

    public function mine(): void
    {
        $this->guard();
        $this->view('businesses.mine', [
            'title' => 'My Businesses',
            'activeNav' => 'businesses',
            'businesses' => Business::byOwner((int) Auth::id()),
        ]);
    }

    public function create(): void
    {
        $this->guard();
        $this->view('businesses.form', [
            'title' => 'Add Business',
            'activeNav' => 'businesses',
            'business' => null,
        ]);
    }

    public function store(): void
    {
        $this->save(null);
    }

    public function edit(string $id): void
    {
        $this->guard();
        $business = Business::find((int) $id);
        if (!$business || ((int) $business['owner_id'] !== (int) Auth::id() && !Auth::isAdmin())) {
            $this->flash('error', 'Business not found.');
            $this->redirect('businesses/mine');
        }

        $this->view('businesses.form', [
            'title' => 'Edit Business',
            'activeNav' => 'businesses',
            'business' => $business,
        ]);
    }

    public function update(string $id): void
    {
        $this->save((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        $this->guard();
        Business::deleteAsOwnerOrAdmin((int) $id, (int) Auth::id(), Auth::isAdmin());
        $this->flash('success', 'Business listing removed.');
        $this->redirect('businesses/mine');
    }

    private function save(?int $id): void
    {
        $this->requireCsrf();
        $this->guard();

        if ($id !== null) {
            $existing = Business::find($id);
            if (!$existing || ((int) $existing['owner_id'] !== (int) Auth::id() && !Auth::isAdmin())) {
                $this->flash('error', 'Business not found.');
                $this->redirect('businesses/mine');
            }
        }

        $name = trim((string) $this->input('name', ''));
        $category = trim((string) $this->input('category', ''));
        $businessType = trim((string) $this->input('business_type', ''));
        $description = trim((string) $this->input('description', ''));
        $website = trim((string) $this->input('website', ''));
        $location = trim((string) $this->input('location', ''));
        $foundedYear = trim((string) $this->input('founded_year', ''));

        $errors = [];
        if ($name === '') {
            $errors[] = 'Business name is required.';
        }
        if ($foundedYear !== '' && !preg_match('/^\d{4}$/', $foundedYear)) {
            $errors[] = 'Founded year must be a 4-digit year.';
        }

        $logo = null;
        try {
            $uploaded = Upload::image($this->file('logo'), 'businesses');
            if ($uploaded) {
                $logo = upload_url($uploaded);
            }
        } catch (\RuntimeException $e) {
            $errors[] = $e->getMessage();
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect($id ? "businesses/{$id}/edit" : 'businesses/create');
        }

        $data = [
            'name' => $name,
            'category' => $category !== '' ? $category : null,
            'business_type' => $businessType !== '' ? $businessType : null,
            'description' => $description !== '' ? $description : null,
            'website' => $website !== '' ? $website : null,
            'location' => $location !== '' ? $location : null,
            'founded_year' => $foundedYear !== '' ? (int) $foundedYear : null,
        ];
        if ($logo) {
            $data['logo'] = $logo;
        }

        if ($id === null) {
            $data['owner_id'] = Auth::id();
            Business::create($data);
            $this->flash('success', 'Business added.');
        } else {
            Business::update($id, $data);
            $this->flash('success', 'Business updated.');
        }

        $this->redirect('businesses/mine');
    }

    private function guard(): void
    {
        if (!Auth::check() || !Auth::user()) {
            Auth::logout();
            header('Location: ' . url('login'));
            exit;
        }
    }
}
