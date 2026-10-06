<?php

namespace App\Controllers\Admin;

use App\Models\EmailQueue;

class EmailQueueController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.email_queue.index', [
            'title' => 'Email Queue',
            'activeNav' => 'email_queue',
            'emails' => EmailQueue::recent(),
        ]);
    }

    public function retry(string $id): void
    {
        $this->requireCsrf();
        EmailQueue::retry((int) $id);
        $this->flash('success', 'Email moved back to the pending queue.');
        $this->redirect('admin/email-queue');
    }
}
