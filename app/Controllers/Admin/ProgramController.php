<?php

namespace App\Controllers\Admin;

use App\Models\Program;

class ProgramController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.programs.index', [
            'title' => 'Programs',
            'activeNav' => 'programs',
            'programs' => Program::all('name ASC'),
        ]);
    }

    public function store(): void
    {
        $this->requireCsrf();

        $name = trim((string) $this->input('name', ''));
        if ($name === '') {
            $this->flash('error', 'Program name is required.');
            $this->redirect('admin/programs');
        }

        try {
            Program::create($name);
            $this->flash('success', 'Program added.');
        } catch (\PDOException $e) {
            $this->flash('error', 'A program with that name already exists.');
        }

        $this->redirect('admin/programs');
    }

    public function edit(string $id): void
    {
        $program = Program::find((int) $id);
        if (!$program) {
            $this->flash('error', 'Program not found.');
            $this->redirect('admin/programs');
        }

        $this->view('admin.programs.edit', [
            'title' => 'Edit Program',
            'activeNav' => 'programs',
            'program' => $program,
        ]);
    }

    public function update(string $id): void
    {
        $this->requireCsrf();

        $name = trim((string) $this->input('name', ''));
        if ($name === '') {
            $this->flash('error', 'Program name is required.');
            $this->redirect("admin/programs/{$id}/edit");
        }

        try {
            Program::update((int) $id, $name);
            $this->flash('success', 'Program updated.');
        } catch (\PDOException $e) {
            $this->flash('error', 'A program with that name already exists.');
        }

        $this->redirect('admin/programs');
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        Program::delete((int) $id);
        $this->flash('success', 'Program deleted.');
        $this->redirect('admin/programs');
    }
}
