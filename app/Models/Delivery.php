<?php

declare(strict_types=1);

namespace App\Models;

class Delivery extends Model
{
    protected static string $table = 'deliveries';

    public static function byOrderItem(int $orderItemId): array
    {
        $stmt = self::db()->prepare('SELECT * FROM deliveries WHERE order_item_id = :id ORDER BY id ASC');
        $stmt->execute(['id' => $orderItemId]);
        return $stmt->fetchAll();
    }
}
