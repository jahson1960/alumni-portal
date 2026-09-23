<?php

namespace App\Controllers\Admin;

use App\Models\Benefit;

class BenefitController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.benefits.index', [
            'title' => 'Benefits',
            'activeNav' => 'benefits',
            'benefits' => Benefit::all('category ASC, title ASC'),
        ]);
    }

    public function create(): void
    {
        $this->view('admin.benefits.form', [
            'title' => 'New Benefit',
            'activeNav' => 'benefits',
            'benefit' => null,
        ]);
    }

    public function store(): void
    {
        $this->save(null);
    }

    public function edit(string $id): void
    {
        $benefit = Benefit::find((int) $id);
        if (!$benefit) {
            $this->flash('error', 'Benefit not found.');
            $this->redirect('admin/benefits');
        }

        $this->view('admin.benefits.form', [
            'title' => 'Edit Benefit',
            'activeNav' => 'benefits',
            'benefit' => $benefit,
        ]);
    }

    public function update(string $id): void
    {
        $this->save((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        Benefit::delete((int) $id);
        $this->flash('success', 'Benefit deleted.');
        $this->redirect('admin/benefits');
    }

    private function save(?int $id): void
    {
        $this->requireCsrf();

        $title = trim((string) $this->input('title', ''));
        $description = trim((string) $this->input('description', ''));
        $link = trim((string) $this->input('link', ''));
        $category = trim((string) $this->input('category', 'General'));

        $errors = [];
        if ($title === '') {
            $errors[] = 'Title is required.';
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect($id ? "admin/benefits/{$id}/edit" : 'admin/benefits/create');
        }

        $data = [
            'title' => $title,
            'description' => $description,
            'link' => $link !== '' ? $link : '#',
            'category' => $category !== '' ? $category : 'General',
        ];

        if ($id === null) {
            Benefit::create($data);
            $this->flash('success', 'Benefit created.');
        } else {
            Benefit::update($id, $data);
            $this->flash('success', 'Benefit updated.');
        }

        $this->redirect('admin/benefits');
    }
}
