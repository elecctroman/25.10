<?php

use App\Core\Security;

global $container;

if (!isset($container) && isset($GLOBALS['container'])) {
    $container = $GLOBALS['container'];
}

$config = $container['config'] ?? null;
$appName = $config ? $config->get('app.name', 'Dijital Mağaza') : 'Dijital Mağaza';
$supportEmail = $config ? $config->get('mail.from_email', 'support@example.com') : 'support@example.com';
?>
    </div>
</main>

<footer class="bg-body-tertiary border-top mt-auto">
    <div class="container py-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <h6 class="text-uppercase text-muted mb-2">Hakkımızda</h6>
                <p class="mb-0 small">Dijital lisans, epin ve hesap satışlarında güvenilir adresiniz.</p>
            </div>
            <div class="col-md-4">
                <h6 class="text-uppercase text-muted mb-2">İletişim</h6>
                <ul class="list-unstyled small mb-0">
                    <li><a href="mailto:<?= Security::sanitize($supportEmail); ?>"><?= Security::sanitize($supportEmail); ?></a></li>
                    <li><a href="/destek">Destek Talebi</a></li>
                </ul>
            </div>
            <div class="col-md-4 text-md-end">
                <h6 class="text-uppercase text-muted mb-2">Bilgilendirme</h6>
                <a href="#" class="small me-3">Kullanım Şartları</a>
                <a href="#" class="small">Gizlilik Politikası</a>
            </div>
        </div>
        <div class="text-center mt-3 small text-muted">© <?= date('Y'); ?> <?= Security::sanitize($appName); ?>. Tüm hakları saklıdır.</div>
    </div>
</footer>

<?php include __DIR__ . '/modals.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="/assets/js/client.js?v=1" defer></script>
</body>
</html>
