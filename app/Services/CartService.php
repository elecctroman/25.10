<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Models\Coupon;
use App\Models\LicenseKey;
use App\Models\Product;
use App\Models\ProductVariant;

class CartService
{
    private Session $session;
    private CouponService $couponService;

    public function __construct(Session $session, CouponService $couponService)
    {
        $this->session = $session;
        $this->couponService = $couponService;
    }

    public function items(): array
    {
        return $this->session->get('cart.items', []);
    }

    public function add(int $productId, ?int $variantId, int $qty): array
    {
        $cart = $this->items();
        $key = $this->key($productId, $variantId);
        $product = Product::find($productId);
        if (!$product || $product['status'] !== 'active') {
            throw new \RuntimeException('Ürün bulunamadı.');
        }
        $variant = null;
        $price = (float) $product['price'];
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if ($variant) {
                $price += (float) $variant['price_delta'];
            }
        }
        $stock = LicenseKey::countAvailable($productId);
        if ($stock > 0 && $qty > $stock) {
            $qty = $stock;
        }
        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
        } else {
            $cart[$key] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'name' => $product['name'],
                'variant_name' => $variant['name'] ?? null,
                'price' => $price,
                'tax_rate' => (float) $product['tax_rate'],
                'currency' => $product['currency'],
                'qty' => $qty,
                'slug' => $product['slug'],
            ];
        }
        $this->session->set('cart.items', $cart);
        return $cart[$key];
    }

    public function updateQuantity(string $key, int $qty): void
    {
        $cart = $this->items();
        if (!isset($cart[$key])) {
            return;
        }
        if ($qty <= 0) {
            unset($cart[$key]);
        } else {
            $stock = LicenseKey::countAvailable($cart[$key]['product_id']);
            if ($stock > 0 && $qty > $stock) {
                $qty = $stock;
            }
            $cart[$key]['qty'] = $qty;
        }
        $this->session->set('cart.items', $cart);
    }

    public function remove(string $key): void
    {
        $cart = $this->items();
        unset($cart[$key]);
        $this->session->set('cart.items', $cart);
    }

    public function clear(): void
    {
        $this->session->remove('cart.items');
        $this->session->remove('cart.coupon');
    }

    public function applyCoupon(string $code): array
    {
        if (trim($code) === '') {
            throw new \RuntimeException('Kupon kodu giriniz.');
        }
        $coupon = $this->couponService->validateForCart($code, $this->summary()['subtotal']);
        $this->session->set('cart.coupon', $coupon);
        return $coupon;
    }

    public function coupon(): ?array
    {
        $coupon = $this->session->get('cart.coupon');
        if (!$coupon) {
            return null;
        }
        return Coupon::findByCode($coupon['code']);
    }

    public function summary(): array
    {
        $items = $this->items();
        $subtotal = 0;
        $tax = 0;
        foreach ($items as $item) {
            $lineNet = $item['price'] * $item['qty'];
            $lineTax = $lineNet * ($item['tax_rate'] / 100);
            $subtotal += $lineNet;
            $tax += $lineTax;
        }
        $couponData = $this->session->get('cart.coupon');
        $discount = 0;
        if ($couponData) {
            $coupon = $this->coupon();
            if ($coupon && $coupon['status'] === 'active' && ($coupon['min_total'] === null || $subtotal >= (float) $coupon['min_total'])) {
                if ($coupon['type'] === 'percent') {
                    $discount = $subtotal * ((float) $coupon['value'] / 100);
                } else {
                    $discount = (float) $coupon['value'];
                }
                if (!empty($coupon['max_discount'])) {
                    $discount = min($discount, (float) $coupon['max_discount']);
                }
                $discount = min($discount, $subtotal);
            } else {
                $this->session->remove('cart.coupon');
            }
        }
        $total = max($subtotal - $discount + $tax, 0);
        return [
            'items' => $items,
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'discount' => round($discount, 2),
            'total' => round($total, 2),
            'currency' => $items ? $items[array_key_first($items)]['currency'] : 'TRY',
        ];
    }

    private function key(int $productId, ?int $variantId): string
    {
        return $productId . ':' . ($variantId ?? '0');
    }
}
