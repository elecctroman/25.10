<?php use App\Core\Security; ?>
<?php include __DIR__ . '/partials/header.php'; ?>

<div class="text-center py-5">
    <div class="display-1 text-success mb-3">✔</div>
    <h1 class="h3 mb-3">Siparişiniz Alındı!</h1>
    <p class="lead">Sipariş numaranız <strong>#<?= (int) $order_id; ?></strong>. Teslimat detayları kısa süre içinde hesabınıza ve e-postanıza iletilecek.</p>
    <div class="card shadow-sm mx-auto mt-4" style="max-width: 480px;">
        <div class="card-body text-start">
            <h5 class="card-title">Ödeme Özeti</h5>
            <ul class="list-unstyled small mb-3">
                <li>Ara Toplam: <?= number_format((float) ($summary['subtotal'] ?? 0), 2); ?> <?= Security::sanitize($summary['currency'] ?? 'TRY'); ?></li>
                <li>Vergi: <?= number_format((float) ($summary['tax'] ?? 0), 2); ?> <?= Security::sanitize($summary['currency'] ?? 'TRY'); ?></li>
                <li>İndirim: -<?= number_format((float) ($summary['discount'] ?? 0), 2); ?> <?= Security::sanitize($summary['currency'] ?? 'TRY'); ?></li>
                <li><strong>Toplam: <?= number_format((float) ($summary['total'] ?? 0), 2); ?> <?= Security::sanitize($summary['currency'] ?? 'TRY'); ?></strong></li>
            </ul>
            <a href="/account/orders" class="btn btn-primary w-100 mb-2">Siparişlerimi Gör</a>
            <a href="/products" class="btn btn-outline-secondary w-100">Alışverişe Devam Et</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
