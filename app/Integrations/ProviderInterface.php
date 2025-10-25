<?php

declare(strict_types=1);

namespace App\Integrations;

interface ProviderInterface
{
    public function getBalance(): array;
    public function listProducts(): array;
    public function placeOrder(array $payload): array;
    public function getOrderStatus(string $externalId): array;
}
