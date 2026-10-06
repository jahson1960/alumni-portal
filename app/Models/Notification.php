<?php

namespace App\Models;

use App\Core\Model;

class Notification extends Model
{
    protected static string $table = 'notifications';

    /** Inserts the in-app notification, then enqueues a matching branded email — the one choke point for all 14 notification types. */
    public static function notify(int $userId, string $type, string $message, ?string $link = null): void
    {
        static::insertRow('notifications', [
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'link' => $link,
        ]);

        $user = User::find($userId);
        if ($user && !empty($user['email'])) {
            EmailQueue::enqueue($user['email'], $user['name'], notification_email_subject($type), render_email(
                notification_email_subject($type),
                '<p>Hi ' . e($user['first_name'] ?: $user['name']) . ',</p><p>' . e($message) . '</p>',
                $link !== null ? 'View' : null,
                $link
            ));
        }
    }

    public static function forUser(int $userId, int $limit = 50): array
    {
        $stmt = static::db()->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ' . max(1, $limit));
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function unreadCount(int $userId): int
    {
        $stmt = static::db()->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function markAllRead(int $userId): void
    {
        $stmt = static::db()->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0');
        $stmt->execute([$userId]);
    }
}
