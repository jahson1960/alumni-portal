<?php

namespace App\Models;

use App\Core\Model;

class Post extends Model
{
    protected static string $table = 'posts';

    private const AUTHOR_FIELDS = "u.id AS author_id, u.name AS author_name, u.avatar AS author_avatar, u.headline AS author_headline";

    private const SORTS = [
        'recent' => 'p.created_at DESC',
        'liked' => '(SELECT COUNT(*) FROM post_likes pl WHERE pl.post_id = p.id) DESC, p.created_at DESC',
    ];

    /** A viewer only sees published posts that are either public, their own, or from a connection (for "connections"-visibility posts). */
    private const VISIBILITY_CLAUSE = "(p.visibility = 'public' OR p.user_id = ? OR EXISTS (
        SELECT 1 FROM connections c WHERE c.status = 'accepted'
          AND ((c.requester_id = ? AND c.recipient_id = p.user_id) OR (c.recipient_id = ? AND c.requester_id = p.user_id))
    ))";

    public static function feed(int $limit, int $offset, ?string $postType = null, string $sort = 'recent', ?int $viewerId = null): array
    {
        $where = "WHERE p.status = 'published'";
        $params = [];
        if ($viewerId !== null) {
            $where .= ' AND ' . self::VISIBILITY_CLAUSE;
            array_push($params, $viewerId, $viewerId, $viewerId);
        } else {
            $where .= " AND p.visibility = 'public'";
        }
        if ($postType !== null) {
            $where .= ' AND p.post_type = ?';
            $params[] = $postType;
        }
        $orderBy = self::SORTS[$sort] ?? self::SORTS['recent'];

        $stmt = static::db()->prepare(
            "SELECT p.*, " . self::AUTHOR_FIELDS . " FROM posts p
             JOIN users u ON u.id = p.user_id
             {$where}
             ORDER BY {$orderBy}
             LIMIT ? OFFSET ?"
        );
        $i = 1;
        foreach ($params as $param) {
            $stmt->bindValue($i++, $param);
        }
        $stmt->bindValue($i++, $limit, \PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countByType(?string $postType = null): int
    {
        if ($postType === null) {
            return self::countAll();
        }
        $stmt = static::db()->prepare("SELECT COUNT(*) FROM posts WHERE status = 'published' AND post_type = ?");
        $stmt->execute([$postType]);
        return (int) $stmt->fetchColumn();
    }

    public static function draftsForUser(int $userId): array
    {
        $stmt = static::db()->prepare("SELECT * FROM posts WHERE user_id = ? AND status = 'draft' ORDER BY updated_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function findDraftOwnedBy(int $id, int $userId): array|false
    {
        $stmt = static::db()->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ? AND status = 'draft'");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }

    public static function countDraftsForUser(int $userId): int
    {
        $stmt = static::db()->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ? AND status = 'draft'");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function update(int $id, array $data): bool
    {
        return static::updateRow('posts', $id, $data);
    }

    public static function findWithAuthor(int $id): array|false
    {
        $stmt = static::db()->prepare(
            "SELECT p.*, " . self::AUTHOR_FIELDS . " FROM posts p
             JOIN users u ON u.id = p.user_id
             WHERE p.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create(array $data): int
    {
        return static::insertRow('posts', $data);
    }

    public static function deleteAsAuthorOrAdmin(int $postId, int $actingUserId, bool $isAdmin): bool
    {
        if ($isAdmin) {
            $stmt = static::db()->prepare('DELETE FROM posts WHERE id = ?');
            return $stmt->execute([$postId]);
        }
        $stmt = static::db()->prepare('DELETE FROM posts WHERE id = ? AND user_id = ?');
        return $stmt->execute([$postId, $actingUserId]);
    }

    public static function countAll(): int
    {
        return (int) static::db()->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
    }
}
