<?php

namespace App\Models;

use App\Core\Model;

class Resource extends Model
{
    protected static string $table = 'resources';

    /** The Career Resources tab's general browse categories. */
    public const CAREER_CATEGORIES = ['Business', 'Career', 'Personal Development'];

    /** The Interview Prep tab's practice-material categories. */
    public const INTERVIEW_CATEGORIES = ['Behavioral Questions', 'Technical Questions', 'Case Interview Prep', 'Salary Negotiation'];

    public const TYPES = ['Guide', 'Template', 'Report', 'Video', 'PPTX'];

    public static function create(array $data): int
    {
        return static::insertRow('resources', $data);
    }

    public static function update(int $id, array $data): bool
    {
        return static::updateRow('resources', $id, $data);
    }

    /**
     * @param array{q?:string,category?:string,type?:string} $filters
     * @param string[] $categoryScope Restrict results to this set of categories (CAREER_CATEGORIES or INTERVIEW_CATEGORIES).
     */
    public static function search(array $filters, array $categoryScope): array
    {
        [$where, $params] = self::searchWhere($filters, $categoryScope);
        $stmt = static::db()->prepare("SELECT * FROM resources WHERE {$where} ORDER BY title ASC");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** @param string[] $categoryScope */
    public static function featured(int $limit, array $categoryScope): array
    {
        $placeholders = self::inPlaceholders($categoryScope, 'cat');
        $stmt = static::db()->prepare(
            "SELECT * FROM resources WHERE is_featured = 1 AND category IN ({$placeholders['sql']})
             ORDER BY updated_at DESC LIMIT " . max(1, $limit)
        );
        $stmt->execute($placeholders['params']);
        return $stmt->fetchAll();
    }

    /** slug-free category => count, restricted to the given scope, only counting categories that actually have resources. */
    public static function countsByCategory(array $categoryScope): array
    {
        $placeholders = self::inPlaceholders($categoryScope, 'cat');
        $stmt = static::db()->prepare(
            "SELECT category, COUNT(*) AS cnt FROM resources WHERE category IN ({$placeholders['sql']}) GROUP BY category"
        );
        $stmt->execute($placeholders['params']);
        return array_column($stmt->fetchAll(), 'cnt', 'category');
    }

    /** @param string[] $categoryScope */
    public static function countScope(array $categoryScope): int
    {
        $placeholders = self::inPlaceholders($categoryScope, 'cat');
        $stmt = static::db()->prepare("SELECT COUNT(*) FROM resources WHERE category IN ({$placeholders['sql']})");
        $stmt->execute($placeholders['params']);
        return (int) $stmt->fetchColumn();
    }

    private static function searchWhere(array $filters, array $categoryScope): array
    {
        $placeholders = self::inPlaceholders($categoryScope, 'cat');
        $where = ["category IN ({$placeholders['sql']})"];
        $params = $placeholders['params'];

        if (!empty($filters['q'])) {
            $where[] = '(title LIKE :q1 OR description LIKE :q2)';
            $params['q1'] = $params['q2'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['category']) && in_array($filters['category'], $categoryScope, true)) {
            $where[] = 'category = :onlyCategory';
            $params['onlyCategory'] = $filters['category'];
        }
        if (!empty($filters['type'])) {
            $where[] = 'resource_type = :type';
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['featured'])) {
            $where[] = 'is_featured = 1';
        }

        return [implode(' AND ', $where), $params];
    }

    /** @return array{sql:string,params:array<string,string>} */
    private static function inPlaceholders(array $values, string $prefix): array
    {
        $placeholders = [];
        $params = [];
        foreach (array_values($values) as $i => $value) {
            $key = $prefix . $i;
            $placeholders[] = ':' . $key;
            $params[$key] = $value;
        }
        return ['sql' => implode(',', $placeholders), 'params' => $params];
    }
}
