<?php
use App\Core\Security;

$icon = $icon ?? 'bi-speedometer';
$label = $label ?? '';
$value = $value ?? '';
$badge = $badge ?? null;
$chartValues = $chartValues ?? [];
?>
<div class="admin-stat-card position-relative overflow-hidden">
    <?php if ($badge): ?>
        <span class="badge text-bg-light text-primary-emphasis"><?= Security::sanitize($badge); ?></span>
    <?php endif; ?>
    <div class="d-flex align-items-center gap-3">
        <span class="avatar-sm fs-4 text-primary bg-white bg-opacity-50"><i class="bi <?= Security::sanitize($icon); ?>"></i></span>
        <div>
            <span class="text-body-secondary small text-uppercase d-block"><?= Security::sanitize($label); ?></span>
            <span class="stat-value d-block mt-1"><?= Security::sanitize($value); ?></span>
        </div>
    </div>
    <?php if (!empty($chartValues)): ?>
        <div class="mini-chart mt-3">
            <?php foreach ($chartValues as $height): ?>
                <span style="height: <?= max(10, min(100, (int) $height)); ?>%"></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
