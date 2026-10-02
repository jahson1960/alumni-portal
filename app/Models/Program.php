<?php

namespace App\Models;

use App\Core\Model;

class Program extends Model
{
    protected static string $table = 'programs';

    public static function create(string $name): int
    {
        return static::insertRow('programs', ['name' => $name]);
    }

    public static function update(int $id, string $name): bool
    {
        return static::updateRow('programs', $id, ['name' => $name]);
    }
}
