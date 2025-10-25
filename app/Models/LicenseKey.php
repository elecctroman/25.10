<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class LicenseKey extends Model
{
    protected static string $table = 'license_keys';

    public static function availableByProduct(int $productId, int $limit): array
    {
        $stmt = self::db()->prepare('SELECT * FROM license_keys WHERE product_id = :pid AND status = "available" ORDER BY id ASC LIMIT :limit');
        $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function assignToOrderItem(int $orderItemId, array $keys): void
    {
        $stmt = self::db()->prepare('UPDATE license_keys SET status = "sold", order_item_id = :order_item_id WHERE id = :id');
        foreach ($keys as $key) {
            $stmt->execute([
                'order_item_id' => $orderItemId,
                'id' => $key['id'],
            ]);
        }
    }

    public static function countAvailable(int $productId): int
    {
        $stmt = self::db()->prepare('SELECT COUNT(*) FROM license_keys WHERE product_id = :pid AND status = "available"');
        $stmt->execute(['pid' => $productId]);
        return (int) $stmt->fetchColumn();
    }
}
