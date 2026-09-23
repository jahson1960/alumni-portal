<?php

namespace App\Models;

use App\Core\Model;

class PageVisibility extends Model
{
    protected static string $table = 'page_visibility';

    /** @var array<string,array{audience:string,show_in_nav:bool}>|null request-scoped cache: page_key => row */
    private static ?array $cache = null;

    public static function allRows(): array
    {
        return static::db()->query('SELECT * FROM page_visibility ORDER BY label ASC')->fetchAll();
    }

    /**
     * @param array<string,string> $audienceByKey page_key => audience, for every row (from the <select> fields)
     * @param string[] $navVisibleKeys page_keys whose "Show in Menu" checkbox was checked
     */
    public static function setMany(array $audienceByKey, array $navVisibleKeys = []): void
    {
        $stmt = static::db()->prepare('UPDATE page_visibility SET audience = ?, show_in_nav = ? WHERE page_key = ?');
        foreach ($audienceByKey as $key => $audience) {
            if (!in_array($audience, ['public', 'alumni', 'editor', 'admin'], true)) {
                continue;
            }
            $stmt->execute([$audience, in_array($key, $navVisibleKeys, true) ? 1 : 0, $key]);
        }
        self::$cache = null;
    }

    private static function row(string $pageKey): ?array
    {
        if (self::$cache === null) {
            self::$cache = [];
            foreach (static::db()->query('SELECT page_key, audience, show_in_nav FROM page_visibility')->fetchAll() as $row) {
                self::$cache[$row['page_key']] = $row;
            }
        }
        return self::$cache[$pageKey] ?? null;
    }

    public static function audienceOf(string $pageKey): string
    {
        return self::row($pageKey)['audience'] ?? 'public';
    }

    /** Whether admin has this page's nav link switched on (independent of who can access the page itself). */
    public static function showInNav(string $pageKey): bool
    {
        $row = self::row($pageKey);
        return $row === null || (bool) $row['show_in_nav'];
    }

    /** Empty $pageKey always passes (used for items not tied to an admin-configurable page). */
    public static function isVisibleTo(string $pageKey, ?array $viewer): bool
    {
        if ($pageKey === '') {
            return true;
        }
        return match (self::audienceOf($pageKey)) {
            'admin' => $viewer !== null && ($viewer['role'] ?? '') === 'admin',
            'editor' => $viewer !== null && in_array($viewer['role'] ?? '', ['editor', 'admin'], true),
            'alumni' => $viewer !== null,
            default => true,
        };
    }

    /** Whether a nav link to this page should render for $viewer: admin has it switched on, and the viewer could actually access it. */
    public static function shouldShowInNav(string $pageKey, ?array $viewer): bool
    {
        return self::showInNav($pageKey) && self::isVisibleTo($pageKey, $viewer);
    }
}
