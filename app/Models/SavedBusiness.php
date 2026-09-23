<?php

namespace App\Models;

use App\Core\Model;

class SavedBusiness extends Model
{
    protected static string $table = 'saved_businesses';

    public static function isSaved(int $userId, int $businessId): bool
    {
        $stmt = static::db()->prepare('SELECT 1 FROM saved_businesses WHERE user_id = ? AND business_id = ?');
        $stmt->execute([$userId, $businessId]);
        return (bool) $stmt->fetchColumn();
    }

    /** Toggles saved state, returns the new state (true = now saved). */
    public static function toggle(int $userId, int $businessId): bool
    {
        if (self::isSaved($userId, $businessId)) {
            $stmt = static::db()->prepare('DELETE FROM saved_businesses WHERE user_id = ? AND business_id = ?');
            $stmt->execute([$userId, $businessId]);
            return false;
        }
        $stmt = static::db()->prepare('INSERT INTO saved_businesses (user_id, business_id) VALUES (?, ?)');
        $stmt->execute([$userId, $businessId]);
        return true;
    }

    /** All business_ids the user has saved, for a single bulk lookup instead of N+1 queries per card. */
    public static function savedIdsFor(int $userId): array
    {
        $stmt = static::db()->prepare('SELECT business_id FROM saved_businesses WHERE user_id = ?');
        $stmt->execute([$userId]);
        return array_map('intval', array_column($stmt->fetchAll(), 'business_id'));
    }
}
