<?php

namespace App\Models;

use App\Core\Model;

class GivingCause extends Model
{
    protected static string $table = 'giving_causes';

    public static function allOrdered(): array
    {
        return static::db()->query('SELECT * FROM giving_causes ORDER BY column_group ASC, sort_order ASC, id ASC')->fetchAll();
    }

    public static function byGroup(string $group): array
    {
        $stmt = static::db()->prepare('SELECT * FROM giving_causes WHERE column_group = ? ORDER BY sort_order ASC, id ASC');
        $stmt->execute([$group]);
        return $stmt->fetchAll();
    }

    public static function findBySlug(string $slug): array|false
    {
        $stmt = static::db()->prepare('SELECT * FROM giving_causes WHERE slug = ?');
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public static function create(array $data): int
    {
        return static::insertRow('giving_causes', $data);
    }

    public static function update(int $id, array $data): bool
    {
        return static::updateRow('giving_causes', $id, $data);
    }

    public static function nextSortOrder(string $group): int
    {
        $stmt = static::db()->prepare('SELECT MAX(sort_order) FROM giving_causes WHERE column_group = ?');
        $stmt->execute([$group]);
        $max = $stmt->fetchColumn();
        return $max !== null ? ((int) $max + 1) : 1;
    }
}
