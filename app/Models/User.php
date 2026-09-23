<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected static string $table = 'users';

    public static function findByEmail(string $email): array|false
    {
        $stmt = static::db()->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function emailExists(string $email): bool
    {
        return self::findByEmail($email) !== false;
    }

    public static function create(array $data): int
    {
        return static::insertRow('users', $data);
    }

    public static function update(int $id, array $data): bool
    {
        return static::updateRow('users', $id, $data);
    }

    /** Updates the activity timestamp, throttled to at most once per minute to avoid a write on every request. */
    public static function touchActivity(int $id): void
    {
        $stmt = static::db()->prepare(
            "UPDATE users SET last_active_at = NOW()
             WHERE id = ? AND (last_active_at IS NULL OR last_active_at < DATE_SUB(NOW(), INTERVAL 1 MINUTE))"
        );
        $stmt->execute([$id]);
    }

    /** Online = active within the last 5 minutes. */
    public static function isOnline(?string $lastActiveAt): bool
    {
        return $lastActiveAt !== null && strtotime($lastActiveAt) >= strtotime('-5 minutes');
    }

    /** @param array<int> $ids @return array<int,?string> id => last_active_at, for a live online-status poll. */
    public static function lastActiveByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = static::db()->prepare("SELECT id, last_active_at FROM users WHERE id IN ({$placeholders})");
        $stmt->execute(array_values($ids));

        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[(int) $row['id']] = $row['last_active_at'];
        }
        return $result;
    }

    private const DIRECTORY_SORTS = [
        'recent' => 'created_at DESC',
        'name_asc' => 'name ASC',
        'name_desc' => 'name DESC',
    ];

    /**
     * @param array<string,string> $filters keys: q, skills, program, graduation_year, industry, country, city, company
     * @param ?array<int> $onlyIds when given, restricts results to these user ids (e.g. a "Saved" list) — named
     *   placeholders throughout, since PDO can't mix ? and :name placeholders in one statement
     * @return array{0:string,1:array<string,mixed>} [whereSql, params]
     */
    private static function directoryWhere(array $filters, bool $viewerLoggedIn, ?array $onlyIds = null): array
    {
        $where = ["role = 'alumni'", "status = 'active'"];
        $params = [];

        $where[] = $viewerLoggedIn ? "profile_visibility IN ('public','alumni')" : "profile_visibility = 'public'";

        if ($onlyIds !== null) {
            if (empty($onlyIds)) {
                $where[] = '1=0';
            } else {
                $idPlaceholders = [];
                foreach (array_values($onlyIds) as $i => $id) {
                    $key = "sid{$i}";
                    $idPlaceholders[] = ":{$key}";
                    $params[$key] = (int) $id;
                }
                $where[] = 'id IN (' . implode(',', $idPlaceholders) . ')';
            }
        }

        if (!empty($filters['q'])) {
            $where[] = '(name LIKE :q1 OR company LIKE :q2 OR headline LIKE :q3 OR program LIKE :q4 OR graduation_year LIKE :q5)';
            $needle = '%' . $filters['q'] . '%';
            $params['q1'] = $params['q2'] = $params['q3'] = $params['q4'] = $params['q5'] = $needle;
        }
        if (!empty($filters['skills'])) {
            $where[] = '(skills LIKE :sk1 OR expertise_areas LIKE :sk2 OR business_interests LIKE :sk3)';
            $needle = '%' . $filters['skills'] . '%';
            $params['sk1'] = $params['sk2'] = $params['sk3'] = $needle;
        }
        foreach (['program', 'industry', 'country', 'city', 'company'] as $field) {
            if (!empty($filters[$field])) {
                $where[] = "{$field} = :{$field}";
                $params[$field] = $filters[$field];
            }
        }
        if (!empty($filters['graduation_year'])) {
            $where[] = 'graduation_year = :graduation_year';
            $params['graduation_year'] = (int) $filters['graduation_year'];
        }

        return [implode(' AND ', $where), $params];
    }

    /**
     * @param array<string,string> $filters keys: q, skills, program, graduation_year, industry, country, city, company
     * @param bool $viewerLoggedIn whether the visitor is authenticated (alumni or admin)
     */
    /** @param ?array<int> $onlyIds when given, restricts results to these user ids (e.g. a "Saved" list) */
    public static function alumniDirectory(array $filters, bool $viewerLoggedIn, string $sort = 'name_asc', int $limit = 0, int $offset = 0, ?array $onlyIds = null): array
    {
        [$whereSql, $params] = self::directoryWhere($filters, $viewerLoggedIn, $onlyIds);
        $orderBy = self::DIRECTORY_SORTS[$sort] ?? self::DIRECTORY_SORTS['name_asc'];

        $sql = 'SELECT id, name, headline, company, industry, city, country, graduation_year, program, avatar, last_active_at, skills, expertise_areas
                FROM users WHERE ' . $whereSql . ' ORDER BY ' . $orderBy;
        if ($limit > 0) {
            $sql .= ' LIMIT ' . $limit . ' OFFSET ' . max(0, $offset);
        }

        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * @param array<string,string> $filters same keys as alumniDirectory()
     * @param ?array<int> $onlyIds when given, restricts results to these user ids (e.g. a "Saved" list)
     */
    public static function countAlumniDirectory(array $filters, bool $viewerLoggedIn, ?array $onlyIds = null): int
    {
        [$whereSql, $params] = self::directoryWhere($filters, $viewerLoggedIn, $onlyIds);
        $stmt = static::db()->prepare('SELECT COUNT(*) FROM users WHERE ' . $whereSql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public static function publicProfile(int $id, bool $isOwnerOrAdmin, bool $viewerLoggedIn): array|false
    {
        $stmt = static::db()->prepare("SELECT * FROM users WHERE id = ? AND role = 'alumni' AND status = 'active'");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if (!$user) {
            return false;
        }
        if ($isOwnerOrAdmin) {
            return $user;
        }
        if ($user['profile_visibility'] === 'private') {
            return false;
        }
        if ($user['profile_visibility'] === 'alumni' && !$viewerLoggedIn) {
            return false;
        }

        return $user;
    }

    /** Distinct non-empty values for a directory filter dropdown, drawn from active alumni. */
    public static function distinctFilterValues(string $column): array
    {
        $allowed = ['program', 'industry', 'country', 'city', 'company', 'graduation_year'];
        if (!in_array($column, $allowed, true)) {
            throw new \InvalidArgumentException("Column not filterable: {$column}");
        }

        $stmt = static::db()->query(
            "SELECT DISTINCT {$column} FROM users
             WHERE role = 'alumni' AND status = 'active' AND {$column} IS NOT NULL AND {$column} != ''
             ORDER BY {$column} ASC"
        );
        return array_column($stmt->fetchAll(), $column);
    }

    public static function countAlumni(): int
    {
        return (int) static::db()->query("SELECT COUNT(*) FROM users WHERE role='alumni' AND status='active'")->fetchColumn();
    }

    public static function allAlumniAdmin(): array
    {
        return static::db()->query("SELECT * FROM users WHERE role IN ('alumni','editor') ORDER BY created_at DESC")->fetchAll();
    }

    public static function adminIds(): array
    {
        return array_column(
            static::db()->query("SELECT id FROM users WHERE role='admin' AND status='active'")->fetchAll(),
            'id'
        );
    }

    /** Distinct (program, graduation_year) cohorts with member counts. */
    public static function cohorts(): array
    {
        return static::db()->query(
            "SELECT program, graduation_year, COUNT(*) AS member_count
             FROM users
             WHERE role = 'alumni' AND status = 'active' AND profile_visibility != 'private'
               AND program IS NOT NULL AND program != '' AND graduation_year IS NOT NULL
             GROUP BY program, graduation_year
             ORDER BY graduation_year DESC, program ASC"
        )->fetchAll();
    }

    public static function cohortMembers(string $program, int $year): array
    {
        $stmt = static::db()->prepare(
            "SELECT id, name, headline, company, city, country, avatar FROM users
             WHERE role = 'alumni' AND status = 'active' AND profile_visibility != 'private'
               AND program = ? AND graduation_year = ?
             ORDER BY name ASC"
        );
        $stmt->execute([$program, $year]);
        return $stmt->fetchAll();
    }

    public static function spotlighted(): array
    {
        return static::db()->query(
            "SELECT id, name, headline, company, city, country, avatar, spotlight_note, expertise_areas FROM users
             WHERE role = 'alumni' AND status = 'active' AND profile_visibility != 'private' AND is_spotlighted = 1
             ORDER BY name ASC"
        )->fetchAll();
    }

    public static function countByCountry(): array
    {
        return static::db()->query(
            "SELECT country, COUNT(*) AS total FROM users
             WHERE role = 'alumni' AND status = 'active' AND profile_visibility != 'private'
               AND country IS NOT NULL AND country != ''
             GROUP BY country ORDER BY total DESC"
        )->fetchAll();
    }

    public static function countByCity(string $country): array
    {
        $stmt = static::db()->prepare(
            "SELECT city, COUNT(*) AS total FROM users
             WHERE role = 'alumni' AND status = 'active' AND profile_visibility != 'private'
               AND country = ? AND city IS NOT NULL AND city != ''
             GROUP BY city ORDER BY total DESC"
        );
        $stmt->execute([$country]);
        return $stmt->fetchAll();
    }

    public static function cohortStats(string $program, int $year): array
    {
        $stmt = static::db()->prepare(
            "SELECT COUNT(DISTINCT country) AS countries, COUNT(DISTINCT company) AS companies, COUNT(*) AS members
             FROM users WHERE role = 'alumni' AND status = 'active' AND profile_visibility != 'private'
               AND program = ? AND graduation_year = ?"
        );
        $stmt->execute([$program, $year]);
        return $stmt->fetch() ?: ['countries' => 0, 'companies' => 0, 'members' => 0];
    }
}
