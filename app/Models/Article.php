<?php

namespace App\Models;

use App\Core\Model;

class Article extends Model
{
    protected static string $table = 'articles';

    private const AUTHOR_JOIN = "SELECT a.*, u.name AS author_name, u.avatar AS author_avatar FROM articles a JOIN users u ON u.id = a.user_id";

    public static function published(?string $category = null, ?string $search = null): array
    {
        $sql = self::AUTHOR_JOIN . " WHERE a.status = 'published'";
        $params = [];
        if ($category !== null) {
            $sql .= ' AND a.category = ?';
            $params[] = $category;
        }
        if ($search !== null && $search !== '') {
            $sql .= ' AND a.title LIKE ?';
            $params[] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY a.published_at DESC';
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function featured(): array|false
    {
        return static::db()->query(self::AUTHOR_JOIN . " WHERE a.status = 'published' ORDER BY a.published_at DESC LIMIT 1")->fetch();
    }

    public static function findPublished(int $id): array|false
    {
        $stmt = static::db()->prepare(self::AUTHOR_JOIN . " WHERE a.id = ? AND a.status = 'published'");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function findWithAuthor(int $id): array|false
    {
        $stmt = static::db()->prepare(self::AUTHOR_JOIN . ' WHERE a.id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function pending(): array
    {
        return static::db()->query(self::AUTHOR_JOIN . " WHERE a.status = 'pending' ORDER BY a.created_at ASC")->fetchAll();
    }

    public static function byAuthor(int $userId): array
    {
        $stmt = static::db()->prepare('SELECT * FROM articles WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        return static::insertRow('articles', $data);
    }

    public static function publish(int $id): bool
    {
        $stmt = static::db()->prepare("UPDATE articles SET status = 'published', published_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
