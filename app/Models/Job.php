<?php

namespace App\Models;

use App\Core\Model;

class Job extends Model
{
    protected static string $table = 'jobs';

    public const JOB_TYPES = ['Full-time', 'Part-time', 'Contract', 'Internship'];
    public const EXPERIENCE_LEVELS = ['entry' => 'Entry Level', 'mid' => 'Mid Level', 'senior' => 'Senior Level', 'executive' => 'Executive'];
    public const WORK_MODES = ['onsite' => 'On-site', 'remote' => 'Remote', 'hybrid' => 'Hybrid'];
    public const SALARY_BUCKETS = [
        'under_500k' => ['label' => 'Under \u{20a6}500,000', 'min' => null, 'max' => 500000],
        '500k_1_5m' => ['label' => '\u{20a6}500,000 - \u{20a6}1,500,000', 'min' => 500000, 'max' => 1500000],
        '1_5m_3m' => ['label' => '\u{20a6}1,500,000 - \u{20a6}3,000,000', 'min' => 1500000, 'max' => 3000000],
        'above_3m' => ['label' => 'Above \u{20a6}3,000,000', 'min' => 3000000, 'max' => null],
    ];
    public const POSTED_WITHIN = ['24h' => 1, 'week' => 7, 'month' => 30];

    public static function open(int $limit = 0, ?string $categorySlug = null, ?string $search = null): array
    {
        $params = [];
        $sql = "SELECT DISTINCT j.* FROM jobs j";
        if ($categorySlug !== null) {
            $sql .= " JOIN job_category jc ON jc.job_id = j.id
                      JOIN categories c ON c.id = jc.category_id AND c.type='job' AND c.slug = :slug";
            $params['slug'] = $categorySlug;
        }
        $sql .= " WHERE j.status='open' AND j.approval_status='approved' AND (j.closing_date IS NULL OR j.closing_date >= CURDATE())";
        if ($search !== null && $search !== '') {
            $sql .= ' AND (j.title LIKE :q1 OR j.company LIKE :q2 OR j.location LIKE :q3)';
            $needle = '%' . $search . '%';
            $params['q1'] = $params['q2'] = $params['q3'] = $needle;
        }
        $sql .= " ORDER BY j.is_featured DESC, j.posted_at DESC";
        if ($limit > 0) {
            $sql .= ' LIMIT ' . $limit;
        }
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * The Job Board / Advanced Search filter set. `$filters` recognized keys:
     * q, location, category (slug), industry, company, experience_level,
     * job_type (array), work_mode (array), salary (bucket key), posted_within, sort.
     */
    public static function search(array $filters, int $limit = 0, int $offset = 0): array
    {
        [$sql, $params] = self::searchQuery('DISTINCT j.*', $filters);
        $sql .= self::sortClause($filters['sort'] ?? 'recent');
        if ($limit > 0) {
            $sql .= ' LIMIT ' . $limit . ' OFFSET ' . max(0, $offset);
        }
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function countSearch(array $filters): int
    {
        [$sql, $params] = self::searchQuery('COUNT(DISTINCT j.id)', $filters);
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    private static function sortClause(string $sort): string
    {
        return $sort === 'salary'
            ? ' ORDER BY j.salary_max DESC, j.is_featured DESC, j.posted_at DESC'
            : ' ORDER BY j.is_featured DESC, j.posted_at DESC';
    }

    private static function searchQuery(string $select, array $filters): array
    {
        $joins = '';
        $where = ["j.status='open'", "j.approval_status='approved'", '(j.closing_date IS NULL OR j.closing_date >= CURDATE())'];
        $params = [];

        if (!empty($filters['category'])) {
            $joins .= " JOIN job_category jc ON jc.job_id = j.id JOIN categories cat ON cat.id = jc.category_id AND cat.type='job' AND cat.slug = :category";
            $params['category'] = $filters['category'];
        }
        if (!empty($filters['industry'])) {
            $joins .= ' JOIN companies co ON co.id = j.company_id AND co.industry = :industry';
            $params['industry'] = $filters['industry'];
        }
        if (!empty($filters['q'])) {
            $where[] = '(j.title LIKE :q1 OR j.company LIKE :q2)';
            $params['q1'] = $params['q2'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['location'])) {
            $where[] = 'j.location LIKE :loc';
            $params['loc'] = '%' . $filters['location'] . '%';
        }
        if (!empty($filters['company'])) {
            $where[] = 'j.company LIKE :company';
            $params['company'] = '%' . $filters['company'] . '%';
        }
        if (!empty($filters['experience_level'])) {
            $where[] = 'j.experience_level = :exp';
            $params['exp'] = $filters['experience_level'];
        }
        if (!empty($filters['job_type']) && is_array($filters['job_type'])) {
            $placeholders = [];
            foreach (array_values($filters['job_type']) as $i => $jobType) {
                $key = 'jt' . $i;
                $placeholders[] = ':' . $key;
                $params[$key] = $jobType;
            }
            $where[] = 'j.job_type IN (' . implode(',', $placeholders) . ')';
        }
        if (!empty($filters['work_mode']) && is_array($filters['work_mode'])) {
            $placeholders = [];
            foreach (array_values($filters['work_mode']) as $i => $mode) {
                $key = 'wm' . $i;
                $placeholders[] = ':' . $key;
                $params[$key] = $mode;
            }
            $where[] = 'j.work_mode IN (' . implode(',', $placeholders) . ')';
        }
        if (!empty($filters['salary']) && isset(self::SALARY_BUCKETS[$filters['salary']])) {
            $bucket = self::SALARY_BUCKETS[$filters['salary']];
            if ($bucket['min'] !== null) {
                $where[] = '(j.salary_max IS NULL OR j.salary_max >= :sal_min)';
                $params['sal_min'] = $bucket['min'];
            }
            if ($bucket['max'] !== null) {
                $where[] = '(j.salary_min IS NULL OR j.salary_min <= :sal_max)';
                $params['sal_max'] = $bucket['max'];
            }
        }
        if (!empty($filters['posted_within']) && isset(self::POSTED_WITHIN[$filters['posted_within']])) {
            $where[] = 'j.posted_at >= DATE_SUB(NOW(), INTERVAL :days DAY)';
            $params['days'] = self::POSTED_WITHIN[$filters['posted_within']];
        }

        $sql = "SELECT {$select} FROM jobs j{$joins} WHERE " . implode(' AND ', $where);
        return [$sql, $params];
    }

    /** Most recently posted open, featured job — used for menu/homepage highlight cards. */
    public static function featuredOpen(): array|false
    {
        return static::db()->query(
            "SELECT * FROM jobs
             WHERE status='open' AND approval_status='approved' AND is_featured = 1 AND (closing_date IS NULL OR closing_date >= CURDATE())
             ORDER BY posted_at DESC LIMIT 1"
        )->fetch();
    }

    public static function countOpen(): int
    {
        return (int) static::db()->query(
            "SELECT COUNT(*) FROM jobs WHERE status='open' AND approval_status='approved' AND (closing_date IS NULL OR closing_date >= CURDATE())"
        )->fetchColumn();
    }

    /** job_type => count of open jobs, for the Job Board's "Quick Filters" sidebar. */
    public static function countsByJobType(): array
    {
        $rows = static::db()->query(
            "SELECT job_type, COUNT(*) AS cnt FROM jobs
             WHERE status='open' AND approval_status='approved' AND (closing_date IS NULL OR closing_date >= CURDATE())
             GROUP BY job_type"
        )->fetchAll();
        return array_column($rows, 'cnt', 'job_type');
    }

    /** experience_level => count of open jobs, for the Job Board's "Quick Filters" sidebar. */
    public static function countsByExperienceLevel(): array
    {
        $rows = static::db()->query(
            "SELECT experience_level, COUNT(*) AS cnt FROM jobs
             WHERE status='open' AND approval_status='approved' AND (closing_date IS NULL OR closing_date >= CURDATE())
               AND experience_level IS NOT NULL
             GROUP BY experience_level"
        )->fetchAll();
        return array_column($rows, 'cnt', 'experience_level');
    }

    /**
     * Top {$limit} open-job locations by frequency (excluding remote-work-mode jobs, which the
     * sidebar buckets separately), plus how many open jobs fall outside that top set ("Other").
     * @return array{top: array<int, array{location:string,cnt:int}>, remote: int, other: int}
     */
    public static function locationBreakdown(int $limit = 3): array
    {
        $top = static::db()->prepare(
            "SELECT location, COUNT(*) AS cnt FROM jobs
             WHERE status='open' AND approval_status='approved' AND (closing_date IS NULL OR closing_date >= CURDATE())
               AND work_mode != 'remote' AND location IS NOT NULL AND location != ''
             GROUP BY location ORDER BY cnt DESC LIMIT " . max(1, $limit)
        );
        $top->execute();
        $topRows = $top->fetchAll();

        $remote = (int) static::db()->query(
            "SELECT COUNT(*) FROM jobs WHERE status='open' AND approval_status='approved' AND (closing_date IS NULL OR closing_date >= CURDATE()) AND work_mode = 'remote'"
        )->fetchColumn();

        $total = self::countOpen();
        $other = max(0, $total - $remote - array_sum(array_column($topRows, 'cnt')));

        return ['top' => $topRows, 'remote' => $remote, 'other' => $other];
    }

    public static function openByCompany(int $companyId): array
    {
        $stmt = static::db()->prepare(
            "SELECT * FROM jobs WHERE company_id = ? AND status = 'open' AND approval_status='approved' AND (closing_date IS NULL OR closing_date >= CURDATE()) ORDER BY posted_at DESC"
        );
        $stmt->execute([$companyId]);
        return $stmt->fetchAll();
    }

    /** Jobs awaiting admin review, oldest first, with poster name. */
    public static function pendingApproval(): array
    {
        return static::db()->query(
            "SELECT j.*, u.name AS poster_name FROM jobs j
             LEFT JOIN users u ON u.id = j.posted_by
             WHERE j.approval_status = 'pending'
             ORDER BY j.created_at ASC"
        )->fetchAll();
    }

    public static function countPendingApproval(): int
    {
        return (int) static::db()->query("SELECT COUNT(*) FROM jobs WHERE approval_status='pending'")->fetchColumn();
    }

    /** All jobs a given alumnus has posted themselves, regardless of status. */
    public static function postedByUser(int $userId): array
    {
        $stmt = static::db()->prepare('SELECT * FROM jobs WHERE posted_by = ? ORDER BY created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function approve(int $id): bool
    {
        $stmt = static::db()->prepare("UPDATE jobs SET approval_status='approved', posted_at = COALESCE(posted_at, NOW()) WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function reject(int $id): bool
    {
        $stmt = static::db()->prepare("UPDATE jobs SET approval_status='rejected' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function savedByUser(int $userId): array
    {
        $stmt = static::db()->prepare(
            "SELECT j.*, s.created_at AS saved_at FROM jobs j JOIN saved_jobs s ON s.job_id = j.id WHERE s.user_id = ? ORDER BY s.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        return static::insertRow('jobs', $data);
    }

    public static function update(int $id, array $data): bool
    {
        return static::updateRow('jobs', $id, $data);
    }

    /** True if the job is manually closed OR its closing date has passed. */
    public static function isClosed(array $job): bool
    {
        if (($job['status'] ?? 'open') === 'closed') {
            return true;
        }
        if (!empty($job['closing_date']) && strtotime($job['closing_date']) < strtotime('today')) {
            return true;
        }
        return false;
    }
}
