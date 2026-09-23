<?php

namespace App\Models;

use App\Core\Model;

class MentorshipRequest extends Model
{
    protected static string $table = 'mentorship_requests';

    /** Career stage is derived from graduation_year rather than stored, since it drifts every year on its own. */
    private const CAREER_STAGES = [
        'early' => '(YEAR(CURDATE()) - graduation_year) <= 5',
        'mid' => '(YEAR(CURDATE()) - graduation_year) BETWEEN 6 AND 15',
        'senior' => '(YEAR(CURDATE()) - graduation_year) > 15',
    ];

    /** @param array<string,string> $filters keys: q, area, industry, location, career_stage, availability */
    private static function mentorsWhere(array $filters): array
    {
        $where = ["role = 'alumni'", "status = 'active'", 'is_mentor = 1', "profile_visibility != 'private'"];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(name LIKE :q1 OR headline LIKE :q2 OR mentorship_areas LIKE :q3 OR company LIKE :q4)';
            $needle = '%' . $filters['q'] . '%';
            $params['q1'] = $params['q2'] = $params['q3'] = $params['q4'] = $needle;
        }
        if (!empty($filters['area'])) {
            $where[] = 'mentorship_areas LIKE :area';
            $params['area'] = '%' . $filters['area'] . '%';
        }
        if (!empty($filters['industry'])) {
            $where[] = 'industry = :industry';
            $params['industry'] = $filters['industry'];
        }
        if (!empty($filters['location'])) {
            $where[] = '(city = :location OR country = :location2)';
            $params['location'] = $params['location2'] = $filters['location'];
        }
        if (!empty($filters['career_stage']) && isset(self::CAREER_STAGES[$filters['career_stage']])) {
            $where[] = 'graduation_year IS NOT NULL AND ' . self::CAREER_STAGES[$filters['career_stage']];
        }
        if (!empty($filters['availability'])) {
            $where[] = 'mentor_availability = :availability';
            $params['availability'] = $filters['availability'];
        }

        return [implode(' AND ', $where), $params];
    }

    /** @param array<string,string> $filters same keys as mentorsWhere() */
    public static function mentors(array $filters = []): array
    {
        [$whereSql, $params] = self::mentorsWhere($filters);
        $stmt = static::db()->prepare(
            "SELECT id, name, headline, company, industry, city, country, avatar, mentorship_areas, mentor_availability, graduation_year
             FROM users WHERE {$whereSql} ORDER BY name ASC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** @param array<string,string> $filters same keys as mentorsWhere() */
    public static function countMentors(array $filters = []): int
    {
        [$whereSql, $params] = self::mentorsWhere($filters);
        $stmt = static::db()->prepare("SELECT COUNT(*) FROM users WHERE {$whereSql}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /** Distinct industries among mentors, for the filter dropdown. */
    public static function distinctIndustries(): array
    {
        return array_column(
            static::db()->query(
                "SELECT DISTINCT industry FROM users WHERE is_mentor = 1 AND industry IS NOT NULL AND industry != '' ORDER BY industry ASC"
            )->fetchAll(),
            'industry'
        );
    }

    /** Distinct cities/countries among mentors, for the filter dropdown. */
    public static function distinctLocations(): array
    {
        $stmt = static::db()->query(
            "SELECT DISTINCT city AS loc FROM users WHERE is_mentor = 1 AND city IS NOT NULL AND city != ''
             UNION
             SELECT DISTINCT country AS loc FROM users WHERE is_mentor = 1 AND country IS NOT NULL AND country != ''
             ORDER BY loc ASC"
        );
        return array_column($stmt->fetchAll(), 'loc');
    }

    public static function create(int $mentorId, int $requesterId, string $area, string $message): bool
    {
        static::insertRow('mentorship_requests', [
            'mentor_id' => $mentorId,
            'requester_id' => $requesterId,
            'area' => $area,
            'message' => $message,
        ]);
        return true;
    }

    public static function incomingFor(int $mentorId): array
    {
        $stmt = static::db()->prepare(
            "SELECT m.*, u.name, u.headline, u.avatar FROM mentorship_requests m
             JOIN users u ON u.id = m.requester_id
             WHERE m.mentor_id = ? ORDER BY m.created_at DESC"
        );
        $stmt->execute([$mentorId]);
        return $stmt->fetchAll();
    }

    public static function sentBy(int $requesterId): array
    {
        $stmt = static::db()->prepare(
            "SELECT m.*, u.name, u.headline, u.avatar FROM mentorship_requests m
             JOIN users u ON u.id = m.mentor_id
             WHERE m.requester_id = ? ORDER BY m.created_at DESC"
        );
        $stmt->execute([$requesterId]);
        return $stmt->fetchAll();
    }

    public static function respond(int $id, int $mentorId, string $status): bool
    {
        $stmt = static::db()->prepare(
            "UPDATE mentorship_requests SET status = ?, responded_at = NOW() WHERE id = ? AND mentor_id = ?"
        );
        return $stmt->execute([$status, $id, $mentorId]);
    }
}
