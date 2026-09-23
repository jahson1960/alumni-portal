<?php

namespace App\Models;

use App\Core\Model;

class Event extends Model
{
    protected static string $table = 'events';

    public const CATEGORIES = ['Global Events', 'Reunions', 'Executive Programmes', 'Webinars'];

    public static function upcoming(int $limit = 0, ?bool $virtualOnly = null): array
    {
        $sql = "SELECT * FROM events WHERE event_date >= CURDATE()";
        if ($virtualOnly === true) {
            $sql .= ' AND is_virtual = 1';
        }
        $sql .= " ORDER BY event_date ASC";
        if ($limit > 0) {
            $sql .= ' LIMIT ' . $limit;
        }
        return static::db()->query($sql)->fetchAll();
    }

    public static function all(string $orderBy = 'event_date ASC'): array
    {
        return parent::all($orderBy);
    }

    public static function create(array $data): int
    {
        return static::insertRow('events', $data);
    }

    public static function update(int $id, array $data): bool
    {
        return static::updateRow('events', $id, $data);
    }

    /** @param array{q?:string,category?:string,featured?:string,format?:string} $filters */
    public static function search(array $filters, int $limit = 0, int $offset = 0): array
    {
        [$where, $params] = self::searchWhere($filters);
        $sql = "SELECT * FROM events WHERE {$where} ORDER BY event_date ASC";
        if ($limit > 0) {
            $sql .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
        }
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** @param array{q?:string,category?:string,featured?:string,format?:string} $filters */
    public static function countSearch(array $filters): int
    {
        [$where, $params] = self::searchWhere($filters);
        $stmt = static::db()->prepare("SELECT COUNT(*) FROM events WHERE {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /** All events (past or future) within a given "YYYY-MM" month, for the calendar view. */
    public static function forMonth(string $yearMonth, array $filters): array
    {
        [$where, $params] = self::searchWhere($filters, upcomingOnly: false);
        $where .= ' AND DATE_FORMAT(event_date, "%Y-%m") = :yearMonth';
        $params['yearMonth'] = $yearMonth;
        $stmt = static::db()->prepare("SELECT * FROM events WHERE {$where} ORDER BY event_date ASC");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** All events a user has saved/bookmarked, soonest first. */
    public static function savedByUser(int $userId): array
    {
        $stmt = static::db()->prepare(
            "SELECT e.* FROM saved_events s JOIN events e ON e.id = s.event_id
             WHERE s.user_id = ? ORDER BY e.event_date ASC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /** @return array{0:string,1:array<string,string>} */
    private static function searchWhere(array $filters, bool $upcomingOnly = true): array
    {
        $where = $upcomingOnly ? ['event_date >= CURDATE()'] : ['1=1'];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(title LIKE :q1 OR description LIKE :q2 OR location LIKE :q3)';
            $params['q1'] = $params['q2'] = $params['q3'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['category']) && in_array($filters['category'], self::CATEGORIES, true)) {
            $where[] = 'category = :category';
            $params['category'] = $filters['category'];
        }
        if (!empty($filters['featured'])) {
            $where[] = 'is_featured = 1';
        }
        if (($filters['format'] ?? '') === 'virtual') {
            $where[] = 'is_virtual = 1';
        } elseif (($filters['format'] ?? '') === 'in-person') {
            $where[] = 'is_virtual = 0';
        }

        return [implode(' AND ', $where), $params];
    }
}
