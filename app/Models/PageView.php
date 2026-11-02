<?php

namespace App\Models;

use App\Core\Model;

/**
 * Self-hosted page-view tracking, written from app/Views/partials/analytics_tracker.php on
 * every real page load (never from the app's own background AJAX calls, since those run via
 * fetch() and never trigger a freshly-rendered page's inline <script>).
 *
 * "Time on site" isn't tracked directly - it's derived at query time by grouping a visitor's
 * rows into sessions (a 30+ minute gap between consecutive page views = a new session) and
 * measuring first-to-last-plus-its-own-duration within each. See sessionsSince().
 */
class PageView extends Model
{
    protected static string $table = 'page_views';

    /** Minutes of inactivity that end one visit/session and start a new one for the same visitor. */
    private const SESSION_GAP_MINUTES = 30;

    public static function record(string $visitorId, ?int $userId, string $path, ?string $referrer): int
    {
        return static::insertRow('page_views', [
            'visitor_id' => $visitorId,
            'user_id' => $userId,
            'path' => $path,
            'referrer' => $referrer !== '' ? $referrer : null,
        ]);
    }

    /** $visitorId must match the row's own - so one visitor's cookie can't overwrite another's duration. */
    public static function updateDuration(int $id, string $visitorId, int $seconds): void
    {
        $stmt = static::db()->prepare('UPDATE page_views SET duration_seconds = ? WHERE id = ? AND visitor_id = ?');
        $stmt->execute([max(0, $seconds), $id, $visitorId]);
    }

    public static function countUniqueVisitors(string $since): int
    {
        $stmt = static::db()->prepare('SELECT COUNT(DISTINCT visitor_id) FROM page_views WHERE created_at >= ?');
        $stmt->execute([$since]);
        return (int) $stmt->fetchColumn();
    }

    public static function countViews(string $since): int
    {
        $stmt = static::db()->prepare('SELECT COUNT(*) FROM page_views WHERE created_at >= ?');
        $stmt->execute([$since]);
        return (int) $stmt->fetchColumn();
    }

    public static function averageDurationSeconds(string $since): float
    {
        $stmt = static::db()->prepare('SELECT AVG(duration_seconds) FROM page_views WHERE created_at >= ? AND duration_seconds IS NOT NULL');
        $stmt->execute([$since]);
        return (float) ($stmt->fetchColumn() ?: 0);
    }

    /** Date (Y-m-d) => ['views' => int, 'visitors' => int], one row per day, for the trend chart. */
    public static function dailyCounts(string $since): array
    {
        $stmt = static::db()->prepare(
            "SELECT DATE(created_at) AS day, COUNT(*) AS views, COUNT(DISTINCT visitor_id) AS visitors
             FROM page_views WHERE created_at >= ? GROUP BY DATE(created_at) ORDER BY day ASC"
        );
        $stmt->execute([$since]);
        $byDay = [];
        foreach ($stmt->fetchAll() as $row) {
            $byDay[$row['day']] = ['views' => (int) $row['views'], 'visitors' => (int) $row['visitors']];
        }
        return $byDay;
    }

    /** @return array<int,array{path:string,views:int,avg_duration:float}> */
    public static function topPages(string $since, int $limit = 10): array
    {
        $stmt = static::db()->prepare(
            'SELECT path, COUNT(*) AS views, AVG(duration_seconds) AS avg_duration
             FROM page_views WHERE created_at >= ? GROUP BY path ORDER BY views DESC LIMIT ' . max(1, $limit)
        );
        $stmt->execute([$since]);
        return array_map(fn ($row) => [
            'path' => $row['path'],
            'views' => (int) $row['views'],
            'avg_duration' => (float) ($row['avg_duration'] ?: 0),
        ], $stmt->fetchAll());
    }

    /**
     * Individual visits, newest first, with the visitor's name/email/role joined in when the
     * page view happened to be made by a logged-in user - null user fields mean a guest
     * ("Non-registered user" in the view).
     */
    public static function paginatedVisits(string $since, int $limit, int $offset): array
    {
        $stmt = static::db()->prepare(
            'SELECT pv.id, pv.path, pv.duration_seconds, pv.created_at,
                    u.name AS user_name, u.email AS user_email, u.role AS user_role
             FROM page_views pv
             LEFT JOIN users u ON u.id = pv.user_id
             WHERE pv.created_at >= ?
             ORDER BY pv.created_at DESC
             LIMIT ' . max(1, $limit) . ' OFFSET ' . max(0, $offset)
        );
        $stmt->execute([$since]);
        return $stmt->fetchAll();
    }

    /**
     * Average session ("time on site") length in seconds, grouping each visitor's rows into
     * sessions split on a 30+ minute gap. Done in PHP rather than a window-function SQL query -
     * simplest and most portable across hosts, and fine performance-wise: this only runs once
     * per Admin > Analytics page load, over a bounded date range.
     */
    public static function averageSessionSeconds(string $since): float
    {
        $stmt = static::db()->prepare(
            'SELECT visitor_id, created_at, COALESCE(duration_seconds, 0) AS duration_seconds
             FROM page_views WHERE created_at >= ? ORDER BY visitor_id ASC, created_at ASC'
        );
        $stmt->execute([$since]);
        $rows = $stmt->fetchAll();

        $sessionLengths = [];
        $currentVisitor = null;
        $sessionStart = null;
        $lastSeen = null;
        $lastDuration = 0;

        $closeSession = function () use (&$sessionLengths, &$sessionStart, &$lastSeen, &$lastDuration) {
            if ($sessionStart === null || $lastSeen === null) {
                return;
            }
            $length = (strtotime($lastSeen) - strtotime($sessionStart)) + $lastDuration;
            $sessionLengths[] = max(0, $length);
        };

        foreach ($rows as $row) {
            $gapMinutes = ($lastSeen !== null) ? (strtotime($row['created_at']) - strtotime($lastSeen)) / 60 : null;
            $isNewSession = $row['visitor_id'] !== $currentVisitor || $gapMinutes === null || $gapMinutes > self::SESSION_GAP_MINUTES;

            if ($isNewSession) {
                $closeSession();
                $sessionStart = $row['created_at'];
                $currentVisitor = $row['visitor_id'];
            }
            $lastSeen = $row['created_at'];
            $lastDuration = (int) $row['duration_seconds'];
        }
        $closeSession();

        return $sessionLengths ? array_sum($sessionLengths) / count($sessionLengths) : 0.0;
    }
}
