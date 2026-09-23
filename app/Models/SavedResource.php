<?php

namespace App\Models;

use App\Core\Model;

class SavedResource extends Model
{
    protected static string $table = 'saved_resources';

    public static function isSaved(int $userId, int $resourceId): bool
    {
        $stmt = static::db()->prepare('SELECT 1 FROM saved_resources WHERE user_id = ? AND resource_id = ?');
        $stmt->execute([$userId, $resourceId]);
        return (bool) $stmt->fetchColumn();
    }

    /** All resource_id values the user has saved, for cheap in-memory lookups on a listing page. */
    public static function savedIdsFor(int $userId): array
    {
        $stmt = static::db()->prepare('SELECT resource_id FROM saved_resources WHERE user_id = ?');
        $stmt->execute([$userId]);
        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    /** Toggles saved state, returns the new state (true = now saved). */
    public static function toggle(int $userId, int $resourceId): bool
    {
        if (self::isSaved($userId, $resourceId)) {
            $stmt = static::db()->prepare('DELETE FROM saved_resources WHERE user_id = ? AND resource_id = ?');
            $stmt->execute([$userId, $resourceId]);
            return false;
        }
        $stmt = static::db()->prepare('INSERT INTO saved_resources (user_id, resource_id) VALUES (?, ?)');
        $stmt->execute([$userId, $resourceId]);
        return true;
    }
}
