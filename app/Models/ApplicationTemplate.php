<?php

namespace App\Models;

use App\Core\Model;

class ApplicationTemplate extends Model
{
    protected static string $table = 'application_templates';

    public const FIELD_TYPES = ['text', 'textarea', 'email', 'phone', 'number', 'url', 'select', 'file'];

    public static function forUser(int $userId): array
    {
        $stmt = static::db()->prepare('SELECT * FROM application_templates WHERE user_id = ? ORDER BY name ASC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /** Null unless the template belongs to $userId, so a poster can never load someone else's template by guessing its id. */
    public static function findForUser(int $id, int $userId): array|false
    {
        $stmt = static::db()->prepare('SELECT * FROM application_templates WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }

    public static function create(array $data): int
    {
        return static::insertRow('application_templates', $data);
    }

    public static function rename(int $id, string $name): bool
    {
        $stmt = static::db()->prepare('UPDATE application_templates SET name = ? WHERE id = ?');
        return $stmt->execute([$name, $id]);
    }
}
