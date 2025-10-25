<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\AuditLogger;
use App\Models\Coupon;

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
}
