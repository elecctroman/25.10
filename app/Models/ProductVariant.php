<?php

declare(strict_types=1);

namespace App\Models;

class ProductVariant extends Model
{
    protected static string $table = 'product_variants';

    public static function byProduct(int $productId): array
    {
        $stmt = self::db()->prepare('SELECT * FROM product_variants WHERE product_id = :id ORDER BY id ASC');
        $stmt->execute(['id' => $productId]);
        return $stmt->fetchAll();
    }
}
