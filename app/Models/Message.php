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
            // Messaging is gated (see canMessage()); a thread with someone who's since been
            // disconnected and has no other standing relationship shouldn't surface here.
            if (!self::canMessage($userId, (int) $partner['id'])) {
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

    /**
     * True if $a and $b are allowed to message each other: an accepted connection, or a
     * recruiter/applicant relationship - either posted a job the other applied to, or one of
     * them is an admin reaching someone who has genuinely applied to a job somewhere (admins
     * manage every job's applicants, including jobs with no individual poster on record).
     * Hiring conversations shouldn't require first sending and accepting a connection request.
     */
    public static function canMessage(int $a, int $b): bool
    {
        $status = Connection::statusBetween($a, $b);
        if ($status !== null && $status['status'] === 'accepted') {
            return true;
        }

        $stmt = static::db()->prepare(
            'SELECT 1 FROM job_applications ja JOIN jobs j ON j.id = ja.job_id
             WHERE (ja.user_id = ? AND j.posted_by = ?) OR (ja.user_id = ? AND j.posted_by = ?)
             LIMIT 1'
        );
        $stmt->execute([$a, $b, $b, $a]);
        if ($stmt->fetchColumn()) {
            return true;
        }

        $roles = static::db()->prepare('SELECT id, role FROM users WHERE id IN (?, ?)');
        $roles->execute([$a, $b]);
        $roleById = array_column($roles->fetchAll(), 'role', 'id');
        $adminId = null;
        $otherId = null;
        if (($roleById[$a] ?? null) === 'admin') {
            $adminId = $a;
            $otherId = $b;
        } elseif (($roleById[$b] ?? null) === 'admin') {
            $adminId = $b;
            $otherId = $a;
        }
        if ($adminId === null) {
            return false;
        }
        $stmt = static::db()->prepare('SELECT 1 FROM job_applications WHERE user_id = ? LIMIT 1');
        $stmt->execute([$otherId]);
        return (bool) $stmt->fetchColumn();
    }
}
