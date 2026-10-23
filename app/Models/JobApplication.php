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

    public static function findForUserJob(int $userId, int $jobId): array|false
    {
        $stmt = static::db()->prepare('SELECT * FROM job_applications WHERE user_id = ? AND job_id = ?');
        $stmt->execute([$userId, $jobId]);
        return $stmt->fetch();
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

    /**
     * Stores (or, on a resubmit, overwrites) the submitted in-app application form answers.
     * $responses is a plain array of ['label' => ..., 'type' => ..., 'value' => ...] - a
     * snapshot taken at submission time, independent of the template's fields afterward, so
     * a poster editing their template later never garbles an already-submitted application.
     */
    public static function submitForm(int $userId, int $jobId, array $responses): void
    {
        $stmt = static::db()->prepare(
            'INSERT INTO job_applications (user_id, job_id, responses, status) VALUES (?, ?, ?, "new")
             ON DUPLICATE KEY UPDATE responses = VALUES(responses), status = "new"'
        );
        $stmt->execute([$userId, $jobId, json_encode($responses)]);
    }

    /** Applicants for a job, newest first, with the applicant's own profile fields joined in for the poster's Applicants view. */
    public static function forJob(int $jobId): array
    {
        $stmt = static::db()->prepare(
            'SELECT ja.*, u.name AS applicant_name, u.email AS applicant_email, u.avatar AS applicant_avatar, u.headline AS applicant_headline
             FROM job_applications ja
             JOIN users u ON u.id = ja.user_id
             WHERE ja.job_id = ?
             ORDER BY ja.created_at DESC'
        );
        $stmt->execute([$jobId]);
        return $stmt->fetchAll();
    }

    /** job_id => applicant count, for cheap bulk display on "My Job Posts". */
    public static function countsForJobs(array $jobIds): array
    {
        if (!$jobIds) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($jobIds), '?'));
        $stmt = static::db()->prepare("SELECT job_id, COUNT(*) AS c FROM job_applications WHERE job_id IN ($placeholders) GROUP BY job_id");
        $stmt->execute($jobIds);
        $counts = [];
        foreach ($stmt->fetchAll() as $row) {
            $counts[(int) $row['job_id']] = (int) $row['c'];
        }
        return $counts;
    }

    public static function updateStatus(int $id, string $status): bool
    {
        $stmt = static::db()->prepare('UPDATE job_applications SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }
}
