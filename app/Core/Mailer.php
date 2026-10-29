<?php

namespace App\Core;

require_once __DIR__ . '/Mail/PHPMailer/Exception.php';
require_once __DIR__ . '/Mail/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/Mail/PHPMailer/SMTP.php';

use App\Models\Setting;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    /**
     * The SMTP settings actually in effect: config.php's ['mail'] array, with any admin-edited
     * value from the settings table (keys smtp_host, smtp_port, ...) overriding it field by
     * field. A blank admin-edited field means "no override" and falls back to config.php, so
     * a partially-filled-in admin form still works.
     */
    public static function effectiveConfig(): array
    {
        $config = require __DIR__ . '/../../config/config.php';
        $mail = $config['mail'] ?? [];

        foreach (['host', 'port', 'encryption', 'username', 'password', 'from_email', 'from_name'] as $key) {
            $override = Setting::get('smtp_' . $key, '');
            if ($override !== '') {
                $mail[$key] = $override;
            }
        }

        return $mail;
    }

    /**
     * Sends one email via the effective SMTP settings (see effectiveConfig()). Never throws —
     * the caller (the queue worker) just needs a pass/fail result to record, not an exception
     * to handle.
     *
     * @return true|string true on success, or a human-readable error message on failure.
     */
    public static function send(string $toEmail, ?string $toName, string $subject, string $html, ?string $bcc = null): bool|string
    {
        $mail = self::effectiveConfig();

        if (empty($mail['host']) || empty($mail['username'])) {
            return 'Mail is not configured (missing SMTP host/username — set it in Admin > Emails > SMTP Connection).';
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
            if ($bcc !== null && $bcc !== '') {
                $mailer->addBCC($bcc);
            }

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
