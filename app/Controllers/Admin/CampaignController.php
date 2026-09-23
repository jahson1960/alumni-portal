<?php

namespace App\Controllers\Admin;

use App\Core\Upload;
use App\Models\Campaign;

class CampaignController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.campaigns.index', [
            'title' => 'Giving Campaigns',
            'activeNav' => 'campaigns',
            'campaigns' => Campaign::all('created_at DESC'),
        ]);
    }

    public function create(): void
    {
        $this->view('admin.campaigns.form', [
            'title' => 'New Campaign',
            'activeNav' => 'campaigns',
            'campaign' => null,
        ]);
    }

    public function store(): void
    {
        $this->save(null);
    }

    public function edit(string $id): void
    {
        $campaign = Campaign::find((int) $id);
        if (!$campaign) {
            $this->flash('error', 'Campaign not found.');
            $this->redirect('admin/campaigns');
        }

        $this->view('admin.campaigns.form', [
            'title' => 'Edit Campaign',
            'activeNav' => 'campaigns',
            'campaign' => $campaign,
        ]);
    }

    public function update(string $id): void
    {
        $this->save((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        Campaign::delete((int) $id);
        $this->flash('success', 'Campaign deleted.');
        $this->redirect('admin/campaigns');
    }

    private function save(?int $id): void
    {
        $this->requireCsrf();

        $title = trim((string) $this->input('title', ''));
        $description = trim((string) $this->input('description', ''));
        $goalAmount = (float) $this->input('goal_amount', 0);
        $raisedAmount = (float) $this->input('raised_amount', 0);
        $status = $this->input('status', 'active') === 'closed' ? 'closed' : 'active';

        $errors = [];
        if ($title === '') {
            $errors[] = 'Title is required.';
        }

        $image = null;
        try {
            $uploaded = Upload::image($this->file('image'), 'campaigns');
            if ($uploaded) {
                $image = upload_url($uploaded);
            }
        } catch (\RuntimeException $e) {
            $errors[] = $e->getMessage();
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect($id ? "admin/campaigns/{$id}/edit" : 'admin/campaigns/create');
        }

        $data = [
            'title' => $title,
            'description' => $description,
            'goal_amount' => max(0, $goalAmount),
            'raised_amount' => max(0, $raisedAmount),
            'status' => $status,
        ];
        if ($image) {
            $data['image'] = $image;
        }

        if ($id === null) {
            Campaign::create($data);
            $this->flash('success', 'Campaign created.');
        } else {
            Campaign::update($id, $data);
            $this->flash('success', 'Campaign updated.');
        }

        $this->redirect('admin/campaigns');
    }
}
