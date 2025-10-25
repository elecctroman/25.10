<?php

declare(strict_types=1);

namespace App\Models;

class Category extends Model
{
    protected static string $table = 'categories';

    public static function tree(): array
    {
        $items = self::all();
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
