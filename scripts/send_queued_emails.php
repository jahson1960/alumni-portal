<?php

/**
 * CLI-only worker: sends pending rows from email_queue via SMTP. Never web-reachable —
 * blocked by the root .htaccess alongside app/config/database/routes. Intended to run on a
 * short interval via cron, e.g.:
 *   * * * * * php /path/to/scripts/send_queued_emails.php
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Forbidden');
}

define('ROOT_PATH', dirname(__DIR__));

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = ROOT_PATH . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

require ROOT_PATH . '/app/Core/helpers.php';

use App\Core\Mailer;
use App\Models\EmailQueue;
use App\Models\Setting;

// Admin-configurable send rate (Admin > Settings > Email Delivery Rate), so a large broadcast
// doesn't fire off dozens of emails in the same minute and trip a mail provider's spam filters.
// This worker is still invoked every minute by cron, but only actually sends a batch once the
// configured interval has elapsed since the last one - tracked via a plain settings row rather
// than requiring the cron schedule itself to match the configured interval.
$batchSize = max(1, (int) Setting::get('email_batch_size', '20'));
$intervalMinutes = max(0, (int) Setting::get('email_batch_interval_minutes', '2'));

if ($intervalMinutes > 0) {
    $lastSentAt = Setting::get('email_last_batch_sent_at', '');
    if ($lastSentAt !== '') {
        $elapsedSeconds = time() - strtotime($lastSentAt);
        $waitSeconds = $intervalMinutes * 60 - $elapsedSeconds;
        if ($waitSeconds > 0) {
            echo "Rate limit: next batch in {$waitSeconds}s.\n";
            exit(0);
        }
    }
}

$rows = EmailQueue::pending($batchSize);
if (!$rows) {
    echo "No pending emails.\n";
    exit(0);
}

foreach ($rows as $row) {
    $result = Mailer::send($row['to_email'], $row['to_name'], $row['subject'], $row['html_body'], $row['bcc_email'] ?? null);
    if ($result === true) {
        EmailQueue::markSent((int) $row['id']);
        echo "Sent #{$row['id']} to {$row['to_email']}\n";
    } else {
        EmailQueue::markFailed((int) $row['id'], (string) $result);
        echo "Failed #{$row['id']} to {$row['to_email']}: {$result}\n";
    }
}

Setting::set('email_last_batch_sent_at', date('Y-m-d H:i:s'));
