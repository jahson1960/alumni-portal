<?php

namespace App\Models;

use App\Core\Model;

class AlumniRoster extends Model
{
    protected static string $table = 'alumni_roster';

    public static function findByMatric(string $matricNumber): array|false
    {
        $stmt = static::db()->prepare('SELECT * FROM alumni_roster WHERE matric_number = ?');
        $stmt->execute([$matricNumber]);
        return $stmt->fetch();
    }

    public static function all(string $orderBy = 'created_at DESC'): array
    {
        return parent::all($orderBy);
    }

    public static function create(array $data): int
    {
        return static::insertRow('alumni_roster', $data);
    }

    public static function update(int $id, array $data): bool
    {
        return static::updateRow('alumni_roster', $id, $data);
    }

    public static function markClaimed(int $id, int $userId): void
    {
        static::updateRow('alumni_roster', $id, ['claimed_by_user_id' => $userId]);
    }
}
