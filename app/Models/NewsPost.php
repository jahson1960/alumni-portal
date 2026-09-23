<?php

namespace App\Models;

use App\Core\Model;

class NewsPost extends Model
{
    protected static string $table = 'news_posts';

    public static function published(
        int $limit = 0,
        ?string $categorySlug = null,
        ?string $q = null,
        string $sort = 'recent',
        int $offset = 0
    ): array {
        [$sql, $params] = self::publishedQuery('DISTINCT n.*', $categorySlug, $q);
        $sql .= $sort === 'oldest' ? ' ORDER BY n.published_at ASC' : ' ORDER BY n.published_at DESC';
        if ($limit > 0) {
            $sql .= ' LIMIT ' . $limit . ' OFFSET ' . max(0, $offset);
        }
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function countPublished(?string $categorySlug = null, ?string $q = null): int
    {
        [$sql, $params] = self::publishedQuery('COUNT(DISTINCT n.id)', $categorySlug, $q);
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /** slug => count of published posts in each news category. */
    public static function categoryCounts(): array
    {
        $stmt = static::db()->query(
            "SELECT c.slug, COUNT(DISTINCT n.id) AS cnt
             FROM categories c
             JOIN news_category nc ON nc.category_id = c.id
             JOIN news_posts n ON n.id = nc.news_post_id AND n.status = 'published'
             WHERE c.type = 'news'
             GROUP BY c.id, c.slug"
        );
        return array_column($stmt->fetchAll(), 'cnt', 'slug');
    }

    private static function publishedQuery(string $select, ?string $categorySlug, ?string $q): array
    {
        $params = [];
        $sql = "SELECT {$select} FROM news_posts n";
        if ($categorySlug !== null) {
            $sql .= " JOIN news_category nc ON nc.news_post_id = n.id
                      JOIN categories c ON c.id = nc.category_id AND c.type='news' AND c.slug = :slug";
            $params['slug'] = $categorySlug;
        }
        $sql .= " WHERE n.status='published'";
        if ($q !== null && $q !== '') {
            $sql .= " AND (n.title LIKE :q OR n.excerpt LIKE :q)";
            $params['q'] = '%' . $q . '%';
        }
        return [$sql, $params];
    }

    public static function findBySlug(string $slug): array|false
    {
        $stmt = static::db()->prepare("SELECT * FROM news_posts WHERE slug = ? AND status = 'published'");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public static function slugExists(string $slug, int $ignoreId = 0): bool
    {
        $stmt = static::db()->prepare('SELECT COUNT(*) FROM news_posts WHERE slug = ? AND id != ?');
        $stmt->execute([$slug, $ignoreId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public static function create(array $data): int
    {
        return static::insertRow('news_posts', $data);
    }

    public static function update(int $id, array $data): bool
    {
        return static::updateRow('news_posts', $id, $data);
    }
}
