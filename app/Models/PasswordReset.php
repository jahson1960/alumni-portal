<?php

namespace App\Models;

use App\Core\Model;

/**
 * Self-service "forgot password" tokens. The plain token is only ever held in memory long enough
 * to email it - the DB stores its sha256 hash, so a DB leak alone can't be used to reset accounts
 * (the same reasoning as App\Core\Csrf, adapted for storage that must survive across requests).
 */
class PasswordReset extends Model
{
    protected static string $table = 'password_resets';

    private const EXPIRY_MINUTES = 60;

    /** Creates a fresh token for $userId (invalidating any previous unused ones) and returns the plain token to email. */
    public static function create(int $userId): string
    {
        $stmt = static::db()->prepare('DELETE FROM password_resets WHERE user_id = ? AND used_at IS NULL');
        $stmt->execute([$userId]);

        $token = bin2hex(random_bytes(32));
        $stmt = static::db()->prepare(
            'INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (:user_id, :token_hash, :expires_at)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'token_hash' => hash('sha256', $token),
            'expires_at' => date('Y-m-d H:i:s', time() + self::EXPIRY_MINUTES * 60),
        ]);

        return $token;
    }

    /** The reset row for $token if it exists, isn't used, and hasn't expired - false otherwise. */
    public static function findValidByToken(string $token): array|false
    {
        $stmt = static::db()->prepare(
            'SELECT * FROM password_resets WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW()'
        );
        $stmt->execute([hash('sha256', $token)]);
        return $stmt->fetch();
    }

    public static function markUsed(int $id): void
    {
        static::updateRow('password_resets', $id, ['used_at' => date('Y-m-d H:i:s')]);
    }
}
