<?php

declare(strict_types=1);

namespace App\Core;

class Session
{
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_set_cookie_params([
                'httponly' => true,
                'samesite' => 'Lax',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
            ]);
            session_start();
        }
    }

    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function flash(string $key, $value = null)
    {
        if ($value === null) {
            if (!isset($_SESSION['_flash'][$key])) {
                return [];
            }

            $data = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);

            if (!is_array($data)) {
                return [$data];
            }

            if (function_exists('array_is_list') && array_is_list($data)) {
                return $data;
            }

            return [$data];
        }

        if (!isset($_SESSION['_flash'][$key])) {
            $_SESSION['_flash'][$key] = [];
        }

        if (!is_array($_SESSION['_flash'][$key]) || (function_exists('array_is_list') && !array_is_list($_SESSION['_flash'][$key]))) {
            $_SESSION['_flash'][$key] = [$_SESSION['_flash'][$key]];
        }

        $_SESSION['_flash'][$key][] = $value;

        return null;
    }

    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
}
