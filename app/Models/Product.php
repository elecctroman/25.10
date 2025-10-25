<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Product extends Model
{
    protected static string $table = 'products';

    public static function withCategory(): array
    {
        $stmt = self::db()->query('SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC');
        return $stmt->fetchAll();
    }

    public static function findWithRelations(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = :id');
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();
        if (!$product) {
            return null;
        }
        $product['variants'] = ProductVariant::byProduct($id);
        return $product;
    }

    public static function topSelling(int $limit = 5): array
    {
        $sql = 'SELECT p.name, SUM(oi.qty) as total_qty, SUM(oi.total) as total_amount FROM order_items oi JOIN products p ON p.id = oi.product_id GROUP BY oi.product_id ORDER BY total_qty DESC LIMIT :limit';
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
