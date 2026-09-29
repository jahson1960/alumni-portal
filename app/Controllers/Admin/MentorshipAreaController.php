<?php

namespace App\Controllers\Admin;

use App\Models\MentorshipArea;

class MentorshipAreaController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.mentorship_areas.index', [
            'title' => 'Mentorship Areas',
            'activeNav' => 'mentorship_areas',
            'areas' => MentorshipArea::all('name ASC'),
        ]);
    }

    public function store(): void
    {
        $this->requireCsrf();

        $name = trim((string) $this->input('name', ''));
        if ($name === '') {
            $this->flash('error', 'Area name is required.');
            $this->redirect('admin/mentorship-areas');
        }

        try {
            MentorshipArea::create($name);
            $this->flash('success', 'Mentorship area added.');
        } catch (\PDOException $e) {
            $this->flash('error', 'A mentorship area with that name already exists.');
        }

        $this->redirect('admin/mentorship-areas');
    }

    public function edit(string $id): void
    {
        $area = MentorshipArea::find((int) $id);
        if (!$area) {
            $this->flash('error', 'Mentorship area not found.');
            $this->redirect('admin/mentorship-areas');
        }

        $this->view('admin.mentorship_areas.edit', [
            'title' => 'Edit Mentorship Area',
            'activeNav' => 'mentorship_areas',
            'area' => $area,
        ]);
    }

    public function update(string $id): void
    {
        $this->requireCsrf();

        $name = trim((string) $this->input('name', ''));
        if ($name === '') {
            $this->flash('error', 'Area name is required.');
            $this->redirect("admin/mentorship-areas/{$id}/edit");
        }

        try {
            MentorshipArea::update((int) $id, $name);
            $this->flash('success', 'Mentorship area updated.');
        } catch (\PDOException $e) {
            $this->flash('error', 'A mentorship area with that name already exists.');
        }

        $this->redirect('admin/mentorship-areas');
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        MentorshipArea::delete((int) $id);
        $this->flash('success', 'Mentorship area deleted.');
        $this->redirect('admin/mentorship-areas');
    }
}
