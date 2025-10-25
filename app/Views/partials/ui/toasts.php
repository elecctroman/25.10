<?php

use App\Core\Security;

$flashMessages = $flashMessages ?? [];
if (!empty($flashMessages) && (!is_array($flashMessages) || !isset($flashMessages[0]))) {
    $flashMessages = [$flashMessages];
}
?>
<div class="toast-container position-fixed top-0 end-0 p-3" id="toast-container" aria-live="polite" aria-atomic="true">
    <?php foreach ($flashMessages as $toast): ?>
        <div class="toast text-bg-<?= Security::sanitize($toast['type'] ?? 'info'); ?> border-0 fade" role="status" data-bs-delay="3500" data-auto-init="toast">
            <div class="d-flex align-items-center">
                <div class="toast-body fw-medium">
                    <?= Security::sanitize($toast['text'] ?? ''); ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Kapat"></button>
            </div>
        </div>
    <?php endforeach; ?>
</div>
