<?php

namespace App\Models;

use App\Core\Model;

class Business extends Model
{
    protected static string $table = 'businesses';

    private const SORTS = [
        'name_asc' => 'b.name ASC',
        'newest' => 'b.created_at DESC',
        'oldest' => 'b.created_at ASC',
    ];

    /** @param array<string,string> $filters keys: q, category, business_type, location, founded_year */
    private static function whereClause(array $filters): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(b.name LIKE :q1 OR u.name LIKE :q2 OR b.category LIKE :q3)';
            $needle = '%' . $filters['q'] . '%';
            $params['q1'] = $params['q2'] = $params['q3'] = $needle;
        }
        foreach (['category', 'business_type', 'location'] as $field) {
            if (!empty($filters[$field])) {
                $where[] = "b.{$field} = :{$field}";
                $params[$field] = $filters[$field];
            }
        }
        if (!empty($filters['founded_year'])) {
            $where[] = 'b.founded_year = :founded_year';
            $params['founded_year'] = (int) $filters['founded_year'];
        }

        return [implode(' AND ', $where), $params];
    }

    public static function directory(array $filters = [], string $sort = 'name_asc', int $limit = 0, int $offset = 0): array
    {
        [$whereSql, $params] = self::whereClause($filters);
        $orderBy = self::SORTS[$sort] ?? self::SORTS['name_asc'];

        $sql = "SELECT b.*, u.name AS owner_name FROM businesses b JOIN users u ON u.id = b.owner_id
                WHERE {$whereSql} ORDER BY {$orderBy}";
        if ($limit > 0) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function countDirectory(array $filters = []): int
    {
        [$whereSql, $params] = self::whereClause($filters);
        $stmt = static::db()->prepare(
            "SELECT COUNT(*) FROM businesses b JOIN users u ON u.id = b.owner_id WHERE {$whereSql}"
        );
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public static function distinctValues(string $field): array
    {
        $allowed = ['category', 'business_type', 'location', 'founded_year'];
        if (!in_array($field, $allowed, true)) {
            return [];
        }
        return array_column(
            static::db()->query(
                "SELECT DISTINCT {$field} FROM businesses WHERE {$field} IS NOT NULL AND {$field} != '' ORDER BY {$field} ASC"
            )->fetchAll(),
            $field
        );
    }

    public static function findWithOwner(int $id): array|false
    {
        $stmt = static::db()->prepare(
            'SELECT b.*, u.name AS owner_name, u.avatar AS owner_avatar FROM businesses b JOIN users u ON u.id = b.owner_id WHERE b.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function byOwner(int $ownerId): array
    {
        $stmt = static::db()->prepare('SELECT * FROM businesses WHERE owner_id = ? ORDER BY created_at DESC');
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        return static::insertRow('businesses', $data);
    }

    public static function update(int $id, array $data): bool
    {
        return static::updateRow('businesses', $id, $data);
    }

    public static function deleteAsOwnerOrAdmin(int $id, int $actingUserId, bool $isAdmin): bool
    {
        if ($isAdmin) {
            $stmt = static::db()->prepare('DELETE FROM businesses WHERE id = ?');
            return $stmt->execute([$id]);
        }
        $stmt = static::db()->prepare('DELETE FROM businesses WHERE id = ? AND owner_id = ?');
        return $stmt->execute([$id, $actingUserId]);
    }
}
