<?php

declare(strict_types=1);

namespace App\Models;

class OrderItem extends Model
{
    protected static string $table = 'order_items';

    public static function byOrder(int $orderId): array
    {
        $stmt = self::db()->prepare('SELECT oi.*, p.name as product_name, pv.name as variant_name FROM order_items oi JOIN products p ON p.id = oi.product_id LEFT JOIN product_variants pv ON pv.id = oi.variant_id WHERE oi.order_id = :id');
        $stmt->execute(['id' => $orderId]);
        return $stmt->fetchAll();
    }
}
