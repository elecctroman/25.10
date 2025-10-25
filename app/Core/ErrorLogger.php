<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\ErrorLog;

class ErrorLogger
{
    public static function log(string $level, string $message, array $context = []): void
    {
        error_log(sprintf('[%s] %s: %s', strtoupper($level), date('c'), $message));
        ErrorLog::create([
            'level' => $level,
            'message' => $message,
            'context' => json_encode($context, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        ]);
    }
}
