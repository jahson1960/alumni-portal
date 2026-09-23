<?php

namespace App\Core;

use PDO;

abstract class Model
{
    protected static string $table = '';

    protected static function db(): PDO
    {
        return Database::connection();
    }

    public static function find(int $id): array|false
    {
        $stmt = static::db()->prepare('SELECT * FROM ' . static::$table . ' WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function all(string $orderBy = 'id DESC'): array
    {
        return static::db()->query('SELECT * FROM ' . static::$table . ' ORDER BY ' . $orderBy)->fetchAll();
    }

    public static function delete(int $id): bool
    {
        $stmt = static::db()->prepare('DELETE FROM ' . static::$table . ' WHERE id = ?');
        return $stmt->execute([$id]);
    }

    protected static function insertRow(string $table, array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn ($c) => ':' . $c, $columns);
        $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';
        $stmt = static::db()->prepare($sql);
        $stmt->execute($data);
        return (int) static::db()->lastInsertId();
    }

    protected static function updateRow(string $table, int $id, array $data): bool
    {
        $sets = implode(', ', array_map(fn ($c) => "$c = :$c", array_keys($data)));
        $data['id'] = $id;
        $stmt = static::db()->prepare("UPDATE $table SET $sets WHERE id = :id");
        return $stmt->execute($data);
    }
}
