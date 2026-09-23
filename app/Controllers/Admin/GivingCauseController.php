<?php

namespace App\Controllers\Admin;

use App\Models\GivingCause;

class GivingCauseController extends AdminController
{
    private const GROUPS = ['support', 'involve', 'impact'];

    public function index(): void
    {
        $this->view('admin.giving_causes.index', [
            'title' => 'Give Back — Causes',
            'activeNav' => 'giving_causes',
            'causes' => GivingCause::allOrdered(),
        ]);
    }

    public function create(): void
    {
        $this->view('admin.giving_causes.form', [
            'title' => 'New Cause',
            'activeNav' => 'giving_causes',
            'cause' => null,
        ]);
    }

    public function store(): void
    {
        $this->save(null);
    }

    public function edit(string $id): void
    {
        $cause = GivingCause::find((int) $id);
        if (!$cause) {
            $this->flash('error', 'Cause not found.');
            $this->redirect('admin/giving-causes');
        }

        $this->view('admin.giving_causes.form', [
            'title' => 'Edit Cause',
            'activeNav' => 'giving_causes',
            'cause' => $cause,
        ]);
    }

    public function update(string $id): void
    {
        $this->save((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        GivingCause::delete((int) $id);
        $this->flash('success', 'Cause deleted.');
        $this->redirect('admin/giving-causes');
    }

    private function save(?int $id): void
    {
        $this->requireCsrf();

        $group = in_array($this->input('column_group'), self::GROUPS, true) ? $this->input('column_group') : 'support';
        $title = trim((string) $this->input('title', ''));
        $slug = trim((string) $this->input('slug', ''));
        $slug = $slug !== '' ? $slug : strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
        $description = trim((string) $this->input('description', ''));
        $body = trim((string) $this->input('body', ''));
        $icon = trim((string) $this->input('icon', 'fa-solid fa-heart'));
        $sortOrder = (int) $this->input('sort_order', 0);

        $errors = [];
        if ($title === '') {
            $errors[] = 'Title is required.';
        }
        if ($slug === '') {
            $errors[] = 'Slug is required.';
        }

        $body = \App\Core\Sanitizer::html($body);

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect($id ? "admin/giving-causes/{$id}/edit" : 'admin/giving-causes/create');
        }

        $data = [
            'column_group' => $group,
            'slug' => $slug,
            'title' => $title,
            'description' => $description !== '' ? $description : null,
            'body' => $body !== '' ? $body : null,
            'icon' => $icon !== '' ? $icon : 'fa-solid fa-heart',
            'sort_order' => $sortOrder,
        ];

        if ($id === null) {
            $data['sort_order'] = $sortOrder ?: GivingCause::nextSortOrder($group);
            GivingCause::create($data);
            $this->flash('success', 'Cause created.');
        } else {
            GivingCause::update($id, $data);
            $this->flash('success', 'Cause updated.');
        }

        $this->redirect('admin/giving-causes');
    }
}
