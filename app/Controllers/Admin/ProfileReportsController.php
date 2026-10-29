<?php

namespace App\Controllers\Admin;

use App\Models\ProfileReport;

class ProfileReportsController extends AdminController
{
    public function index(): void
    {
        $p = $this->paginate(ProfileReport::countAll(), 30);
        $this->view('admin.profile_reports.index', [
            'title' => 'Profile Reports',
            'activeNav' => 'profile_reports',
            'reports' => ProfileReport::paginateWithUsers($p['perPage'], $p['offset']),
            ...$p,
        ]);
    }

    public function resolve(string $id): void
    {
        $this->requireCsrf();
        ProfileReport::markResolved((int) $id);
        $this->flash('success', 'Report marked resolved.');
        $this->redirect('admin/profile-reports');
    }

    public function dismiss(string $id): void
    {
        $this->requireCsrf();
        ProfileReport::markDismissed((int) $id);
        $this->flash('success', 'Report dismissed.');
        $this->redirect('admin/profile-reports');
    }
}
