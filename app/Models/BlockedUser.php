<?php

namespace App\Models;

use App\Core\Model;

class BlockedUser extends Model
{
    protected static string $table = 'blocked_users';

    public static function block(int $blockerId, int $blockedId): bool
    {
        if ($blockerId === $blockedId) {
            return false;
        }
        $stmt = static::db()->prepare(
            'INSERT IGNORE INTO blocked_users (blocker_id, blocked_id) VALUES (?, ?)'
        );
        return $stmt->execute([$blockerId, $blockedId]);
    }

    public static function unblock(int $blockerId, int $blockedId): bool
    {
        $stmt = static::db()->prepare('DELETE FROM blocked_users WHERE blocker_id = ? AND blocked_id = ?');
        return $stmt->execute([$blockerId, $blockedId]);
    }

    /** True if either user has blocked the other. */
    public static function isBlockedEitherWay(int $a, int $b): bool
    {
        $stmt = static::db()->prepare(
            'SELECT 1 FROM blocked_users WHERE (blocker_id = ? AND blocked_id = ?) OR (blocker_id = ? AND blocked_id = ?)'
        );
        $stmt->execute([$a, $b, $b, $a]);
        return (bool) $stmt->fetchColumn();
    }
}
