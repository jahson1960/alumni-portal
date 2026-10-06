<?php

namespace App\Models;

use App\Core\Model;

class EmailQueue extends Model
{
    protected static string $table = 'email_queue';

    private const MAX_ATTEMPTS = 5;

    public static function enqueue(string $toEmail, ?string $toName, string $subject, string $html): int
    {
        return static::insertRow('email_queue', [
            'to_email' => $toEmail,
            'to_name' => $toName,
            'subject' => $subject,
            'html_body' => $html,
        ]);
    }

    public static function pending(int $limit = 20): array
    {
        $stmt = static::db()->prepare('SELECT * FROM email_queue WHERE status = ? ORDER BY created_at ASC LIMIT ' . max(1, $limit));
        $stmt->execute(['pending']);
        return $stmt->fetchAll();
    }

    public static function markSent(int $id): void
    {
        $stmt = static::db()->prepare("UPDATE email_queue SET status = 'sent', sent_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function markFailed(int $id, string $error): void
    {
        // MySQL evaluates column self-references left-to-right within one UPDATE, so by the time
        // the CASE below runs, `attempts` already holds the incremented value set just above —
        // comparing it to MAX_ATTEMPTS directly (no extra +1) is what makes this fire on the 5th attempt.
        $stmt = static::db()->prepare(
            "UPDATE email_queue
             SET attempts = attempts + 1,
                 last_error = ?,
                 status = CASE WHEN attempts >= ? THEN 'failed' ELSE 'pending' END
             WHERE id = ?"
        );
        $stmt->execute([$error, self::MAX_ATTEMPTS, $id]);
    }

    /** Admin "Retry" action: put a failed row back into the pending queue. */
    public static function retry(int $id): void
    {
        $stmt = static::db()->prepare("UPDATE email_queue SET status = 'pending', attempts = 0, last_error = NULL WHERE id = ? AND status = 'failed'");
        $stmt->execute([$id]);
    }

    public static function recent(int $limit = 100): array
    {
        return static::db()->query('SELECT * FROM email_queue ORDER BY created_at DESC LIMIT ' . max(1, $limit))->fetchAll();
    }
}
