<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\AuditLogger;
use App\Models\Setting;

class SettingService
{
    public function save(array $settings, ?int $userId = null): void
    {
        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
        AuditLogger::log($userId, 'settings.update', 'setting', null, ['keys' => array_keys($settings)]);
    }
}
