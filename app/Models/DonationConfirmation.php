<?php

namespace App\Models;

use App\Core\Model;

class DonationConfirmation extends Model
{
    protected static string $table = 'donation_confirmations';

    public static function create(int $userId, int $campaignId): int
    {
        return static::insertRow('donation_confirmations', [
            'user_id' => $userId,
            'campaign_id' => $campaignId,
        ]);
    }

    /** Campaign ids a user has already marked as given, for a "✓ Marked as Given" state. */
    public static function confirmedCampaignIdsFor(int $userId): array
    {
        $stmt = static::db()->prepare('SELECT DISTINCT campaign_id FROM donation_confirmations WHERE user_id = ?');
        $stmt->execute([$userId]);
        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    public static function forCampaign(int $campaignId): array
    {
        $stmt = static::db()->prepare(
            "SELECT d.*, u.name AS user_name, u.avatar AS user_avatar FROM donation_confirmations d
             JOIN users u ON u.id = d.user_id
             WHERE d.campaign_id = ? ORDER BY d.created_at DESC"
        );
        $stmt->execute([$campaignId]);
        return $stmt->fetchAll();
    }

    /** All confirmations site-wide, joined with donor + campaign names, for the admin reconciliation list. */
    public static function all(string $orderBy = 'd.created_at DESC'): array
    {
        return static::db()->query(
            "SELECT d.*, u.name AS user_name, u.email AS user_email, c.title AS campaign_title
             FROM donation_confirmations d
             JOIN users u ON u.id = d.user_id
             JOIN campaigns c ON c.id = d.campaign_id
             ORDER BY {$orderBy}"
        )->fetchAll();
    }
}
