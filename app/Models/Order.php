<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Order extends Model
{
    protected static string $table = 'orders';

    public static function findWithItems(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM orders WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $order = $stmt->fetch();
        if (!$order) {
            return null;
        }
        $order['items'] = OrderItem::byOrder($id);
        return $order;
    }

    public static function stats(): array
    {
        $pdo = self::db();
        $today = $pdo->query('SELECT SUM(total_gross) as total FROM orders WHERE DATE(created_at) = CURDATE() AND status IN ("paid", "delivered")')->fetch();
        $week = $pdo->query('SELECT SUM(total_gross) as total FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND status IN ("paid", "delivered")')->fetch();
        $month = $pdo->query('SELECT SUM(total_gross) as total FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND status IN ("paid", "delivered")')->fetch();
        return [
            'today' => (float) ($today['total'] ?? 0),
            'week' => (float) ($week['total'] ?? 0),
            'month' => (float) ($month['total'] ?? 0),
        ];
    }

    public static function recent(int $limit = 10): array
    {
        $stmt = self::db()->prepare('SELECT * FROM orders ORDER BY created_at DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
