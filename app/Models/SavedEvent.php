<?php

namespace App\Models;

use App\Core\Model;

class SavedEvent extends Model
{
    protected static string $table = 'saved_events';

    public static function isSaved(int $userId, int $eventId): bool
    {
        $stmt = static::db()->prepare('SELECT 1 FROM saved_events WHERE user_id = ? AND event_id = ?');
        $stmt->execute([$userId, $eventId]);
        return (bool) $stmt->fetchColumn();
    }

    /** All event_id values the user has saved, for cheap in-memory lookups on a listing page. */
    public static function savedIdsFor(int $userId): array
    {
        $stmt = static::db()->prepare('SELECT event_id FROM saved_events WHERE user_id = ?');
        $stmt->execute([$userId]);
        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    /** Toggles saved state, returns the new state (true = now saved). */
    public static function toggle(int $userId, int $eventId): bool
    {
        if (self::isSaved($userId, $eventId)) {
            $stmt = static::db()->prepare('DELETE FROM saved_events WHERE user_id = ? AND event_id = ?');
            $stmt->execute([$userId, $eventId]);
            return false;
        }
        $stmt = static::db()->prepare('INSERT INTO saved_events (user_id, event_id) VALUES (?, ?)');
        $stmt->execute([$userId, $eventId]);
        return true;
    }
}
