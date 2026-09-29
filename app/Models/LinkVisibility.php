<?php

namespace App\Models;

use App\Core\Model;

class LinkVisibility extends Model
{
    protected static string $table = 'link_visibility';

    /** @var array<string,array{is_visible:bool,requires_login:bool}>|null request-scoped cache */
    private static ?array $cache = null;

    public static function allRows(): array
    {
        return static::db()->query('SELECT * FROM link_visibility ORDER BY page_key ASC, label ASC')->fetchAll();
    }

    private static function loadCache(): array
    {
        if (self::$cache === null) {
            self::$cache = [];
            foreach (static::db()->query('SELECT link_key, is_visible, requires_login FROM link_visibility')->fetchAll() as $row) {
                self::$cache[$row['link_key']] = [
                    'is_visible' => (bool) $row['is_visible'],
                    'requires_login' => (bool) $row['requires_login'],
                ];
            }
        }
        return self::$cache;
    }

    /** Links not present in the table default to visible (fail-open, same convention as PageTabVisibility). */
    public static function isVisible(string $linkKey): bool
    {
        return self::loadCache()[$linkKey]['is_visible'] ?? true;
    }

    /** Links not present in the table default to not requiring login (fail-open). */
    public static function requiresLogin(string $linkKey): bool
    {
        return self::loadCache()[$linkKey]['requires_login'] ?? false;
    }

    /**
     * @param string[] $visibleKeys link_key values whose "show in menu" checkbox was checked
     * @param string[] $requiresLoginKeys link_key values whose "requires login" checkbox was checked
     */
    public static function setMany(array $visibleKeys, array $requiresLoginKeys): void
    {
        $stmt = static::db()->prepare('UPDATE link_visibility SET is_visible = ?, requires_login = ? WHERE link_key = ?');
        foreach (self::allRows() as $row) {
            $stmt->execute([
                in_array($row['link_key'], $visibleKeys, true) ? 1 : 0,
                in_array($row['link_key'], $requiresLoginKeys, true) ? 1 : 0,
                $row['link_key'],
            ]);
        }
        self::$cache = null;
    }
}
