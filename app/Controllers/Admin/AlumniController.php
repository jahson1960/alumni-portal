<?php

namespace App\Controllers\Admin;

use App\Models\User;

class AlumniController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.alumni.index', [
            'title' => 'Alumni',
            'activeNav' => 'alumni',
            'alumni' => User::allAlumniAdmin(),
        ]);
    }

    public function show(string $id): void
    {
        $alum = User::find((int) $id);
        if (!$alum || !in_array($alum['role'], ['alumni', 'editor'], true)) {
            $this->flash('error', 'Alumni not found.');
            $this->redirect('admin/alumni');
        }

        $this->view('admin.alumni.show', [
            'title' => $alum['name'],
            'activeNav' => 'alumni',
            'alum' => $alum,
        ]);
    }

    public function suspend(string $id): void
    {
        $this->requireCsrf();
        User::update((int) $id, ['status' => 'suspended']);
        $this->flash('success', 'Alumni account suspended.');
        $this->redirect('admin/alumni');
    }

    public function activate(string $id): void
    {
        $this->requireCsrf();
        User::update((int) $id, ['status' => 'active']);
        $this->flash('success', 'Alumni account activated.');
        $this->redirect('admin/alumni');
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        User::delete((int) $id);
        $this->flash('success', 'Alumni account deleted.');
        $this->redirect('admin/alumni');
    }

    public function spotlight(string $id): void
    {
        $this->requireCsrf();
        User::update((int) $id, [
            'is_spotlighted' => 1,
            'spotlight_note' => trim((string) $this->input('spotlight_note', '')) ?: null,
        ]);
        $this->flash('success', 'Alumni added to Spotlight.');
        $this->redirect("admin/alumni/{$id}");
    }

    public function unspotlight(string $id): void
    {
        $this->requireCsrf();
        User::update((int) $id, ['is_spotlighted' => 0]);
        $this->flash('success', 'Alumni removed from Spotlight.');
        $this->redirect("admin/alumni/{$id}");
    }

    /** Toggles a user between 'alumni' and 'editor' — never touches 'admin', so this can't be used to self-escalate. */
    public function setRole(string $id): void
    {
        $this->requireCsrf();
        $alum = User::find((int) $id);
        if ($alum && in_array($alum['role'], ['alumni', 'editor'], true)) {
            $newRole = $alum['role'] === 'editor' ? 'alumni' : 'editor';
            User::update((int) $id, ['role' => $newRole]);
            $this->flash('success', $newRole === 'editor' ? 'Alumni is now an editor.' : 'Editor access removed.');
        }
        $this->redirect("admin/alumni/{$id}");
    }
}
