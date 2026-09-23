<?php

namespace App\Models;

use App\Core\Model;

class SavedAlumni extends Model
{
    protected static string $table = 'saved_alumni';

    public static function isSaved(int $userId, int $alumniId): bool
    {
        $stmt = static::db()->prepare('SELECT 1 FROM saved_alumni WHERE user_id = ? AND alumni_id = ?');
        $stmt->execute([$userId, $alumniId]);
        return (bool) $stmt->fetchColumn();
    }

    /** Toggles saved state, returns the new state (true = now saved). */
    public static function toggle(int $userId, int $alumniId): bool
    {
        if ($userId === $alumniId) {
            return false;
        }
        if (self::isSaved($userId, $alumniId)) {
            $stmt = static::db()->prepare('DELETE FROM saved_alumni WHERE user_id = ? AND alumni_id = ?');
            $stmt->execute([$userId, $alumniId]);
            return false;
        }
        $stmt = static::db()->prepare('INSERT INTO saved_alumni (user_id, alumni_id) VALUES (?, ?)');
        $stmt->execute([$userId, $alumniId]);
        return true;
    }

    /** All alumni_ids the user has saved, for a single bulk lookup instead of N+1 queries per directory card. */
    public static function savedIdsFor(int $userId): array
    {
        $stmt = static::db()->prepare('SELECT alumni_id FROM saved_alumni WHERE user_id = ?');
        $stmt->execute([$userId]);
        return array_map('intval', array_column($stmt->fetchAll(), 'alumni_id'));
    }
}
