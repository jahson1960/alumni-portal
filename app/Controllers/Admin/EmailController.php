<?php

namespace App\Controllers\Admin;

use App\Models\EmailQueue;
use App\Models\Setting;

/**
 * Everything email-related for admins, in one tabbed page: the outbound queue (paginated),
 * delivery/throttling settings, and the read-only SMTP connection details from config.php.
 */
class EmailController extends AdminController
{
    private const TABS = ['queue', 'settings', 'connection'];

    public function index(): void
    {
        $tab = in_array($this->input('tab'), self::TABS, true) ? $this->input('tab') : 'queue';

        $data = ['title' => 'Emails', 'activeNav' => 'emails', 'tab' => $tab];
        $data += match ($tab) {
            'settings' => ['settings' => Setting::all()],
            'connection' => ['mailConfig' => $this->mailConfig()],
            default => $this->queueTabData(),
        };

        $this->view('admin.emails.index', $data);
    }

    private function queueTabData(): array
    {
        $p = $this->paginate(EmailQueue::countAll(), 30);
        return [
            'emails' => EmailQueue::paginated($p['perPage'], $p['offset']),
            ...$p,
        ];
    }

    /** Masks the SMTP password - this page only displays config.php's mail settings, it can't edit them. */
    private function mailConfig(): array
    {
        $config = require __DIR__ . '/../../../config/config.php';
        $mail = $config['mail'] ?? [];
        $mail['password'] = !empty($mail['password']) ? str_repeat('•', 12) : '';
        return $mail;
    }

    public function updateSettings(): void
    {
        $this->requireCsrf();

        $errors = [];
        $data = [];

        foreach ([
            'admin_email_header_bg' => '#091a2e',
            'admin_email_header_text' => '#ffffff',
            'admin_email_button_color' => '#d49326',
        ] as $key => $default) {
            $data[$key] = valid_hex_color($this->input($key), $default);
        }

        $emailBatchSize = (int) $this->input('email_batch_size', 20);
        $data['email_batch_size'] = (string) max(1, min(100, $emailBatchSize ?: 20));
        $emailBatchInterval = (int) $this->input('email_batch_interval_minutes', 2);
        $data['email_batch_interval_minutes'] = (string) max(0, min(120, $emailBatchInterval));

        $broadcastBcc = trim((string) $this->input('broadcast_bcc_email', ''));
        if ($broadcastBcc !== '' && !filter_var($broadcastBcc, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Broadcast BCC must be a valid email address.';
        }
        $data['broadcast_bcc_email'] = $broadcastBcc;

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect('admin/emails?tab=settings');
        }

        Setting::setMany($data);
        $this->flash('success', 'Email settings updated.');
        $this->redirect('admin/emails?tab=settings');
    }

    public function retry(string $id): void
    {
        $this->requireCsrf();
        EmailQueue::retry((int) $id);
        $this->flash('success', 'Email moved back to the pending queue.');
        $this->redirect('admin/emails');
    }
}
