<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

class Auth
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function attempt(string $email, string $password): bool
    {
        $user = User::findByEmail($email);
        if (!$user || $user['status'] !== 'active') {
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        $this->session->regenerate();
        $this->session->set('auth_user', [
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'role' => $user['role'],
        ]);

        AuditLogger::log((int) $user['id'], 'auth.login', 'user', (int) $user['id']);
        return true;
    }

    public function user(): ?array
    {
        return $this->session->get('auth_user');
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function logout(): void
    {
        $user = $this->user();
        if ($user) {
            AuditLogger::log((int) $user['id'], 'auth.logout', 'user', (int) $user['id']);
        }
        $this->session->destroy();
    }

    public function requireRole(array $roles): void
    {
        $user = $this->user();
        if (!$user || !in_array($user['role'], $roles, true)) {
            header('Location: /login');
            exit;
        }
    }
}
