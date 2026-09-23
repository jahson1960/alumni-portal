<?php

namespace App\Models;

use App\Core\Model;

class JobApplication extends Model
{
    protected static string $table = 'job_applications';

    public static function hasApplied(int $userId, int $jobId): bool
    {
        $stmt = static::db()->prepare('SELECT 1 FROM job_applications WHERE user_id = ? AND job_id = ?');
        $stmt->execute([$userId, $jobId]);
        return (bool) $stmt->fetchColumn();
    }

    /** All job_id values the user has applied to, for cheap in-memory lookups on a listing page. */
    public static function appliedIdsFor(int $userId): array
    {
        $stmt = static::db()->prepare('SELECT job_id FROM job_applications WHERE user_id = ?');
        $stmt->execute([$userId]);
        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    /** Records the application if not already recorded. Idempotent — a second "Apply" click doesn't error. */
    public static function record(int $userId, int $jobId): void
    {
        if (self::hasApplied($userId, $jobId)) {
            return;
        }
        $stmt = static::db()->prepare('INSERT INTO job_applications (user_id, job_id) VALUES (?, ?)');
        $stmt->execute([$userId, $jobId]);
    }
}
