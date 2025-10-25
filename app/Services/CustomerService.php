<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\AuditLogger;
use App\Core\Mailer;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\PasswordReset;
use App\Models\User;

class CustomerService
{
    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function register(array $data): int
    {
        $userId = User::create([
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'name' => $data['name'],
            'role' => 'customer',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        AuditLogger::log($userId, 'customer.register', 'user', $userId);
        return $userId;
    }

    public function updateProfile(int $userId, array $data): void
    {
        $fields = [];
        if (isset($data['name'])) {
            $fields['name'] = $data['name'];
        }
        if (isset($data['email'])) {
            $fields['email'] = $data['email'];
        }
        if (!$fields) {
            return;
        }
        $fields['updated_at'] = date('Y-m-d H:i:s');
        User::update($userId, $fields);
        AuditLogger::log($userId, 'customer.update_profile', 'user', $userId);
    }

    public function changePassword(int $userId, string $current, string $password): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }
        if (!password_verify($current, $user['password_hash'])) {
            return false;
        }
        User::update($userId, ['password_hash' => password_hash($password, PASSWORD_DEFAULT), 'updated_at' => date('Y-m-d H:i:s')]);
        AuditLogger::log($userId, 'customer.change_password', 'user', $userId);
        return true;
    }

    public function createResetToken(string $email): ?string
    {
        $user = User::findByEmail($email);
        if (!$user || $user['role'] !== 'customer') {
            return null;
        }
        $token = bin2hex(random_bytes(32));
        PasswordReset::create(["email" => $email, "token" => $token, "expires_at" => date('Y-m-d H:i:s', time() + 3600)]);
        $body = "Merhaba,\n\nŞifre sıfırlama bağlantınız: " . url('/account/reset?token=' . $token) . "\n\nBu bağlantı 60 dakika geçerlidir.";
        $this->mailer->send($email, 'Şifre Sıfırlama Talebi', $body);
        AuditLogger::log((int) $user['id'], 'customer.reset_request', 'user', (int) $user['id']);
        return $token;
    }

    public function resetPassword(string $token, string $password): bool
    {
        $reset = PasswordReset::findValid($token);
        if (!$reset) {
            return false;
        }
        $user = User::findByEmail($reset['email']);
        if (!$user) {
            return false;
        }
        User::update((int) $user['id'], ['password_hash' => password_hash($password, PASSWORD_DEFAULT), 'updated_at' => date('Y-m-d H:i:s')]);
        PasswordReset::consume($token);
        AuditLogger::log((int) $user['id'], 'customer.reset_password', 'user', (int) $user['id']);
        return true;
    }

    public function dashboardStats(int $userId): array
    {
        $orders = Order::byCustomer($userId);
        $total = 0;
        $pending = 0;
        $completed = 0;
        foreach ($orders as $order) {
            $total += (float) $order['total_gross'];
            if ($order['status'] === 'pending') {
                $pending++;
            }
            if ($order['status'] === 'delivered' || $order['status'] === 'paid') {
                $completed++;
            }
        }
        return [
            'orders' => count($orders),
            'pending' => $pending,
            'completed' => $completed,
            'spent' => $total,
        ];
    }

    public function orderDetail(int $orderId, int $userId): ?array
    {
        $order = Order::findWithItems($orderId);
        if (!$order || (int) $order['customer_id'] !== $userId) {
            return null;
        }
        foreach ($order['items'] as &$item) {
            $item['deliveries'] = Delivery::byOrderItem((int) $item['id']);
        }
        return $order;
    }
}
