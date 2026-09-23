<?php

namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    protected static string $table = 'categories';

    public static function forType(string $type): array
    {
        $stmt = static::db()->prepare('SELECT * FROM categories WHERE type = ? ORDER BY name ASC');
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }

    public static function create(string $type, string $name): int
    {
        return static::insertRow('categories', [
            'type' => $type,
            'name' => $name,
            'slug' => self::slugify($name),
        ]);
    }

    public static function update(int $id, string $name): bool
    {
        return static::updateRow('categories', $id, [
            'name' => $name,
            'slug' => self::slugify($name),
        ]);
    }

    public static function slugForId(int $id): ?string
    {
        $stmt = static::db()->prepare('SELECT slug FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        $slug = $stmt->fetchColumn();
        return $slug !== false ? $slug : null;
    }

    public static function forJob(int $jobId): array
    {
        $stmt = static::db()->prepare(
            'SELECT c.* FROM categories c
             JOIN job_category jc ON jc.category_id = c.id
             WHERE jc.job_id = ? ORDER BY c.name ASC'
        );
        $stmt->execute([$jobId]);
        return $stmt->fetchAll();
    }

    public static function forNews(int $postId): array
    {
        $stmt = static::db()->prepare(
            'SELECT c.* FROM categories c
             JOIN news_category nc ON nc.category_id = c.id
             WHERE nc.news_post_id = ? ORDER BY c.name ASC'
        );
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }

    /** @param int[] $categoryIds */
    public static function syncJobCategories(int $jobId, array $categoryIds): void
    {
        $db = static::db();
        $db->prepare('DELETE FROM job_category WHERE job_id = ?')->execute([$jobId]);
        $stmt = $db->prepare('INSERT INTO job_category (job_id, category_id) VALUES (?, ?)');
        foreach (self::cleanIds($categoryIds) as $categoryId) {
            $stmt->execute([$jobId, $categoryId]);
        }
    }

    /** @param int[] $categoryIds */
    public static function syncNewsCategories(int $postId, array $categoryIds): void
    {
        $db = static::db();
        $db->prepare('DELETE FROM news_category WHERE news_post_id = ?')->execute([$postId]);
        $stmt = $db->prepare('INSERT INTO news_category (news_post_id, category_id) VALUES (?, ?)');
        foreach (self::cleanIds($categoryIds) as $categoryId) {
            $stmt->execute([$postId, $categoryId]);
        }
    }

    /** @param mixed[] $ids @return int[] */
    private static function cleanIds(array $ids): array
    {
        return array_unique(array_filter(array_map('intval', $ids), fn ($id) => $id > 0));
    }

    private static function slugify(string $text): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $text), '-'));
        return $slug !== '' ? $slug : 'category-' . time();
    }
}
