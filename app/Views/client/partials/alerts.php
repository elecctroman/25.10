<?php

use App\Core\Security;

?>
<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= Security::sanitize($flash['type'] ?? 'info'); ?> alert-dismissible fade show" role="alert">
        <?= Security::sanitize($flash['text'] ?? ''); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
    </div>
<?php endif; ?>
<div class="toast-container position-fixed top-0 end-0 p-3" id="toast-container" style="z-index: 1080;"></div>
