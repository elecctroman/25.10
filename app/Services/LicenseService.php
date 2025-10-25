<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\AuditLogger;
use App\Models\LicenseKey;

class LicenseService
{
    public function bulkUpdateStatus(array $ids, string $status, ?int $userId = null): void
    {
        foreach ($ids as $id) {
            LicenseKey::update((int) $id, ['status' => $status]);
        }
        AuditLogger::log($userId, 'license.bulk_status', 'license', null, ['ids' => $ids, 'status' => $status]);
    }
}
