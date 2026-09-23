<?php

namespace App\Models;

use App\Core\Model;

class LinkVisibility extends Model
{
    protected static string $table = 'link_visibility';

    /** @var array<string,bool>|null request-scoped cache: link_key => is_visible */
    private static ?array $cache = null;

    public static function allRows(): array
    {
        return static::db()->query('SELECT * FROM link_visibility ORDER BY page_key ASC, label ASC')->fetchAll();
    }

    /** Links not present in the table default to visible (fail-open, same convention as PageTabVisibility). */
    public static function isVisible(string $linkKey): bool
    {
        if (self::$cache === null) {
            self::$cache = [];
            foreach (static::db()->query('SELECT link_key, is_visible FROM link_visibility')->fetchAll() as $row) {
                self::$cache[$row['link_key']] = (bool) $row['is_visible'];
            }
        }
        return self::$cache[$linkKey] ?? true;
    }

    /** @param string[] $visibleKeys link_key values whose checkbox was checked */
    public static function setMany(array $visibleKeys): void
    {
        $stmt = static::db()->prepare('UPDATE link_visibility SET is_visible = ? WHERE link_key = ?');
        foreach (self::allRows() as $row) {
            $stmt->execute([in_array($row['link_key'], $visibleKeys, true) ? 1 : 0, $row['link_key']]);
        }
        self::$cache = null;
    }
}
