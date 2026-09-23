<?php

namespace App\Models;

use App\Core\Model;

class Connection extends Model
{
    protected static string $table = 'connections';

    /** The connection row between two users, regardless of direction, or null. */
    public static function statusBetween(int $a, int $b): ?array
    {
        $stmt = static::db()->prepare(
            'SELECT * FROM connections WHERE (requester_id = ? AND recipient_id = ?) OR (requester_id = ? AND recipient_id = ?)'
        );
        $stmt->execute([$a, $b, $b, $a]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Creates a pending request if none exists between the two users yet and neither has blocked the other. Returns true if created. */
    public static function request(int $requesterId, int $recipientId): bool
    {
        if ($requesterId === $recipientId || self::statusBetween($requesterId, $recipientId)) {
            return false;
        }
        if (BlockedUser::isBlockedEitherWay($requesterId, $recipientId)) {
            return false;
        }

        static::insertRow('connections', [
            'requester_id' => $requesterId,
            'recipient_id' => $recipientId,
            'status' => 'pending',
        ]);
        return true;
    }

    /** Accepts a pending request; only the recipient may accept. */
    public static function accept(int $connectionId, int $actingUserId): bool
    {
        $stmt = static::db()->prepare("SELECT * FROM connections WHERE id = ? AND recipient_id = ? AND status = 'pending'");
        $stmt->execute([$connectionId, $actingUserId]);
        if (!$stmt->fetch()) {
            return false;
        }

        $update = static::db()->prepare("UPDATE connections SET status = 'accepted', responded_at = NOW() WHERE id = ?");
        return $update->execute([$connectionId]);
    }

    /** Deletes a connection row (decline, cancel, or remove) as long as the acting user is a party to it. */
    public static function deleteAsParty(int $connectionId, int $actingUserId): bool
    {
        $stmt = static::db()->prepare('DELETE FROM connections WHERE id = ? AND (requester_id = ? OR recipient_id = ?)');
        return $stmt->execute([$connectionId, $actingUserId, $actingUserId]);
    }

    /** Deletes the connection/pending-request row between two users, if any, as long as the acting user is a party. */
    public static function deleteBetweenAsParty(int $userId, int $otherUserId): bool
    {
        $stmt = static::db()->prepare(
            'DELETE FROM connections WHERE (requester_id = ? AND recipient_id = ?) OR (requester_id = ? AND recipient_id = ?)'
        );
        return $stmt->execute([$userId, $otherUserId, $otherUserId, $userId]);
    }

    private const CONNECTIONS_SORTS = [
        'recent' => 'c.responded_at DESC',
        'name_asc' => 'u.name ASC',
        'name_desc' => 'u.name DESC',
    ];

    public static function myConnections(int $userId, string $sort = 'recent'): array
    {
        $orderBy = self::CONNECTIONS_SORTS[$sort] ?? self::CONNECTIONS_SORTS['recent'];
        $stmt = static::db()->prepare(
            "SELECT u.id, u.name, u.headline, u.company, u.city, u.country, u.avatar, u.last_active_at,
                    u.program, u.graduation_year, c.responded_at AS connected_at
             FROM connections c
             JOIN users u ON u.id = IF(c.requester_id = ?, c.recipient_id, c.requester_id)
             WHERE (c.requester_id = ? OR c.recipient_id = ?) AND c.status = 'accepted'
             ORDER BY {$orderBy}"
        );
        $stmt->execute([$userId, $userId, $userId]);
        return $stmt->fetchAll();
    }

    public static function incomingRequests(int $userId): array
    {
        $stmt = static::db()->prepare(
            "SELECT c.id AS connection_id, u.id, u.name, u.headline, u.company, u.city, u.country, u.avatar, u.last_active_at,
                    u.program, u.graduation_year, c.created_at
             FROM connections c
             JOIN users u ON u.id = c.requester_id
             WHERE c.recipient_id = ? AND c.status = 'pending'
             ORDER BY c.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function outgoingRequests(int $userId): array
    {
        $stmt = static::db()->prepare(
            "SELECT c.id AS connection_id, u.id, u.name, u.headline, u.company, u.city, u.country, u.avatar, u.last_active_at,
                    u.program, u.graduation_year, c.created_at
             FROM connections c
             JOIN users u ON u.id = c.recipient_id
             WHERE c.requester_id = ? AND c.status = 'pending'
             ORDER BY c.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function countIncoming(int $userId): int
    {
        $stmt = static::db()->prepare("SELECT COUNT(*) FROM connections WHERE recipient_id = ? AND status = 'pending'");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function countOutgoing(int $userId): int
    {
        $stmt = static::db()->prepare("SELECT COUNT(*) FROM connections WHERE requester_id = ? AND status = 'pending'");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function countAccepted(int $userId): int
    {
        $stmt = static::db()->prepare(
            "SELECT COUNT(*) FROM connections WHERE (requester_id = ? OR recipient_id = ?) AND status = 'accepted'"
        );
        $stmt->execute([$userId, $userId]);
        return (int) $stmt->fetchColumn();
    }

    /** Users accepted-connected to both $a and $b — the "mutual connections" shown on a conversation/profile. */
    public static function mutualConnections(int $a, int $b, int $limit = 4): array
    {
        $stmt = static::db()->prepare(
            "SELECT u.id, u.name, u.avatar FROM users u
             WHERE u.id IN (
                 SELECT IF(requester_id = ?, recipient_id, requester_id) FROM connections
                 WHERE (requester_id = ? OR recipient_id = ?) AND status = 'accepted'
             )
             AND u.id IN (
                 SELECT IF(requester_id = ?, recipient_id, requester_id) FROM connections
                 WHERE (requester_id = ? OR recipient_id = ?) AND status = 'accepted'
             )
             LIMIT " . max(1, $limit)
        );
        $stmt->execute([$a, $a, $a, $b, $b, $b]);
        return $stmt->fetchAll();
    }

    public static function countMutualConnections(int $a, int $b): int
    {
        $stmt = static::db()->prepare(
            "SELECT COUNT(*) FROM users u
             WHERE u.id IN (
                 SELECT IF(requester_id = ?, recipient_id, requester_id) FROM connections
                 WHERE (requester_id = ? OR recipient_id = ?) AND status = 'accepted'
             )
             AND u.id IN (
                 SELECT IF(requester_id = ?, recipient_id, requester_id) FROM connections
                 WHERE (requester_id = ? OR recipient_id = ?) AND status = 'accepted'
             )"
        );
        $stmt->execute([$a, $a, $a, $b, $b, $b]);
        return (int) $stmt->fetchColumn();
    }

    public static function suggestions(int $userId, int $limit = 6): array
    {
        $stmt = static::db()->prepare(
            "SELECT u.id, u.name, u.headline, u.company, u.city, u.country, u.avatar, u.last_active_at,
                    u.program, u.graduation_year
             FROM users u
             JOIN users me ON me.id = ?
             WHERE u.id != ? AND u.role = 'alumni' AND u.status = 'active' AND u.profile_visibility != 'private'
               AND u.id NOT IN (
                   SELECT IF(requester_id = ?, recipient_id, requester_id) FROM connections
                   WHERE requester_id = ? OR recipient_id = ?
               )
             ORDER BY (u.industry IS NOT NULL AND u.industry = me.industry) DESC,
                      (u.program IS NOT NULL AND u.program = me.program) DESC,
                      RAND()
             LIMIT " . max(1, $limit)
        );
        $stmt->execute([$userId, $userId, $userId, $userId, $userId]);
        return $stmt->fetchAll();
    }
}
