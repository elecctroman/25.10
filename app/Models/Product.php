<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Product extends Model
{
    protected static string $table = 'products';

    public static function active(): array
    {
        $stmt = self::db()->query('SELECT * FROM products WHERE status = "active" ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = self::db()->prepare('SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.slug = :slug AND p.status = "active" LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $product = $stmt->fetch();
        if (!$product) {
            return null;
        }
        $product['variants'] = ProductVariant::byProduct((int) $product['id']);
        return $product;
    }

    public static function featured(int $limit = 8): array
    {
        $stmt = self::db()->prepare('SELECT * FROM products WHERE status = "active" AND is_featured = 1 ORDER BY created_at DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function related(int $categoryId, int $excludeId, int $limit = 4): array
    {
        $stmt = self::db()->prepare('SELECT * FROM products WHERE status = "active" AND category_id = :cat AND id <> :id ORDER BY created_at DESC LIMIT :limit');
        $stmt->bindValue(':cat', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function search(array $filters, int $page, int $perPage): array
    {
        $conditions = ['status = "active"'];
        $params = [];
        if (!empty($filters['category_id'])) {
            $conditions[] = 'category_id = :category_id';
            $params['category_id'] = (int) $filters['category_id'];
        }
        if (!empty($filters['type'])) {
            $conditions[] = 'type = :type';
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['q'])) {
            $conditions[] = '(name LIKE :q_name OR description LIKE :q_desc)';
            $likeTerm = '%' . $filters['q'] . '%';
            $params['q_name'] = $likeTerm;
            $params['q_desc'] = $likeTerm;
        }
        if (!empty($filters['min_price'])) {
            $conditions[] = 'price >= :min_price';
            $params['min_price'] = (float) $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $conditions[] = 'price <= :max_price';
            $params['max_price'] = (float) $filters['max_price'];
        }
        $where = implode(' AND ', $conditions);
        $offset = ($page - 1) * $perPage;
        $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM products WHERE ' . $where . ' ORDER BY created_at DESC LIMIT :offset, :limit';
        $stmt = self::db()->prepare($sql);
        foreach ($params as $key => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(':' . $key, $value, $paramType);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();
        $total = (int) self::db()->query('SELECT FOUND_ROWS()')->fetchColumn();
        return [
            'data' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
        ];
    }

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
