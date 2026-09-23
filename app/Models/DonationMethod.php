<?php

namespace App\Models;

use App\Core\Model;

class DonationMethod extends Model
{
    protected static string $table = 'donation_methods';

    public static function activeOrdered(): array
    {
        return static::db()->query(
            'SELECT * FROM donation_methods WHERE is_active = 1 ORDER BY sort_order ASC, id ASC'
        )->fetchAll();
    }

    public static function all(string $orderBy = 'sort_order ASC, id ASC'): array
    {
        return parent::all($orderBy);
    }

    public static function create(array $data): int
    {
        return static::insertRow('donation_methods', $data);
    }

    public static function update(int $id, array $data): bool
    {
        return static::updateRow('donation_methods', $id, $data);
    }
}
