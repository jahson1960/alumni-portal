<?php

namespace App\Controllers\Admin;

use App\Core\Mailer;
use App\Models\EmailQueue;
use App\Models\Setting;

/**
 * Everything email-related for admins, in one tabbed page: the outbound queue (paginated),
 * delivery/throttling settings, and the SMTP connection itself.
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

    /**
     * The effective SMTP config (config.php, overridden field-by-field by anything saved from
     * this page). The real password is never sent to the browser - just whether one is set.
     */
    private function mailConfig(): array
    {
        $mail = Mailer::effectiveConfig();
        $mail['password_set'] = !empty($mail['password']);
        $mail['password'] = '';
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

    /**
     * Saves SMTP connection overrides. Each field is stored as its own setting (smtp_host,
     * smtp_port, ...); leaving a field blank clears that override and falls back to
     * config.php's value for it (see Mailer::effectiveConfig()). The password field is the one
     * exception - it's never pre-filled with the real value, so a blank submission means
     * "leave the current password alone", not "clear it".
     */
    public function updateConnection(): void
    {
        $this->requireCsrf();

        $errors = [];

        $host = trim((string) $this->input('smtp_host', ''));
        $port = trim((string) $this->input('smtp_port', ''));
        if ($port !== '' && (!ctype_digit($port) || (int) $port < 1 || (int) $port > 65535)) {
            $errors[] = 'SMTP port must be a number between 1 and 65535.';
        }
        $encryption = $this->input('smtp_encryption', '') === 'ssl' ? 'ssl' : ($this->input('smtp_encryption', '') === 'tls' ? 'tls' : '');
        $username = trim((string) $this->input('smtp_username', ''));
        $fromEmail = trim((string) $this->input('smtp_from_email', ''));
        if ($fromEmail !== '' && !filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'From Email must be a valid email address.';
        }
        $fromName = trim((string) $this->input('smtp_from_name', ''));

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect('admin/emails?tab=connection');
        }

        Setting::setMany([
            'smtp_host' => $host,
            'smtp_port' => $port,
            'smtp_encryption' => $encryption,
            'smtp_username' => $username,
            'smtp_from_email' => $fromEmail,
            'smtp_from_name' => $fromName,
        ]);

        $password = (string) $this->input('smtp_password', '');
        if ($password !== '') {
            Setting::set('smtp_password', $password);
        }

        $this->flash('success', 'SMTP connection updated.');
        $this->redirect('admin/emails?tab=connection');
    }

    public function retry(string $id): void
    {
        $this->requireCsrf();
        EmailQueue::retry((int) $id);
        $this->flash('success', 'Email moved back to the pending queue.');
        $this->redirect('admin/emails');
    }
}
