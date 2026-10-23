<?php

namespace App\Models;

use App\Core\Model;

class ApplicationTemplateField extends Model
{
    protected static string $table = 'application_template_fields';

    public static function forTemplate(int $templateId): array
    {
        $stmt = static::db()->prepare('SELECT * FROM application_template_fields WHERE template_id = ? ORDER BY sort_order ASC, id ASC');
        $stmt->execute([$templateId]);
        return $stmt->fetchAll();
    }

    /**
     * Replaces every field on a template in one go (delete-then-reinsert), which keeps the
     * template builder's "add/remove rows freely, then save" UI simple - there's no need to
     * diff which rows are new/changed/removed, since existing applications keep their own
     * label/type/value snapshot in job_applications.responses rather than referencing these
     * rows live.
     */
    public static function replaceForTemplate(int $templateId, array $fields): void
    {
        $db = static::db();
        $db->prepare('DELETE FROM application_template_fields WHERE template_id = ?')->execute([$templateId]);

        $stmt = $db->prepare(
            'INSERT INTO application_template_fields (template_id, label, field_type, options, is_required, sort_order, profile_field, section)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        foreach ($fields as $i => $field) {
            $stmt->execute([
                $templateId,
                $field['label'],
                $field['field_type'],
                $field['options'] ?? null,
                $field['is_required'] ? 1 : 0,
                $i,
                $field['profile_field'] ?? null,
                $field['section'] ?? 'profile_info',
            ]);
        }
    }
}
