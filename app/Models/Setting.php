<?php

namespace App\Models;

use App\Core\Model;

class Setting extends Model
{
    protected static string $table = 'settings';

    public static function all(string $orderBy = 'id DESC'): array
    {
        $rows = static::db()->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
        $map = [];
        foreach ($rows as $row) {
            $map[$row['setting_key']] = $row['setting_value'];
        }
        return $map;
    }

    public static function get(string $key, string $default = ''): string
    {
        $stmt = static::db()->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();
        return $value !== false ? $value : $default;
    }

    public static function set(string $key, string $value): void
    {
        $stmt = static::db()->prepare(
            'INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v)
             ON DUPLICATE KEY UPDATE setting_value = :v2'
        );
        $stmt->execute(['k' => $key, 'v' => $value, 'v2' => $value]);
    }

    public static function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            self::set($key, $value);
        }
    }
}
