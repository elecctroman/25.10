<?php

declare(strict_types=1);

namespace App\Models;

class Setting extends Model
{
    protected static string $table = 'settings';

    public static function get(string $key, $default = null)
    {
        $stmt = self::db()->prepare('SELECT value FROM settings WHERE `key` = :key');
        $stmt->execute(['key' => $key]);
        $value = $stmt->fetchColumn();
        return $value !== false ? json_decode((string) $value, true) ?? $value : $default;
    }

    public static function set(string $key, $value): void
    {
        $encoded = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) : (string) $value;
        $stmt = self::db()->prepare('REPLACE INTO settings (`key`, value) VALUES (:key, :value)');
        $stmt->execute(['key' => $key, 'value' => $encoded]);
    }
}
