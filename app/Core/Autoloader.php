<?php

declare(strict_types=1);

namespace App\Core;

class Autoloader
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    public function register(): void
    {
        spl_autoload_register([$this, 'load']);
    }

    private function load(string $class): void
    {
        if (strpos($class, 'App\\') !== 0) {
            return;
        }

        $relative = substr($class, 4);
        $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
        $file = $this->basePath . 'app' . DIRECTORY_SEPARATOR . $relativePath;

        if (file_exists($file)) {
            require_once $file;
        }
    }
}
