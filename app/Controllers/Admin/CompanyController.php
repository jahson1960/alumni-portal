<?php

namespace App\Controllers\Admin;

use App\Core\Sanitizer;
use App\Core\Upload;
use App\Models\Company;

class CompanyController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.companies.index', [
            'title' => 'Companies',
            'activeNav' => 'companies',
            'companies' => Company::all(),
        ]);
    }

    public function create(): void
    {
        $this->view('admin.companies.form', [
            'title' => 'New Company',
            'activeNav' => 'companies',
            'company' => null,
        ]);
    }

    public function store(): void
    {
        $this->save(null);
    }

    public function edit(string $id): void
    {
        $company = Company::find((int) $id);
        if (!$company) {
            $this->flash('error', 'Company not found.');
            $this->redirect('admin/companies');
        }

        $this->view('admin.companies.form', [
            'title' => 'Edit Company',
            'activeNav' => 'companies',
            'company' => $company,
        ]);
    }

    public function update(string $id): void
    {
        $this->save((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        Company::delete((int) $id);
        $this->flash('success', 'Company deleted.');
        $this->redirect('admin/companies');
    }

    private function save(?int $id): void
    {
        $this->requireCsrf();

        $name = trim((string) $this->input('name', ''));
        $industry = trim((string) $this->input('industry', ''));
        $about = Sanitizer::html(trim((string) $this->input('about', '')));
        $website = trim((string) $this->input('website', ''));
        $location = trim((string) $this->input('location', ''));

        $errors = [];
        if ($name === '') {
            $errors[] = 'Company name is required.';
        }

        $logo = null;
        try {
            $uploaded = Upload::image($this->file('logo'), 'companies');
            if ($uploaded) {
                $logo = upload_url($uploaded);
            }
        } catch (\RuntimeException $e) {
            $errors[] = $e->getMessage();
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect($id ? "admin/companies/{$id}/edit" : 'admin/companies/create');
        }

        $data = [
            'name' => $name,
            'industry' => $industry !== '' ? $industry : null,
            'about' => $about,
            'website' => $website !== '' ? $website : null,
            'location' => $location !== '' ? $location : null,
        ];
        if ($logo) {
            $data['logo'] = $logo;
        }

        if ($id === null) {
            Company::create($data);
            $this->flash('success', 'Company added.');
        } else {
            Company::update($id, $data);
            $this->flash('success', 'Company updated.');
        }

        $this->redirect('admin/companies');
    }
}
