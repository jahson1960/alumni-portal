<?php

namespace App\Models;

use App\Core\Model;

class Follow extends Model
{
    protected static string $table = 'follows';

    public static function isFollowing(int $followerId, int $followedId): bool
    {
        $stmt = static::db()->prepare('SELECT 1 FROM follows WHERE follower_id = ? AND followed_id = ?');
        $stmt->execute([$followerId, $followedId]);
        return (bool) $stmt->fetchColumn();
    }

    public static function follow(int $followerId, int $followedId): bool
    {
        if ($followerId === $followedId || self::isFollowing($followerId, $followedId)) {
            return false;
        }
        $stmt = static::db()->prepare('INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)');
        return $stmt->execute([$followerId, $followedId]);
    }

    public static function unfollow(int $followerId, int $followedId): bool
    {
        $stmt = static::db()->prepare('DELETE FROM follows WHERE follower_id = ? AND followed_id = ?');
        return $stmt->execute([$followerId, $followedId]);
    }

    public static function following(int $userId): array
    {
        $stmt = static::db()->prepare(
            "SELECT u.id, u.name, u.headline, u.company, u.city, u.country, u.avatar, u.last_active_at,
                    u.program, u.graduation_year
             FROM follows f
             JOIN users u ON u.id = f.followed_id
             WHERE f.follower_id = ?
             ORDER BY u.name ASC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function followerCount(int $userId): int
    {
        $stmt = static::db()->prepare('SELECT COUNT(*) FROM follows WHERE followed_id = ?');
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }
}
