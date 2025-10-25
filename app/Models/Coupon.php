<?php

declare(strict_types=1);

namespace App\Models;

class Coupon extends Model
{
    protected static string $table = 'coupons';

    public static function findByCode(string $code): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM coupons WHERE code = :code');
        $stmt->execute(['code' => $code]);
        $coupon = $stmt->fetch();
        return $coupon ?: null;
    }
}
