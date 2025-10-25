<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\AuditLog;

class AuditLogger
{
    public static function log(?int $userId, string $action, string $entity, ?int $entityId, array $context = []): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'cli';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'cli';
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'ip' => $ip,
            'ua' => $ua,
            'context' => json_encode($context, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        ]);
    }
}
