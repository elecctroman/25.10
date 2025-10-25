<?php

declare(strict_types=1);

namespace App\Models;

class PasswordReset extends Model
{
    protected static string $table = 'password_resets';

    public static function findValid(string $token): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW() LIMIT 1');
        $stmt->execute(['token' => $token]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function consume(string $token): void
    {
        $stmt = self::db()->prepare('DELETE FROM password_resets WHERE token = :token');
        $stmt->execute(['token' => $token]);
    }
}
