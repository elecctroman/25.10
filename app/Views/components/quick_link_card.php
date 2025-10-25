<?php
use App\Core\Security;

$title = $title ?? '';
$href = $href ?? '#';
$icon = $icon ?? 'bi-arrow-right-circle';
$description = $description ?? '';
?>
<a class="quick-link-card text-decoration-none text-body" href="<?= Security::sanitize($href); ?>">
    <div class="d-flex align-items-center gap-3">
        <span class="avatar-sm bg-white bg-opacity-75 text-primary"><i class="bi <?= Security::sanitize($icon); ?>"></i></span>
        <div>
            <span class="fw-semibold d-block mb-1"><?= Security::sanitize($title); ?></span>
            <span class="small text-body-secondary d-block"><?= Security::sanitize($description); ?></span>
        </div>
    </div>
</a>
