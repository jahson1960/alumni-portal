<?php

namespace App\Models;

use App\Core\Model;

class SavedJob extends Model
{
    protected static string $table = 'saved_jobs';

    public static function isSaved(int $userId, int $jobId): bool
    {
        $stmt = static::db()->prepare('SELECT 1 FROM saved_jobs WHERE user_id = ? AND job_id = ?');
        $stmt->execute([$userId, $jobId]);
        return (bool) $stmt->fetchColumn();
    }

    /** Toggles saved state, returns the new state (true = now saved). */
    public static function toggle(int $userId, int $jobId): bool
    {
        if (self::isSaved($userId, $jobId)) {
            $stmt = static::db()->prepare('DELETE FROM saved_jobs WHERE user_id = ? AND job_id = ?');
            $stmt->execute([$userId, $jobId]);
            return false;
        }
        $stmt = static::db()->prepare('INSERT INTO saved_jobs (user_id, job_id) VALUES (?, ?)');
        $stmt->execute([$userId, $jobId]);
        return true;
    }
}
