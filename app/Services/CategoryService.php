<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Cache;
use App\Models\Category;
use App\Models\Product;

class CategoryService
{
    private Cache $cache;

    public function __construct()
    {
        global $container;
        /** @var Cache $cache */
        $cache = $container['cache'];
        $this->cache = $cache;
    }

    public function topCategories(int $limit = 6): array
    {
        return $this->cache->remember('top_categories_' . $limit, 600, function () use ($limit) {
            $categories = Category::active();
            $counts = [];
            foreach ($categories as $category) {
                $counts[$category['id']] = 0;
            }
            $products = Product::active();
            foreach ($products as $product) {
                if (isset($counts[$product['category_id']])) {
                    $counts[$product['category_id']]++;
                }
            }
            usort($categories, function ($a, $b) use ($counts) {
                return ($counts[$b['id']] ?? 0) <=> ($counts[$a['id']] ?? 0);
            });
            return array_slice($categories, 0, $limit);
        });
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->cache->remember('category_' . $slug, 600, function () use ($slug) {
            return Category::findBySlug($slug);
        });
    }

    public function menuTree(): array
    {
        return $this->cache->remember('category_menu', 600, fn() => Category::tree());
    }
}
