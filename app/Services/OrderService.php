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
        Order::update($orderId, [
            'total_net' => $totalNet,
            'total_tax' => $totalTax,
            'total_gross' => $totalNet + $totalTax,
        ]);
        AuditLogger::log($userId, 'order.create', 'order', $orderId, ['items' => count($items)]);
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
