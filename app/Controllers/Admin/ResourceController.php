<?php

namespace App\Controllers\Admin;

use App\Models\Resource;

class ResourceController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.resources.index', [
            'title' => 'Resources',
            'activeNav' => 'resources',
            'resources' => Resource::all('category ASC, title ASC'),
        ]);
    }

    public function create(): void
    {
        $this->view('admin.resources.form', [
            'title' => 'New Resource',
            'activeNav' => 'resources',
            'resource' => null,
        ]);
    }

    public function store(): void
    {
        $this->save(null);
    }

    public function edit(string $id): void
    {
        $resource = Resource::find((int) $id);
        if (!$resource) {
            $this->flash('error', 'Resource not found.');
            $this->redirect('admin/resources');
        }

        $this->view('admin.resources.form', [
            'title' => 'Edit Resource',
            'activeNav' => 'resources',
            'resource' => $resource,
        ]);
    }

    public function update(string $id): void
    {
        $this->save((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        Resource::delete((int) $id);
        $this->flash('success', 'Resource deleted.');
        $this->redirect('admin/resources');
    }

    private function save(?int $id): void
    {
        $this->requireCsrf();

        $title = trim((string) $this->input('title', ''));
        $description = trim((string) $this->input('description', ''));
        $link = trim((string) $this->input('link', ''));
        $category = trim((string) $this->input('category', 'Career'));
        $resourceType = in_array($this->input('resource_type'), \App\Models\Resource::TYPES, true) ? $this->input('resource_type') : 'Guide';
        $readTime = $this->input('read_time_minutes') !== '' && $this->input('read_time_minutes') !== null ? (int) $this->input('read_time_minutes') : null;
        $difficulty = in_array($this->input('difficulty'), ['beginner', 'intermediate', 'advanced'], true) ? $this->input('difficulty') : null;
        $isFeatured = $this->input('is_featured') ? 1 : 0;

        $errors = [];
        if ($title === '') {
            $errors[] = 'Title is required.';
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect($id ? "admin/resources/{$id}/edit" : 'admin/resources/create');
        }

        $data = [
            'title' => $title,
            'description' => $description,
            'link' => $link !== '' ? $link : '#',
            'category' => $category !== '' ? $category : 'Career',
            'resource_type' => $resourceType,
            'read_time_minutes' => $readTime,
            'difficulty' => $difficulty,
            'is_featured' => $isFeatured,
        ];

        if ($id === null) {
            Resource::create($data);
            $this->flash('success', 'Resource created.');
        } else {
            Resource::update($id, $data);
            $this->flash('success', 'Resource updated.');
        }

        $this->redirect('admin/resources');
    }
}
