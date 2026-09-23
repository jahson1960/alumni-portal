<?php

namespace App\Models;

use App\Core\Model;

class Message extends Model
{
    protected static string $table = 'messages';

    /** Distinct conversation partners for a user, most recent first. */
    public static function conversations(int $userId): array
    {
        $stmt = static::db()->prepare(
            'SELECT IF(sender_id = ?, recipient_id, sender_id) AS partner_id, MAX(created_at) AS last_at
             FROM messages WHERE sender_id = ? OR recipient_id = ?
             GROUP BY partner_id ORDER BY last_at DESC'
        );
        $stmt->execute([$userId, $userId, $userId]);
        $rows = $stmt->fetchAll();

        $conversations = [];
        foreach ($rows as $row) {
            $partner = User::find((int) $row['partner_id']);
            if (!$partner) {
                continue;
            }
            // Messaging is connection-gated; a thread with someone you're no longer (or never
            // were, per stale seed data) connected to shouldn't surface in the active list.
            $status = Connection::statusBetween($userId, (int) $partner['id']);
            if ($status === null || $status['status'] !== 'accepted') {
                continue;
            }
            $lastMessage = static::db()->prepare(
                'SELECT * FROM messages WHERE (sender_id = ? AND recipient_id = ?) OR (sender_id = ? AND recipient_id = ?)
                 ORDER BY created_at DESC LIMIT 1'
            );
            $lastMessage->execute([$userId, $partner['id'], $partner['id'], $userId]);

            $conversations[] = [
                'partner' => $partner,
                'last_message' => $lastMessage->fetch(),
                'unread_count' => self::unreadCountFrom($userId, (int) $partner['id']),
            ];
        }

        return $conversations;
    }

    public static function thread(int $userA, int $userB, int $sinceId = 0): array
    {
        $stmt = static::db()->prepare(
            'SELECT * FROM messages
             WHERE ((sender_id = ? AND recipient_id = ?) OR (sender_id = ? AND recipient_id = ?)) AND id > ?
             ORDER BY created_at ASC'
        );
        $stmt->execute([$userA, $userB, $userB, $userA, $sinceId]);
        return $stmt->fetchAll();
    }

    public static function send(int $senderId, int $recipientId, string $body): int
    {
        return static::insertRow('messages', [
            'sender_id' => $senderId,
            'recipient_id' => $recipientId,
            'body' => $body,
        ]);
    }

    public static function markThreadRead(int $viewerId, int $partnerId): void
    {
        $stmt = static::db()->prepare(
            'UPDATE messages SET read_at = NOW() WHERE recipient_id = ? AND sender_id = ? AND read_at IS NULL'
        );
        $stmt->execute([$viewerId, $partnerId]);
    }

    public static function unreadCount(int $userId): int
    {
        $stmt = static::db()->prepare('SELECT COUNT(*) FROM messages WHERE recipient_id = ? AND read_at IS NULL');
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function unreadCountFrom(int $userId, int $partnerId): int
    {
        $stmt = static::db()->prepare('SELECT COUNT(*) FROM messages WHERE recipient_id = ? AND sender_id = ? AND read_at IS NULL');
        $stmt->execute([$userId, $partnerId]);
        return (int) $stmt->fetchColumn();
    }
}
