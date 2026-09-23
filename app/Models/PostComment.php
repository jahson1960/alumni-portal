<?php

namespace App\Models;

use App\Core\Model;

class PostComment extends Model
{
    protected static string $table = 'post_comments';

    public static function forPost(int $postId): array
    {
        $stmt = static::db()->prepare(
            "SELECT c.*, u.name AS author_name, u.avatar AS author_avatar
             FROM post_comments c
             JOIN users u ON u.id = c.user_id
             WHERE c.post_id = ?
             ORDER BY c.created_at ASC"
        );
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }

    public static function countForPost(int $postId): int
    {
        $stmt = static::db()->prepare('SELECT COUNT(*) FROM post_comments WHERE post_id = ?');
        $stmt->execute([$postId]);
        return (int) $stmt->fetchColumn();
    }

    public static function create(int $postId, int $userId, string $content): int
    {
        return static::insertRow('post_comments', [
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $content,
        ]);
    }

    public static function deleteAsAuthorOrAdmin(int $commentId, int $actingUserId, bool $isAdmin): bool
    {
        if ($isAdmin) {
            $stmt = static::db()->prepare('DELETE FROM post_comments WHERE id = ?');
            return $stmt->execute([$commentId]);
        }
        $stmt = static::db()->prepare('DELETE FROM post_comments WHERE id = ? AND user_id = ?');
        return $stmt->execute([$commentId, $actingUserId]);
    }

    /** The post_id a comment belongs to (used to redirect back after delete). */
    public static function postIdFor(int $commentId): ?int
    {
        $stmt = static::db()->prepare('SELECT post_id FROM post_comments WHERE id = ?');
        $stmt->execute([$commentId]);
        $val = $stmt->fetchColumn();
        return $val !== false ? (int) $val : null;
    }
}
