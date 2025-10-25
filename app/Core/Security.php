<?php

declare(strict_types=1);

namespace App\Core;

class Security
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function csrfToken(): string
    {
        $existing = $this->session->get('_csrf');
        if (is_string($existing)) {
            return $existing;
        }
        $token = bin2hex(random_bytes(16));
        $this->session->set('_csrf', $token);
        return $token;
    }

    public function verifyCsrf(string $token): bool
    {
        $sessionToken = $this->session->get('_csrf');
        $this->session->remove('_csrf');
        return is_string($sessionToken) && hash_equals($sessionToken, $token);
    }

    public static function sanitize(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function sanitizeArray(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $sanitized[$key] = is_string($value) ? self::sanitize($value) : $value;
        }
        return $sanitized;
    }
}
