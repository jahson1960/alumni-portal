<?php

namespace App\Models;

use App\Core\Model;

class ResumeReview extends Model
{
    protected static string $table = 'resume_reviews';

    public static function create(int $requesterId, ?string $resumeUrl, ?string $resumeLink, string $message): int
    {
        return static::insertRow('resume_reviews', [
            'requester_id' => $requesterId,
            'resume_url' => $resumeUrl,
            'resume_link' => $resumeLink,
            'message' => $message !== '' ? $message : null,
        ]);
    }

    public static function forRequester(int $requesterId): array
    {
        $stmt = static::db()->prepare(
            "SELECT r.*, u.name AS reviewer_name, u.avatar AS reviewer_avatar, u.headline AS reviewer_headline
             FROM resume_reviews r
             LEFT JOIN users u ON u.id = r.reviewer_id
             WHERE r.requester_id = ? ORDER BY r.created_at DESC"
        );
        $stmt->execute([$requesterId]);
        return $stmt->fetchAll();
    }

    /** Pending requests no mentor has claimed yet, oldest first — the reviewer queue. */
    public static function pendingUnclaimed(): array
    {
        return static::db()->query(
            "SELECT r.*, u.name AS requester_name, u.avatar AS requester_avatar, u.headline AS requester_headline
             FROM resume_reviews r
             JOIN users u ON u.id = r.requester_id
             WHERE r.status = 'pending'
             ORDER BY r.created_at ASC"
        )->fetchAll();
    }

    /** A mentor's own claimed-but-not-yet-completed reviews. */
    public static function claimedBy(int $reviewerId): array
    {
        $stmt = static::db()->prepare(
            "SELECT r.*, u.name AS requester_name, u.avatar AS requester_avatar, u.headline AS requester_headline
             FROM resume_reviews r
             JOIN users u ON u.id = r.requester_id
             WHERE r.reviewer_id = ? AND r.status = 'claimed'
             ORDER BY r.claimed_at ASC"
        );
        $stmt->execute([$reviewerId]);
        return $stmt->fetchAll();
    }

    public static function findOwnedByReviewer(int $id, int $reviewerId): array|false
    {
        $stmt = static::db()->prepare('SELECT * FROM resume_reviews WHERE id = ? AND reviewer_id = ?');
        $stmt->execute([$id, $reviewerId]);
        return $stmt->fetch();
    }

    /** Atomically claims a still-pending request — returns false if another mentor already claimed it first. */
    public static function claim(int $id, int $reviewerId): bool
    {
        $stmt = static::db()->prepare(
            "UPDATE resume_reviews SET reviewer_id = ?, status = 'claimed', claimed_at = NOW() WHERE id = ? AND status = 'pending'"
        );
        $stmt->execute([$reviewerId, $id]);
        return $stmt->rowCount() > 0;
    }

    public static function submitFeedback(int $id, int $reviewerId, string $feedback): bool
    {
        $stmt = static::db()->prepare(
            "UPDATE resume_reviews SET status = 'completed', feedback = ?, completed_at = NOW() WHERE id = ? AND reviewer_id = ? AND status = 'claimed'"
        );
        $stmt->execute([$feedback, $id, $reviewerId]);
        return $stmt->rowCount() > 0;
    }
}
