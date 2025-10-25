<?php

declare(strict_types=1);

namespace App\Core;

class Config
{
    private array $items = [];

    public function __construct(string $configPath)
    {
        $files = glob(rtrim($configPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.php');
        foreach ($files as $file) {
            $key = basename($file, '.php');
            $this->items[$key] = require $file;
        }
    }

    public function get(string $key, $default = null)
    {
        if (strpos($key, '.') === false) {
            return $this->items[$key] ?? $default;
        }

        [$file, $path] = explode('.', $key, 2);
        $value = $this->items[$file] ?? null;

        foreach (explode('.', $path) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}
