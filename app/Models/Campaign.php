<?php

namespace App\Models;

use App\Core\Model;

class Campaign extends Model
{
    protected static string $table = 'campaigns';

    public static function active(): array
    {
        return static::db()->query("SELECT * FROM campaigns WHERE status = 'active' ORDER BY created_at DESC")->fetchAll();
    }

    /** Most recently added active campaign — used for the Give Back menu highlight card. */
    public static function featured(): array|false
    {
        return static::db()->query("SELECT * FROM campaigns WHERE status = 'active' ORDER BY created_at DESC LIMIT 1")->fetch();
    }

    /** @param string $sort 'newest'|'most_funded'|'least_funded' */
    public static function search(string $q = '', string $sort = 'newest'): array
    {
        $sql = "SELECT * FROM campaigns WHERE status = 'active'";
        $params = [];
        if ($q !== '') {
            $sql .= ' AND (title LIKE :q1 OR description LIKE :q2)';
            $params['q1'] = $params['q2'] = '%' . $q . '%';
        }
        $orderBy = match ($sort) {
            'most_funded' => '(CASE WHEN goal_amount > 0 THEN raised_amount / goal_amount ELSE 0 END) DESC',
            'least_funded' => '(CASE WHEN goal_amount > 0 THEN raised_amount / goal_amount ELSE 0 END) ASC',
            default => 'created_at DESC',
        };
        $sql .= " ORDER BY {$orderBy}";
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function totalRaised(): float
    {
        return (float) static::db()->query("SELECT COALESCE(SUM(raised_amount), 0) FROM campaigns WHERE status = 'active'")->fetchColumn();
    }

    public static function activeCount(): int
    {
        return (int) static::db()->query("SELECT COUNT(*) FROM campaigns WHERE status = 'active'")->fetchColumn();
    }

    public static function create(array $data): int
    {
        return static::insertRow('campaigns', $data);
    }

    public static function update(int $id, array $data): bool
    {
        return static::updateRow('campaigns', $id, $data);
    }
}
