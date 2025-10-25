<?php

declare(strict_types=1);

namespace App\Core;

class Cache
{
    private string $path;

    public function __construct(string $basePath)
    {
        $this->path = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!is_dir($this->path)) {
            mkdir($this->path, 0775, true);
        }
    }

    public function get(string $key)
    {
        $file = $this->filePath($key);
        if (!file_exists($file)) {
            return null;
        }
        $payload = json_decode((string) file_get_contents($file), true);
        if (!is_array($payload)) {
            return null;
        }
        if (($payload['expires_at'] ?? 0) < time()) {
            unlink($file);
            return null;
        }
        return $payload['value'] ?? null;
    }

    public function put(string $key, $value, int $ttl): void
    {
        $file = $this->filePath($key);
        file_put_contents($file, json_encode([
            'expires_at' => time() + $ttl,
            'value' => $value,
        ], JSON_UNESCAPED_UNICODE));
    }

    public function remember(string $key, int $ttl, callable $callback)
    {
        $cached = $this->get($key);
        if ($cached !== null) {
            return $cached;
        }
        $value = $callback();
        $this->put($key, $value, $ttl);
        return $value;
    }

    public function forget(string $key): void
    {
        $file = $this->filePath($key);
        if (file_exists($file)) {
            unlink($file);
        }
    }

    private function filePath(string $key): string
    {
        $safeKey = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $key);
        return $this->path . $safeKey . '.cache';
    }
}
