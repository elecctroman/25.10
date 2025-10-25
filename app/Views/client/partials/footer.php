<?php

use App\Core\Security;

global $container;

if (!isset($container) && isset($GLOBALS['container'])) {
    $container = $GLOBALS['container'];
}

$config = $container['config'] ?? null;
$appName = $config ? $config->get('app.name', 'Dijital Mağaza') : 'Dijital Mağaza';
$supportEmail = $config ? $config->get('mail.from_email', 'support@example.com') : 'support@example.com';
$footerLinks = [
    ['label' => 'Kullanım Şartları', 'href' => '#'],
    ['label' => 'Gizlilik Politikası', 'href' => '#'],
    ['label' => 'Destek', 'href' => '/destek'],
];
?>
    </div>
</main>

<footer class="app-footer mt-auto py-5 bg-body-tertiary border-top">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-md-4">
                <h6 class="text-uppercase text-muted mb-3">Hakkımızda</h6>
                <p class="mb-3 small text-body-secondary">Dijital lisans, epin ve hesap satışlarında güven veren kurumsal çözüm ortağınız.</p>
                <div class="d-flex align-items-center gap-2 text-body-secondary">
                    <i class="bi bi-envelope"></i>
                    <a class="link-underline-opacity-0" href="mailto:<?= Security::sanitize($supportEmail); ?>"><?= Security::sanitize($supportEmail); ?></a>
                </div>
            </div>
            <div class="col-md-4">
                <h6 class="text-uppercase text-muted mb-3">Hızlı Menü</h6>
                <ul class="list-unstyled vstack gap-2 mb-0">
                    <?php foreach ($footerLinks as $link): ?>
                        <li><a class="link-secondary" href="<?= Security::sanitize($link['href']); ?>"><?= Security::sanitize($link['label']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="text-uppercase text-muted mb-3">Güncel Kampanyalar</h6>
                <div class="promo-card border rounded-4 p-3 bg-gradient-soft shadow-sm">
                    <p class="mb-1 fw-semibold">Yeni Üyelere %10 İndirim</p>
                    <span class="badge bg-primary-subtle text-primary-emphasis">Kodu: HOSGELDIN</span>
                </div>
            </div>
        </div>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top small text-body-secondary">
            <span>© <?= date('Y'); ?> <?= Security::sanitize($appName); ?> · Tüm hakları saklıdır.</span>
            <div class="d-flex align-items-center gap-3 mt-2 mt-md-0">
                <span><i class="bi bi-shield-check me-1"></i>Güvenli Ödeme</span>
                <span><i class="bi bi-phone me-1"></i>7/24 Destek</span>
            </div>
        </div>
    </div>
</footer>

<nav class="bottom-nav d-lg-none bg-body-tertiary border-top">
    <a class="bottom-nav__item" href="/">
        <i class="bi bi-house"></i>
        <span>Ana Sayfa</span>
    </a>
    <a class="bottom-nav__item" href="/products">
        <i class="bi bi-collection"></i>
        <span>Ürünler</span>
    </a>
    <a class="bottom-nav__item" href="/cart">
        <i class="bi bi-cart3"></i>
        <span>Sepet</span>
        <span class="badge rounded-pill bg-danger" data-cart-count><?= $cartCount; ?></span>
    </a>
    <a class="bottom-nav__item" href="<?= $customer ? '/account' : '/account/login'; ?>">
        <i class="bi bi-person-circle"></i>
        <span><?= $customer ? 'Hesabım' : 'Giriş'; ?></span>
    </a>
</nav>

<?php include __DIR__ . '/modals.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="/assets/js/ui.js?v=1" defer></script>
<script src="/assets/js/client.js?v=2" defer></script>
</body>
</html>
