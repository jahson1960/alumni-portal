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

$rows = EmailQueue::pending(20);
if (!$rows) {
    echo "No pending emails.\n";
    exit(0);
}

foreach ($rows as $row) {
    $result = Mailer::send($row['to_email'], $row['to_name'], $row['subject'], $row['html_body']);
    if ($result === true) {
        EmailQueue::markSent((int) $row['id']);
        echo "Sent #{$row['id']} to {$row['to_email']}\n";
    } else {
        EmailQueue::markFailed((int) $row['id'], (string) $result);
        echo "Failed #{$row['id']} to {$row['to_email']}: {$result}\n";
    }
}
