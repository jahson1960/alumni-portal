<?php

namespace App\Models;

use App\Core\Model;

class HeroSlide extends Model
{
    protected static string $table = 'hero_slides';

    public static function allOrdered(): array
    {
        return static::db()->query('SELECT * FROM hero_slides ORDER BY sort_order ASC, id ASC')->fetchAll();
    }

    public static function enabledOrdered(): array
    {
        return static::db()->query("SELECT * FROM hero_slides WHERE enabled = 1 ORDER BY sort_order ASC, id ASC")->fetchAll();
    }

    public static function create(array $data): int
    {
        return static::insertRow('hero_slides', $data);
    }

    public static function update(int $id, array $data): bool
    {
        return static::updateRow('hero_slides', $id, $data);
    }

    public static function toggleEnabled(int $id): bool
    {
        $stmt = static::db()->prepare('UPDATE hero_slides SET enabled = 1 - enabled WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public static function nextSortOrder(): int
    {
        $max = static::db()->query('SELECT MAX(sort_order) FROM hero_slides')->fetchColumn();
        return $max !== null ? ((int) $max + 1) : 1;
    }
}
