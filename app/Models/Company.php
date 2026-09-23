<?php

namespace App\Models;

use App\Core\Model;

class Company extends Model
{
    protected static string $table = 'companies';

    public static function all(string $orderBy = 'name ASC'): array
    {
        return parent::all($orderBy);
    }

    public static function create(array $data): int
    {
        return static::insertRow('companies', $data);
    }

    public static function update(int $id, array $data): bool
    {
        return static::updateRow('companies', $id, $data);
    }

    /** Alumni whose free-text profile "company" matches this company's name. */
    public static function alumniAt(string $companyName): array
    {
        $stmt = static::db()->prepare(
            "SELECT id, name, headline, avatar FROM users
             WHERE role = 'alumni' AND status = 'active' AND profile_visibility != 'private' AND company = ?
             ORDER BY name ASC"
        );
        $stmt->execute([$companyName]);
        return $stmt->fetchAll();
    }

    /** Companies that currently have at least one open (non-expired) job, with their open job count. */
    public static function withOpenJobCounts(?string $search = null, ?string $industry = null, int $limit = 0, int $offset = 0): array
    {
        [$where, $params] = self::openJobCountsFilter($search, $industry);
        $sql = "SELECT c.*, COUNT(j.id) AS open_job_count
                FROM companies c
                JOIN jobs j ON j.company_id = c.id AND j.status = 'open' AND (j.closing_date IS NULL OR j.closing_date >= CURDATE())
                {$where}
                GROUP BY c.id
                ORDER BY open_job_count DESC, c.name ASC";
        if ($limit > 0) {
            $sql .= ' LIMIT ' . $limit . ' OFFSET ' . max(0, $offset);
        }
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function countWithOpenJobs(?string $search = null, ?string $industry = null): int
    {
        [$where, $params] = self::openJobCountsFilter($search, $industry);
        $sql = "SELECT COUNT(DISTINCT c.id)
                FROM companies c
                JOIN jobs j ON j.company_id = c.id AND j.status = 'open' AND (j.closing_date IS NULL OR j.closing_date >= CURDATE())
                {$where}";
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /** Distinct industries among companies currently hiring, for the "All Industries" filter. */
    public static function hiringIndustries(): array
    {
        return static::db()->query(
            "SELECT DISTINCT c.industry
             FROM companies c
             JOIN jobs j ON j.company_id = c.id AND j.status = 'open' AND (j.closing_date IS NULL OR j.closing_date >= CURDATE())
             WHERE c.industry IS NOT NULL AND c.industry != ''
             ORDER BY c.industry ASC"
        )->fetchAll(\PDO::FETCH_COLUMN);
    }

    private static function openJobCountsFilter(?string $search, ?string $industry): array
    {
        $clauses = [];
        $params = [];
        if ($search !== null && $search !== '') {
            $clauses[] = 'c.name LIKE :q';
            $params['q'] = '%' . $search . '%';
        }
        if ($industry !== null && $industry !== '') {
            $clauses[] = 'c.industry = :industry';
            $params['industry'] = $industry;
        }
        $where = $clauses ? 'WHERE ' . implode(' AND ', $clauses) : '';
        return [$where, $params];
    }

    /** Count of distinct companies currently hiring (at least one open job). */
    public static function countHiring(): int
    {
        return (int) static::db()->query(
            "SELECT COUNT(DISTINCT c.id) FROM companies c
             JOIN jobs j ON j.company_id = c.id AND j.status = 'open' AND (j.closing_date IS NULL OR j.closing_date >= CURDATE())"
        )->fetchColumn();
    }
}
