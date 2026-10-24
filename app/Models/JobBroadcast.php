<?php

namespace App\Models;

use App\Core\Model;

class JobBroadcast extends Model
{
    protected static string $table = 'job_broadcasts';

    public const STATUS_LABELS = [
        'all' => 'All Applicants',
        'new' => 'Applied',
        'reviewed' => 'Reviewed',
        'shortlisted' => 'Shortlisted',
        'interview_scheduled' => 'Interview Scheduled',
        'rejected' => 'Rejected',
    ];

    public static function forJob(int $jobId): array
    {
        $stmt = static::db()->prepare('SELECT * FROM job_broadcasts WHERE job_id = ? ORDER BY created_at DESC');
        $stmt->execute([$jobId]);
        return $stmt->fetchAll();
    }

    public static function create(int $jobId, int $senderId, string $statusFilter, string $body): int
    {
        return static::insertRow('job_broadcasts', [
            'job_id' => $jobId,
            'sender_id' => $senderId,
            'status_filter' => $statusFilter,
            'body' => $body,
        ]);
    }

    public static function setRecipientCount(int $id, int $count): void
    {
        static::updateRow('job_broadcasts', $id, ['recipient_count' => $count]);
    }

    /** Records the message/notification created for one recipient of a broadcast, so recall() can remove exactly those rows later. */
    public static function addRecipient(int $broadcastId, int $userId, int $messageId, int $notificationId): void
    {
        static::insertRow('job_broadcast_recipients', [
            'broadcast_id' => $broadcastId,
            'user_id' => $userId,
            'message_id' => $messageId,
            'notification_id' => $notificationId,
        ]);
    }

    public static function recipients(int $broadcastId): array
    {
        $stmt = static::db()->prepare('SELECT * FROM job_broadcast_recipients WHERE broadcast_id = ?');
        $stmt->execute([$broadcastId]);
        return $stmt->fetchAll();
    }

    /**
     * "Recall" a broadcast: deletes the individual message and notification it created for
     * every recipient (removing it from their inboxes) and marks the broadcast itself as
     * recalled, keeping its body as a record of what was sent for the poster's own history.
     */
    public static function recall(int $id): void
    {
        foreach (self::recipients($id) as $recipient) {
            if ($recipient['message_id']) {
                $stmt = static::db()->prepare('DELETE FROM messages WHERE id = ?');
                $stmt->execute([(int) $recipient['message_id']]);
            }
            if ($recipient['notification_id']) {
                $stmt = static::db()->prepare('DELETE FROM notifications WHERE id = ?');
                $stmt->execute([(int) $recipient['notification_id']]);
            }
        }
        static::updateRow('job_broadcasts', $id, ['recalled_at' => date('Y-m-d H:i:s')]);
    }
}
