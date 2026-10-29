<?php

namespace App\Controllers\Admin;

use App\Models\Advisor;
use App\Models\AdvisorNote;
use App\Models\Category;
use App\Models\EmailQueue;
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
        if (!$alum || !in_array($alum['role'], ['alumni', 'editor', 'recruiter', 'advisor'], true)) {
            $this->flash('error', 'Alumni not found.');
            $this->redirect('admin/alumni');
        }

        $isActualAlumni = $alum['role'] === 'alumni';

        $this->view('admin.alumni.show', [
            'title' => $alum['name'],
            'activeNav' => 'alumni',
            'alum' => $alum,
            'allCategories' => Category::forType('job'),
            'selectedIds' => Category::idsForUser((int) $id),
            'allAdvisors' => $isActualAlumni ? Advisor::allAdvisors() : [],
            'assignedAdvisorIds' => $isActualAlumni ? Advisor::advisorIdsForAlumni((int) $id) : [],
            'advisorNotes' => $isActualAlumni ? AdvisorNote::forAlumni((int) $id) : [],
        ]);
    }

    public function updateJobCategories(string $id): void
    {
        $this->requireCsrf();
        $alum = User::find((int) $id);
        if (!$alum || !in_array($alum['role'], ['alumni', 'editor', 'recruiter', 'advisor'], true)) {
            $this->flash('error', 'Alumni not found.');
            $this->redirect('admin/alumni');
        }

        $jobCategoryIds = array_map('intval', (array) $this->input('job_categories', []));
        if (!$jobCategoryIds) {
            $this->flash('error', 'Please select at least one job category.');
            $this->redirect("admin/alumni/{$id}");
        }

        Category::syncUserJobCategories((int) $id, $jobCategoryIds);
        $this->flash('success', 'Job categories updated.');
        $this->redirect("admin/alumni/{$id}");
    }

    /** Admin-only: syncs which advisor(s) are assigned to an alumnus. */
    public function updateAdvisors(string $id): void
    {
        $this->requireCsrf();
        $alum = User::find((int) $id);
        if (!$alum || $alum['role'] !== 'alumni') {
            $this->flash('error', 'Alumni not found.');
            $this->redirect('admin/alumni');
        }

        $advisorIds = array_map('intval', (array) $this->input('advisor_ids', []));
        Advisor::syncAdvisorsForAlumni((int) $id, $advisorIds);
        $this->flash('success', 'Assigned advisors updated.');
        $this->redirect("admin/alumni/{$id}");
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

    /** Generates a new temporary password, forces a change on next login, and emails it to the alum. */
    public function resetPassword(string $id): void
    {
        $this->requireCsrf();
        $alum = User::find((int) $id);
        if (!$alum || !in_array($alum['role'], ['alumni', 'editor', 'recruiter', 'advisor'], true)) {
            $this->flash('error', 'Alumni not found.');
            $this->redirect('admin/alumni');
        }

        $tempPassword = generate_temp_password();
        User::update((int) $id, [
            'password_hash' => password_hash($tempPassword, PASSWORD_DEFAULT),
            'must_change_password' => 1,
        ]);

        EmailQueue::enqueue($alum['email'], $alum['name'], 'Your Alumni Portal Password Has Been Reset', render_email(
            'Password Reset',
            credential_email_body(
                $alum['first_name'] ?: $alum['name'],
                $alum['email'],
                $tempPassword,
                'An administrator has reset your password on the Rome Business School Nigeria Alumni Portal.'
            ),
            'Log In Now',
            'login'
        ));

        $this->flash('success', 'Password reset. A temporary password has been emailed to the alumnus.');
        $this->redirect("admin/alumni/{$id}");
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
