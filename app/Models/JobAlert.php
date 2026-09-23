<?php

namespace App\Models;

use App\Core\Model;

class JobAlert extends Model
{
    protected static string $table = 'job_alerts';

    public static function forUser(int $userId): array
    {
        $stmt = static::db()->prepare(
            "SELECT a.*, c.name AS category_name FROM job_alerts a
             LEFT JOIN categories c ON c.id = a.category_id
             WHERE a.user_id = ? ORDER BY a.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function findOwnedBy(int $id, int $userId): array|false
    {
        $stmt = static::db()->prepare('SELECT * FROM job_alerts WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }

    public static function create(array $data): int
    {
        return static::insertRow('job_alerts', $data);
    }

    public static function update(int $id, array $data): bool
    {
        return static::updateRow('job_alerts', $id, $data);
    }

    public static function delete(int $id): bool
    {
        $stmt = static::db()->prepare('DELETE FROM job_alerts WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public static function toggleActive(int $id, int $userId): bool
    {
        $stmt = static::db()->prepare('UPDATE job_alerts SET is_active = NOT is_active WHERE id = ? AND user_id = ?');
        return $stmt->execute([$id, $userId]);
    }

    /** Converts a stored alert row into a Job::search()-compatible filters array. */
    public static function toJobFilters(array $alert): array
    {
        $filters = [];
        if (!empty($alert['keywords'])) {
            $filters['q'] = $alert['keywords'];
        }
        if (!empty($alert['location'])) {
            $filters['location'] = $alert['location'];
        }
        if (!empty($alert['category_id'])) {
            $filters['category'] = Category::slugForId((int) $alert['category_id']);
        }
        if (!empty($alert['job_type'])) {
            $filters['job_type'] = [$alert['job_type']];
        }
        if (!empty($alert['work_mode'])) {
            $filters['work_mode'] = [$alert['work_mode']];
        }
        return $filters;
    }
}
