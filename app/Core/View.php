<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    public function render(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = $this->basePath . $template . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException('View not found: ' . $viewFile);
        }
        include $viewFile;
    }
}
