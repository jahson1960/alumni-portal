<?php

namespace App\Core;

use App\Models\User;

class Auth
{
    private static ?array $userCache = null;

    public static function attempt(string $email, string $password): bool
    {
        $user = User::findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['status'] !== 'active') {
                return false;
            }
            self::login($user);
            return true;
        }

        return false;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        self::$userCache = $user;
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['role']);
        session_regenerate_id(true);
        self::$userCache = null;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin(): bool
    {
        return self::check() && ($_SESSION['role'] ?? '') === 'admin';
    }

    public static function isAlumni(): bool
    {
        return self::check() && ($_SESSION['role'] ?? '') === 'alumni';
    }

    public static function isEditor(): bool
    {
        return self::check() && ($_SESSION['role'] ?? '') === 'editor';
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        if (self::$userCache === null) {
            $user = User::find((int) $_SESSION['user_id']);
            self::$userCache = $user ?: null;
        }
        return self::$userCache;
    }

    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
}
