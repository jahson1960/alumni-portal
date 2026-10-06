<?php

namespace App\Core;

require_once __DIR__ . '/Mail/PHPMailer/Exception.php';
require_once __DIR__ . '/Mail/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/Mail/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    /**
     * Sends one email via the SMTP settings in config['mail']. Never throws — the caller
     * (the queue worker) just needs a pass/fail result to record, not an exception to handle.
     *
     * @return true|string true on success, or a human-readable error message on failure.
     */
    public static function send(string $toEmail, ?string $toName, string $subject, string $html): bool|string
    {
        $config = require __DIR__ . '/../../config/config.php';
        $mail = $config['mail'] ?? [];

        if (empty($mail['host']) || empty($mail['username'])) {
            return 'Mail is not configured (missing SMTP host/username in config.php).';
        }

        $mailer = new PHPMailer(true);
        try {
            $mailer->isSMTP();
            $mailer->Host = $mail['host'];
            $mailer->SMTPAuth = true;
            $mailer->Username = $mail['username'];
            $mailer->Password = $mail['password'] ?? '';
            $mailer->SMTPSecure = ($mail['encryption'] ?? 'tls') === 'ssl'
                ? PHPMailer::ENCRYPTION_SMTPS
                : PHPMailer::ENCRYPTION_STARTTLS;
            $mailer->Port = (int) ($mail['port'] ?? 587);

            $mailer->setFrom($mail['from_email'] ?? 'noreply@example.com', $mail['from_name'] ?? '');
            $mailer->addAddress($toEmail, $toName ?? '');

            $mailer->isHTML(true);
            $mailer->Subject = $subject;
            $mailer->Body = $html;
            $mailer->AltBody = trim(strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $html)));

            $mailer->send();
            return true;
        } catch (PHPMailerException $e) {
            return $mailer->ErrorInfo !== '' ? $mailer->ErrorInfo : $e->getMessage();
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }
}
