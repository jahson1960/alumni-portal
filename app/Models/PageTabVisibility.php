<?php

namespace App\Models;

use App\Core\Model;

class PageTabVisibility extends Model
{
    protected static string $table = 'page_tab_visibility';

    /** @var array<string,bool>|null request-scoped cache: "pageKey|tabKey" => is_visible */
    private static ?array $cache = null;

    public static function allRows(): array
    {
        return static::db()->query('SELECT * FROM page_tab_visibility ORDER BY page_key ASC, tab_key ASC')->fetchAll();
    }

    /** Rows not present in the table default to visible (fail-open, same convention as PageVisibility). */
    public static function isVisible(string $pageKey, string $tabKey): bool
    {
        if (self::$cache === null) {
            self::$cache = [];
            foreach (static::db()->query('SELECT page_key, tab_key, is_visible FROM page_tab_visibility')->fetchAll() as $row) {
                self::$cache[$row['page_key'] . '|' . $row['tab_key']] = (bool) $row['is_visible'];
            }
        }
        return self::$cache[$pageKey . '|' . $tabKey] ?? true;
    }

    /** First tab in $orderedKeys that's currently visible, or null if none are (nothing left to show). */
    public static function firstVisibleTab(string $pageKey, array $orderedKeys): ?string
    {
        foreach ($orderedKeys as $tabKey) {
            if (self::isVisible($pageKey, $tabKey)) {
                return $tabKey;
            }
        }
        return null;
    }

    /** @param string[] $visibleCompositeKeys "pageKey|tabKey" strings whose checkbox was checked */
    public static function setMany(array $visibleCompositeKeys): void
    {
        $stmt = static::db()->prepare('UPDATE page_tab_visibility SET is_visible = ? WHERE page_key = ? AND tab_key = ?');
        foreach (self::allRows() as $row) {
            $composite = $row['page_key'] . '|' . $row['tab_key'];
            $stmt->execute([in_array($composite, $visibleCompositeKeys, true) ? 1 : 0, $row['page_key'], $row['tab_key']]);
        }
        self::$cache = null;
    }
}
