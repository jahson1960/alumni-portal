<?php

namespace App\Controllers\Admin;

use App\Models\AlumniRoster;

class AlumniRosterController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.alumni_roster.index', [
            'title' => 'Alumni Roster',
            'activeNav' => 'alumni_roster',
            'rows' => AlumniRoster::all(),
        ]);
    }

    public function create(): void
    {
        $this->view('admin.alumni_roster.form', [
            'title' => 'New Roster Entry',
            'activeNav' => 'alumni_roster',
            'row' => null,
        ]);
    }

    public function store(): void
    {
        $this->save(null);
    }

    public function edit(string $id): void
    {
        $row = AlumniRoster::find((int) $id);
        if (!$row) {
            $this->flash('error', 'Roster entry not found.');
            $this->redirect('admin/alumni-roster');
        }

        $this->view('admin.alumni_roster.form', [
            'title' => 'Edit Roster Entry',
            'activeNav' => 'alumni_roster',
            'row' => $row,
        ]);
    }

    public function update(string $id): void
    {
        $this->save((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        AlumniRoster::delete((int) $id);
        $this->flash('success', 'Roster entry deleted.');
        $this->redirect('admin/alumni-roster');
    }

    private function save(?int $id): void
    {
        $this->requireCsrf();

        $matricNumber = trim((string) $this->input('matric_number', ''));
        $fullName = trim((string) $this->input('full_name', ''));
        $graduationYear = trim((string) $this->input('graduation_year', ''));
        $cohort = trim((string) $this->input('cohort', ''));

        $errors = [];
        if ($matricNumber === '') {
            $errors[] = 'A matric number is required.';
        }
        if ($graduationYear === '' || !ctype_digit($graduationYear)) {
            $errors[] = 'A valid graduation year is required.';
        }
        if ($cohort === '') {
            $errors[] = 'A cohort is required.';
        }

        if (!$errors) {
            $existing = AlumniRoster::findByMatric($matricNumber);
            if ($existing && (int) $existing['id'] !== $id) {
                $errors[] = 'That matric number is already on the roster.';
            }
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect($id ? "admin/alumni-roster/{$id}/edit" : 'admin/alumni-roster/create');
        }

        $data = [
            'matric_number' => $matricNumber,
            'full_name' => $fullName !== '' ? $fullName : null,
            'graduation_year' => (int) $graduationYear,
            'cohort' => $cohort,
        ];

        if ($id === null) {
            AlumniRoster::create($data);
            $this->flash('success', 'Roster entry added.');
        } else {
            AlumniRoster::update($id, $data);
            $this->flash('success', 'Roster entry updated.');
        }

        $this->redirect('admin/alumni-roster');
    }
}
