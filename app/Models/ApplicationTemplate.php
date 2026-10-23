<?php

namespace App\Models;

use App\Core\Model;

class ApplicationTemplate extends Model
{
    protected static string $table = 'application_templates';

    public const FIELD_TYPES = ['text', 'textarea', 'email', 'phone', 'number', 'url', 'select', 'file'];

    /** profile_field key => human label, for the small "from profile" badge in the template builder. */
    public const PROFILE_FIELDS = [
        'name' => 'Full Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'linkedin_url' => 'LinkedIn Profile',
        'resume' => 'Resume',
        'education' => 'Education History',
        'experience' => 'Work Experience',
    ];

    /** Every template's fields are grouped under one of these three headings on the apply form. */
    public const SECTIONS = [
        'profile_info' => 'Profile Info',
        'education' => 'Education',
        'experience' => 'Experience',
    ];

    public static function forUser(int $userId): array
    {
        $stmt = static::db()->prepare('SELECT * FROM application_templates WHERE user_id = ? ORDER BY name ASC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Same as forUser(), but if the user has never created a template, seeds one first -
     * "Quick Apply", with the usual LinkedIn-Easy-Apply-style contact fields (name, email,
     * phone, LinkedIn, resume) bound to the applicant's own profile - so the Apply Method
     * dropdown's In-App Form option always has something ready to attach, not a dead end
     * that requires building a template from scratch before it can be used.
     */
    public static function forUserOrCreateDefault(int $userId): array
    {
        $templates = self::forUser($userId);
        if ($templates) {
            return $templates;
        }

        $id = self::create(['user_id' => $userId, 'name' => 'Quick Apply']);
        ApplicationTemplateField::replaceForTemplate($id, [
            ['label' => 'Full Name', 'field_type' => 'text', 'is_required' => true, 'profile_field' => 'name', 'section' => 'profile_info'],
            ['label' => 'Email', 'field_type' => 'email', 'is_required' => true, 'profile_field' => 'email', 'section' => 'profile_info'],
            ['label' => 'Phone', 'field_type' => 'phone', 'is_required' => true, 'profile_field' => 'phone', 'section' => 'profile_info'],
            ['label' => 'Resume', 'field_type' => 'file', 'is_required' => true, 'profile_field' => 'resume', 'section' => 'profile_info'],
            ['label' => 'LinkedIn Profile', 'field_type' => 'url', 'is_required' => false, 'profile_field' => 'linkedin_url', 'section' => 'profile_info'],
            ['label' => 'Education History', 'field_type' => 'textarea', 'is_required' => false, 'profile_field' => 'education', 'section' => 'education'],
            ['label' => 'Work Experience', 'field_type' => 'textarea', 'is_required' => false, 'profile_field' => 'experience', 'section' => 'experience'],
        ]);

        return self::forUser($userId);
    }

    /** Null unless the template belongs to $userId, so a poster can never load someone else's template by guessing its id. */
    public static function findForUser(int $id, int $userId): array|false
    {
        $stmt = static::db()->prepare('SELECT * FROM application_templates WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }

    public static function create(array $data): int
    {
        return static::insertRow('application_templates', $data);
    }

    public static function rename(int $id, string $name): bool
    {
        $stmt = static::db()->prepare('UPDATE application_templates SET name = ? WHERE id = ?');
        return $stmt->execute([$name, $id]);
    }
}
