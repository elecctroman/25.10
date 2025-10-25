<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\AuditLogger;
use App\Models\Coupon;
use App\Models\Order;

class CouponService
{
    public function create(array $data, ?int $userId = null): int
    {
        $id = Coupon::create($data);
        AuditLogger::log($userId, 'coupon.create', 'coupon', $id);
        return $id;
    }

    public function update(int $id, array $data, ?int $userId = null): void
    {
        Coupon::update($id, $data);
        AuditLogger::log($userId, 'coupon.update', 'coupon', $id);
    }

    public function validateForCart(string $code, float $subtotal): array
    {
        $coupon = Coupon::findByCode($code);
        if (!$coupon || $coupon['status'] !== 'active') {
            throw new \RuntimeException('Kupon bulunamadı veya aktif değil.');
        }
        $now = new \DateTimeImmutable();
        if (!empty($coupon['start_at']) && $now < new \DateTimeImmutable($coupon['start_at'])) {
            throw new \RuntimeException('Kupon henüz kullanılamaz.');
        }
        if (!empty($coupon['end_at']) && $now > new \DateTimeImmutable($coupon['end_at'])) {
            throw new \RuntimeException('Kupon süresi doldu.');
        }
        if (!empty($coupon['min_total']) && $subtotal < (float) $coupon['min_total']) {
            throw new \RuntimeException('Kupon için sepet tutarı yetersiz.');
        }
        if (!empty($coupon['usage_limit'])) {
            $usage = Order::countByCoupon((int) $coupon['id']);
            if ($usage >= (int) $coupon['usage_limit']) {
                throw new \RuntimeException('Kupon kullanım limiti doldu.');
            }
        }
        return $coupon;
    }
}
