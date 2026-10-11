<?php

namespace App\Models;

use App\Core\Model;

class ProfileReport extends Model
{
    protected static string $table = 'profile_reports';

    public static function create(array $data): int
    {
        return static::insertRow('profile_reports', $data);
    }

    public static function allWithUsers(): array
    {
        return static::db()->query(
            "SELECT r.*, reporter.name AS reporter_name, reported.name AS reported_name, reported.id AS reported_id
             FROM profile_reports r
             JOIN users reporter ON reporter.id = r.reporter_id
             JOIN users reported ON reported.id = r.reported_user_id
             ORDER BY (r.status = 'pending') DESC, r.created_at DESC"
        )->fetchAll();
    }

    public static function markResolved(int $id): void
    {
        $stmt = static::db()->prepare("UPDATE profile_reports SET status = 'resolved', resolved_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function markDismissed(int $id): void
    {
        $stmt = static::db()->prepare("UPDATE profile_reports SET status = 'dismissed', resolved_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function countPending(): int
    {
        $stmt = static::db()->prepare("SELECT COUNT(*) FROM profile_reports WHERE status = 'pending'");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}
