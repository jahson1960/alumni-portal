<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Category;
use App\Models\Job;
use App\Models\JobAlert;

class JobAlertController extends Controller
{
    public function __construct()
    {
        if (!Auth::check() || !Auth::user()) {
            Auth::logout();
            header('Location: ' . url('login'));
            exit;
        }
    }

    /** Old standalone URL — the listing now lives on the Careers page's "Job Alerts" tab. */
    public function index(): void
    {
        $this->redirect('jobs?tab=alerts');
    }

    public function createForm(): void
    {
        $this->view('jobs.alerts.form', [
            'title' => 'Create Job Alert',
            'activeNav' => 'jobs',
            'alert' => null,
            'allCategories' => Category::forType('job'),
        ]);
    }

    public function store(): void
    {
        $this->requireCsrf();
        $this->save(null);
    }

    public function editForm(string $id): void
    {
        $alert = JobAlert::findOwnedBy((int) $id, (int) Auth::id());
        if (!$alert) {
            $this->flash('error', 'Alert not found.');
            $this->redirect('jobs?tab=alerts');
        }

        $this->view('jobs.alerts.form', [
            'title' => 'Edit Job Alert',
            'activeNav' => 'jobs',
            'alert' => $alert,
            'allCategories' => Category::forType('job'),
        ]);
    }

    public function update(string $id): void
    {
        $this->requireCsrf();
        $alert = JobAlert::findOwnedBy((int) $id, (int) Auth::id());
        if (!$alert) {
            $this->flash('error', 'Alert not found.');
            $this->redirect('jobs?tab=alerts');
        }
        $this->save((int) $id);
    }

    public function toggle(string $id): void
    {
        $this->requireCsrf();
        JobAlert::toggleActive((int) $id, (int) Auth::id());
        $this->redirectBack('jobs?tab=alerts');
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        $alert = JobAlert::findOwnedBy((int) $id, (int) Auth::id());
        if ($alert) {
            JobAlert::delete((int) $id);
            $this->flash('success', 'Alert deleted.');
        }
        $this->redirect('jobs?tab=alerts');
    }

    private function save(?int $id): void
    {
        $name = trim((string) $this->input('name', ''));
        $keywords = trim((string) $this->input('keywords', ''));
        $location = trim((string) $this->input('location', ''));
        $categoryId = $this->input('category_id') !== '' && $this->input('category_id') !== null ? (int) $this->input('category_id') : null;
        $jobType = in_array($this->input('job_type'), Job::JOB_TYPES, true) ? $this->input('job_type') : null;
        $workMode = array_key_exists($this->input('work_mode'), Job::WORK_MODES) ? $this->input('work_mode') : null;
        $frequency = $this->input('frequency') === 'weekly' ? 'weekly' : 'daily';

        if ($name === '') {
            $this->flash('error', 'Give the alert a name so you can recognize it later.');
            $this->redirect($id ? "jobs/alerts/{$id}/edit" : 'jobs/alerts/create');
        }

        $data = [
            'name' => $name,
            'keywords' => $keywords !== '' ? $keywords : null,
            'location' => $location !== '' ? $location : null,
            'category_id' => $categoryId,
            'job_type' => $jobType,
            'work_mode' => $workMode,
            'frequency' => $frequency,
        ];

        if ($id === null) {
            $data['user_id'] = Auth::id();
            JobAlert::create($data);
            $this->flash('success', 'Alert created.');
        } else {
            JobAlert::update($id, $data);
            $this->flash('success', 'Alert updated.');
        }

        $this->redirect('jobs?tab=alerts');
    }
}
