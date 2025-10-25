<?php

declare(strict_types=1);

namespace App\Core;

class RateLimiter
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function check(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        $attempts = $this->session->get('_rate_' . $key, []);
        $now = time();
        $attempts = array_filter($attempts, fn($timestamp) => $timestamp > $now - $decaySeconds);
        if (count($attempts) >= $maxAttempts) {
            $this->session->set('_rate_' . $key, $attempts);
            return false;
        }
        $attempts[] = $now;
        $this->session->set('_rate_' . $key, $attempts);
        return true;
    }
}
