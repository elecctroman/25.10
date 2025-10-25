<?php

declare(strict_types=1);

namespace App\Models;

class Category extends Model
{
    protected static string $table = 'categories';

    public static function active(): array
    {
        $stmt = self::db()->query('SELECT * FROM categories WHERE status = "active" ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM categories WHERE slug = :slug AND status = "active" LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function tree(): array
    {
        $items = self::active();
        $byParent = [];
        foreach ($items as $item) {
            $byParent[$item['parent_id'] ?? 0][] = $item;
        }
        return self::buildTree($byParent, null);
    }

    private static function buildTree(array $byParent, ?int $parentId): array
    {
        $children = $byParent[$parentId ?? 0] ?? [];
        foreach ($children as &$child) {
            $child['children'] = self::buildTree($byParent, (int) $child['id']);
        }
        return $children;
    }
}
