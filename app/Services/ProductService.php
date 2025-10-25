<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\AuditLogger;
use App\Models\LicenseKey;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductService
{
    public function create(array $data, array $variants = []): int
    {
        $userId = $data['created_by'] ?? null;
        unset($data['created_by'], $data['updated_by']);
        $productId = Product::create($data);
        $this->syncVariants($productId, $variants);
        AuditLogger::log($userId, 'product.create', 'product', $productId);
        return $productId;
    }

    public function update(int $id, array $data, array $variants = []): void
    {
        $userId = $data['updated_by'] ?? null;
        unset($data['created_by'], $data['updated_by']);
        Product::update($id, $data);
        $this->syncVariants($id, $variants);
        AuditLogger::log($userId, 'product.update', 'product', $id);
    }

    private function syncVariants(int $productId, array $variants): void
    {
        $existing = ProductVariant::byProduct($productId);
        $existingIds = array_column($existing, 'id');
        $submittedIds = [];
        foreach ($variants as $variant) {
            if (!empty($variant['id']) && in_array((int) $variant['id'], $existingIds, true)) {
                $submittedIds[] = (int) $variant['id'];
                ProductVariant::update((int) $variant['id'], [
                    'name' => $variant['name'],
                    'price_delta' => $variant['price_delta'],
                    'sku' => $variant['sku'],
                ]);
            } else {
                ProductVariant::create([
                    'product_id' => $productId,
                    'name' => $variant['name'],
                    'price_delta' => $variant['price_delta'],
                    'sku' => $variant['sku'],
                ]);
            }
        }
        foreach ($existingIds as $existingId) {
            if (!in_array($existingId, $submittedIds, true)) {
                ProductVariant::delete($existingId);
            }
        }
    }

    public function importKeys(int $productId, array $codes, ?int $userId = null): int
    {
        $count = 0;
        foreach ($codes as $code) {
            $code = trim($code);
            if ($code === '') {
                continue;
            }
            LicenseKey::create([
                'product_id' => $productId,
                'code' => $code,
                'status' => 'available',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $count++;
        }
        AuditLogger::log($userId, 'license.import', 'product', $productId, ['count' => $count]);
        return $count;
    }

    public function featured(int $limit = 8): array
    {
        global $container;
        /** @var \App\Core\Cache $cache */
        $cache = $container['cache'];
        return $cache->remember('featured_products_' . $limit, 600, fn() => Product::featured($limit));
    }

    public function list(array $filters, int $page = 1, int $perPage = 12): array
    {
        return Product::search($filters, $page, $perPage);
    }

    public function detail(string $slug): ?array
    {
        return Product::findBySlug($slug);
    }

    public function related(int $categoryId, int $excludeId, int $limit = 4): array
    {
        return Product::related($categoryId, $excludeId, $limit);
    }
}
