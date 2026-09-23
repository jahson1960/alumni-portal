<?php

namespace App\Models;

use App\Core\Model;

class PostLike extends Model
{
    protected static string $table = 'post_likes';

    public static function hasLiked(int $postId, int $userId): bool
    {
        $stmt = static::db()->prepare('SELECT 1 FROM post_likes WHERE post_id = ? AND user_id = ?');
        $stmt->execute([$postId, $userId]);
        return (bool) $stmt->fetchColumn();
    }

    public static function count(int $postId): int
    {
        $stmt = static::db()->prepare('SELECT COUNT(*) FROM post_likes WHERE post_id = ?');
        $stmt->execute([$postId]);
        return (int) $stmt->fetchColumn();
    }

    /** Toggles the like for this user and returns the new liked state (true = now liked). */
    public static function toggle(int $postId, int $userId): bool
    {
        if (self::hasLiked($postId, $userId)) {
            $stmt = static::db()->prepare('DELETE FROM post_likes WHERE post_id = ? AND user_id = ?');
            $stmt->execute([$postId, $userId]);
            return false;
        }

        $stmt = static::db()->prepare('INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)');
        $stmt->execute([$postId, $userId]);
        return true;
    }
}
