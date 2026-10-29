<?php

namespace App\Models;

use App\Core\Model;

/** Admin-managed, many-to-many assignments of advisor accounts to the alumni they advise. */
class Advisor extends Model
{
    protected static string $table = 'advisor_assignments';

    public static function allAdvisors(): array
    {
        return static::db()->query("SELECT * FROM users WHERE role = 'advisor' AND status = 'active' ORDER BY name ASC")->fetchAll();
    }

    /** @return int[] */
    public static function advisorIdsForAlumni(int $alumniId): array
    {
        $stmt = static::db()->prepare('SELECT advisor_id FROM advisor_assignments WHERE alumni_id = ?');
        $stmt->execute([$alumniId]);
        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    public static function isAssigned(int $advisorId, int $alumniId): bool
    {
        $stmt = static::db()->prepare('SELECT 1 FROM advisor_assignments WHERE advisor_id = ? AND alumni_id = ? LIMIT 1');
        $stmt->execute([$advisorId, $alumniId]);
        return (bool) $stmt->fetchColumn();
    }

    /** @param int[] $advisorIds the full desired set of advisors for this alumnus */
    public static function syncAdvisorsForAlumni(int $alumniId, array $advisorIds): void
    {
        $db = static::db();
        $db->prepare('DELETE FROM advisor_assignments WHERE alumni_id = ?')->execute([$alumniId]);
        $stmt = $db->prepare('INSERT INTO advisor_assignments (advisor_id, alumni_id) VALUES (?, ?)');
        foreach (array_unique(array_filter(array_map('intval', $advisorIds), fn ($id) => $id > 0)) as $advisorId) {
            $stmt->execute([$advisorId, $alumniId]);
        }
    }

    /** Advisors currently assigned to an alumnus, for the admin alumni page. */
    public static function advisorsForAlumni(int $alumniId): array
    {
        $stmt = static::db()->prepare(
            'SELECT u.* FROM users u
             JOIN advisor_assignments aa ON aa.advisor_id = u.id
             WHERE aa.alumni_id = ? ORDER BY u.name ASC'
        );
        $stmt->execute([$alumniId]);
        return $stmt->fetchAll();
    }

    /** Alumni currently assigned to an advisor, for the advisor's "My Assigned Alumni" list. */
    public static function alumniForAdvisor(int $advisorId): array
    {
        $stmt = static::db()->prepare(
            'SELECT u.* FROM users u
             JOIN advisor_assignments aa ON aa.alumni_id = u.id
             WHERE aa.advisor_id = ? ORDER BY u.name ASC'
        );
        $stmt->execute([$advisorId]);
        return $stmt->fetchAll();
    }
}
