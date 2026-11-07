<?php

namespace App\Models;

use App\Core\Model;

class AlumniRoster extends Model
{
    protected static string $table = 'alumni_roster';

    public static function findByMatric(string $matricNumber): array|false
    {
        $stmt = static::db()->prepare('SELECT * FROM alumni_roster WHERE matric_number = ?');
        $stmt->execute([$matricNumber]);
        return $stmt->fetch();
    }

    public static function all(string $orderBy = 'created_at DESC'): array
    {
        return parent::all($orderBy);
    }

    public static function create(array $data): int
    {
        return static::insertRow('alumni_roster', $data);
    }

    public static function update(int $id, array $data): bool
    {
        return static::updateRow('alumni_roster', $id, $data);
    }

    public static function markClaimed(int $id, int $userId): void
    {
        static::updateRow('alumni_roster', $id, ['claimed_by_user_id' => $userId]);
    }

    /**
     * Upserts a batch of already-validated row data (matric_number/full_name/email/graduation_year/
     * cohort/program keys) inside a single transaction: a claimed matric number is left untouched
     * and counted as skipped; an unclaimed existing one is updated; a new one is inserted.
     *
     * @param array<int,array<string,mixed>> $rows
     * @return array{added:int,updated:int,skipped_claimed:int}
     */
    public static function importRows(array $rows): array
    {
        $added = 0;
        $updated = 0;
        $skippedClaimed = 0;

        $db = static::db();
        $db->beginTransaction();
        try {
            foreach ($rows as $data) {
                $existing = self::findByMatric($data['matric_number']);
                if ($existing && $existing['claimed_by_user_id']) {
                    $skippedClaimed++;
                    continue;
                }
                if ($existing) {
                    self::update((int) $existing['id'], $data);
                    $updated++;
                } else {
                    self::create($data);
                    $added++;
                }
            }
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }

        return ['added' => $added, 'updated' => $updated, 'skipped_claimed' => $skippedClaimed];
    }

    /** @return array{where:string,params:array<string,mixed>} shared WHERE clause for filtered()/countFiltered()/unclaimedFiltered() */
    private static function filterClause(array $filters): array
    {
        $where = [];
        $params = [];
        if (!empty($filters['cohort'])) {
            $where[] = 'cohort = :cohort';
            $params['cohort'] = $filters['cohort'];
        }
        if (!empty($filters['program'])) {
            $where[] = 'program = :program';
            $params['program'] = $filters['program'];
        }
        if (!empty($filters['graduation_year'])) {
            $where[] = 'graduation_year = :graduation_year';
            $params['graduation_year'] = $filters['graduation_year'];
        }
        return ['where' => $where ? 'WHERE ' . implode(' AND ', $where) : '', 'params' => $params];
    }

    public static function filtered(array $filters, int $limit, int $offset): array
    {
        $clause = self::filterClause($filters);
        $stmt = static::db()->prepare(
            "SELECT * FROM alumni_roster {$clause['where']} ORDER BY created_at DESC LIMIT " . max(1, $limit) . ' OFFSET ' . max(0, $offset)
        );
        $stmt->execute($clause['params']);
        return $stmt->fetchAll();
    }

    public static function countFiltered(array $filters): int
    {
        $clause = self::filterClause($filters);
        $stmt = static::db()->prepare("SELECT COUNT(*) FROM alumni_roster {$clause['where']}");
        $stmt->execute($clause['params']);
        return (int) $stmt->fetchColumn();
    }

    /** Unclaimed rows matching $filters that also have a syntactically valid email - the pool a "Register All Matching" click acts on. */
    public static function unclaimedFiltered(array $filters): array
    {
        $clause = self::filterClause($filters);
        $conditions = array_filter([$clause['where'] !== '' ? trim(substr($clause['where'], 6)) : null, 'claimed_by_user_id IS NULL', "email IS NOT NULL AND email != ''"]);
        $stmt = static::db()->prepare('SELECT * FROM alumni_roster WHERE ' . implode(' AND ', $conditions));
        $stmt->execute($clause['params']);
        return $stmt->fetchAll();
    }

    public static function distinctCohorts(): array
    {
        return static::db()->query('SELECT DISTINCT cohort FROM alumni_roster ORDER BY cohort ASC')->fetchAll(\PDO::FETCH_COLUMN);
    }

    public static function distinctPrograms(): array
    {
        return static::db()->query("SELECT DISTINCT program FROM alumni_roster WHERE program IS NOT NULL AND program != '' ORDER BY program ASC")->fetchAll(\PDO::FETCH_COLUMN);
    }

    public static function distinctGraduationYears(): array
    {
        return static::db()->query('SELECT DISTINCT graduation_year FROM alumni_roster ORDER BY graduation_year DESC')->fetchAll(\PDO::FETCH_COLUMN);
    }
}
