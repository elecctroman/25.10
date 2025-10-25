<?php

declare(strict_types=1);

namespace App\Integrations;

class StubProvider implements ProviderInterface
{
    public function getBalance(): array
    {
        return ['balance' => 1234.56, 'currency' => 'TRY'];
    }

    public function listProducts(): array
    {
        return [
            ['id' => 'stub-1', 'name' => 'Stub Lisans 1', 'price' => 10.0],
            ['id' => 'stub-2', 'name' => 'Stub Lisans 2', 'price' => 15.0],
        ];
    }

    public function placeOrder(array $payload): array
    {
        return ['status' => 'success', 'external_id' => 'SP' . time(), 'payload' => $payload];
    }

    public function getOrderStatus(string $externalId): array
    {
        return ['external_id' => $externalId, 'status' => 'delivered', 'delivered_at' => date('c')];
    }
}
