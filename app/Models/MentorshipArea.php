<?php

namespace App\Models;

use App\Core\Model;

class MentorshipArea extends Model
{
    protected static string $table = 'mentorship_areas';

    public static function create(string $name): int
    {
        return static::insertRow('mentorship_areas', ['name' => $name]);
    }

    public static function update(int $id, string $name): bool
    {
        return static::updateRow('mentorship_areas', $id, ['name' => $name]);
    }
}
