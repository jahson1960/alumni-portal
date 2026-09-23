<?php

namespace App\Models;

use App\Core\Model;

class UserExperience extends Model
{
    protected static string $table = 'user_experience';

    public static function forUser(int $userId): array
    {
        $stmt = static::db()->prepare(
            'SELECT * FROM user_experience WHERE user_id = ?
             ORDER BY is_current DESC, COALESCE(end_date, "9999-12-31") DESC, start_date DESC, sort_order ASC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        return static::insertRow('user_experience', $data);
    }

    public static function update(int $id, array $data): bool
    {
        return static::updateRow('user_experience', $id, $data);
    }

    /** Deletes a row only if it belongs to the acting user. */
    public static function deleteAsOwner(int $id, int $userId): bool
    {
        $stmt = static::db()->prepare('DELETE FROM user_experience WHERE id = ? AND user_id = ?');
        return $stmt->execute([$id, $userId]);
    }

    public static function findAsOwner(int $id, int $userId): array|false
    {
        $stmt = static::db()->prepare('SELECT * FROM user_experience WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }
}
