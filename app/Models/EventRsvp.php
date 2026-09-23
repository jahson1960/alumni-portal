<?php

namespace App\Models;

use App\Core\Model;

class EventRsvp extends Model
{
    protected static string $table = 'event_rsvps';

    public static function isAttending(int $eventId, int $userId): bool
    {
        $stmt = static::db()->prepare('SELECT 1 FROM event_rsvps WHERE event_id = ? AND user_id = ?');
        $stmt->execute([$eventId, $userId]);
        return (bool) $stmt->fetchColumn();
    }

    /** Toggles RSVP, returns the new state (true = now attending). */
    public static function toggle(int $eventId, int $userId): bool
    {
        if (self::isAttending($eventId, $userId)) {
            $stmt = static::db()->prepare('DELETE FROM event_rsvps WHERE event_id = ? AND user_id = ?');
            $stmt->execute([$eventId, $userId]);
            return false;
        }
        $stmt = static::db()->prepare('INSERT INTO event_rsvps (event_id, user_id) VALUES (?, ?)');
        $stmt->execute([$eventId, $userId]);
        return true;
    }

    public static function attendees(int $eventId): array
    {
        $stmt = static::db()->prepare(
            "SELECT u.id, u.name, u.headline, u.avatar FROM event_rsvps r
             JOIN users u ON u.id = r.user_id
             WHERE r.event_id = ? ORDER BY r.created_at DESC"
        );
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }

    /** First few attendees for a card's avatar-stack preview. */
    public static function attendeesPreview(int $eventId, int $limit = 3): array
    {
        $stmt = static::db()->prepare(
            "SELECT u.id, u.name, u.avatar FROM event_rsvps r
             JOIN users u ON u.id = r.user_id
             WHERE r.event_id = ? ORDER BY r.created_at DESC LIMIT " . max(1, $limit)
        );
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }

    public static function count(int $eventId): int
    {
        $stmt = static::db()->prepare('SELECT COUNT(*) FROM event_rsvps WHERE event_id = ?');
        $stmt->execute([$eventId]);
        return (int) $stmt->fetchColumn();
    }

    /** All event_id values a user is registered for, for cheap in-memory lookups on a listing page. */
    public static function attendingIdsFor(int $userId): array
    {
        $stmt = static::db()->prepare('SELECT event_id FROM event_rsvps WHERE user_id = ?');
        $stmt->execute([$userId]);
        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    /** All events a user has RSVP'd to, most recent first. */
    public static function myEvents(int $userId): array
    {
        $stmt = static::db()->prepare(
            "SELECT e.* FROM event_rsvps r JOIN events e ON e.id = r.event_id
             WHERE r.user_id = ? ORDER BY e.event_date DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
