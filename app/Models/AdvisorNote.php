<?php

namespace App\Models;

use App\Core\Model;

/** Private advising notes - each one visible only to the advisor who wrote it, and to admin. */
class AdvisorNote extends Model
{
    protected static string $table = 'advisor_notes';

    public static function create(int $advisorId, int $alumniId, string $note): int
    {
        return static::insertRow('advisor_notes', [
            'advisor_id' => $advisorId,
            'alumni_id' => $alumniId,
            'note' => $note,
        ]);
    }

    /** An advisor's own notes about one alumnus, newest first - what the advisor sees. */
    public static function forAdvisorAndAlumni(int $advisorId, int $alumniId): array
    {
        $stmt = static::db()->prepare(
            'SELECT * FROM advisor_notes WHERE advisor_id = ? AND alumni_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$advisorId, $alumniId]);
        return $stmt->fetchAll();
    }

    /** Every advisor's notes about one alumnus, joined with the advisor's name - what admin sees. */
    public static function forAlumni(int $alumniId): array
    {
        $stmt = static::db()->prepare(
            'SELECT n.*, u.name AS advisor_name FROM advisor_notes n
             JOIN users u ON u.id = n.advisor_id
             WHERE n.alumni_id = ? ORDER BY n.created_at DESC'
        );
        $stmt->execute([$alumniId]);
        return $stmt->fetchAll();
    }
}
