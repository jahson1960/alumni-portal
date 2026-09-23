<?php

namespace App\Models;

use App\Core\Model;

class Benefit extends Model
{
    protected static string $table = 'benefits';

    public static function create(array $data): int
    {
        return static::insertRow('benefits', $data);
    }

    public static function update(int $id, array $data): bool
    {
        return static::updateRow('benefits', $id, $data);
    }

    /** Most recently added benefit — used for the Benefits menu highlight card. */
    public static function featured(): array|false
    {
        return static::db()->query('SELECT * FROM benefits ORDER BY created_at DESC LIMIT 1')->fetch();
    }
}
