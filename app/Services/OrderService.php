<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\AuditLogger;
use App\Core\ErrorLogger;
use App\Models\Delivery;
use App\Models\LicenseKey;
use App\Models\Order;
use App\Models\OrderItem;

class OrderService
{
    public function create(array $orderData, array $items, ?int $userId = null): int
    {
        $orderId = Order::create($orderData);
        $totalNet = 0;
        $totalTax = 0;
        foreach ($items as $item) {
            $item['order_id'] = $orderId;
            $itemId = OrderItem::create($item);
            $totalNet += $item['unit_price'] * $item['qty'];
            $totalTax += ($item['unit_price'] * $item['qty']) * ($item['tax_rate'] / 100);
            if (($orderData['delivery_strategy'] ?? '') === 'auto_key') {
                $keys = LicenseKey::availableByProduct((int) $item['product_id'], (int) $item['qty']);
                if (count($keys) === (int) $item['qty']) {
                    LicenseKey::assignToOrderItem($itemId, $keys);
                    $this->createDeliveryFromKeys($itemId, $keys);
                } else {
                    ErrorLogger::log('warning', 'Insufficient keys for order ' . $orderId);
                }
            }
        }
        $discount = (float) ($orderData['discount_total'] ?? 0);
        Order::update($orderId, [
            'total_net' => $totalNet,
            'total_tax' => $totalTax,
            'total_gross' => max($totalNet - $discount + $totalTax, 0),
            'discount_total' => $discount,
        ]);
        AuditLogger::log($userId, 'order.create', 'order', $orderId, ['items' => count($items)]);
        return $orderId;
    }

    public function createFromCart(array $customer, array $cartSummary, array $items, array $options = []): int
    {
        $orderNo = 'ORD' . date('YmdHis') . random_int(100, 999);
        $orderData = [
            'order_no' => $orderNo,
            'customer_id' => $customer['id'] ?? null,
            'email' => $customer['email'],
            'total_net' => 0,
            'total_tax' => 0,
            'total_gross' => 0,
            'discount_total' => $cartSummary['discount'],
            'currency' => $cartSummary['currency'],
            'status' => 'paid',
            'delivery_strategy' => $options['delivery_strategy'] ?? 'auto_key',
            'created_at' => date('Y-m-d H:i:s'),
            'paid_at' => date('Y-m-d H:i:s'),
            'delivered_at' => null,
            'coupon_id' => $options['coupon_id'] ?? null,
        ];
        $orderId = $this->create($orderData, $items, $customer['id'] ?? null);
        if ($options['mark_delivered'] ?? false) {
            Order::update($orderId, ['status' => 'delivered', 'delivered_at' => date('Y-m-d H:i:s')]);
        }
        return $orderId;
    }

    public function updateStatus(int $orderId, string $status, ?int $userId = null): void
    {
        Order::update($orderId, ['status' => $status]);
        AuditLogger::log($userId, 'order.status', 'order', $orderId, ['status' => $status]);
    }

    public function addDelivery(int $orderItemId, string $type, string $payload, ?int $userId = null): void
    {
        Delivery::create([
            'order_item_id' => $orderItemId,
            'type' => $type,
            'payload' => $payload,
            'delivered_at' => date('Y-m-d H:i:s'),
        ]);
        AuditLogger::log($userId, 'delivery.create', 'order_item', $orderItemId, ['type' => $type]);
    }

    private function createDeliveryFromKeys(int $orderItemId, array $keys): void
    {
        $codes = array_column($keys, 'code');
        $payload = implode("\n", $codes);
        Delivery::create([
            'order_item_id' => $orderItemId,
            'type' => 'code',
            'payload' => $payload,
            'delivered_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
