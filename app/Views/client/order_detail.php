<?php use App\Core\Security; ?>
<?php include __DIR__ . '/partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1">Sipariş #<?= Security::sanitize($order['order_no']); ?></h1>
        <p class="text-muted small mb-0">Oluşturma: <?= Security::sanitize(date('d.m.Y H:i', strtotime($order['created_at']))); ?></p>
    </div>
    <a href="/account/orders" class="btn btn-outline-secondary">Geri Dön</a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Sipariş Kalemleri</div>
            <div class="card-body">
                <?php foreach ($order['items'] as $item): ?>
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="fw-semibold"><?= Security::sanitize($item['product_name']); ?></div>
                                <?php if (!empty($item['variant_name'])): ?>
                                    <div class="text-muted small">Varyant: <?= Security::sanitize($item['variant_name']); ?></div>
                                <?php endif; ?>
                                <div class="text-muted small">Adet: <?= (int) $item['qty']; ?></div>
                            </div>
                            <div class="text-end">
                                <div class="fw-semibold"><?= number_format((float) $item['total'], 2); ?> <?= Security::sanitize($order['currency']); ?></div>
                                <div class="text-muted small">Vergi: <?= (float) $item['tax_rate']; ?>%</div>
                            </div>
                        </div>
                        <?php if (!empty($item['deliveries'])): ?>
                            <div class="mt-3">
                                <h6 class="small text-uppercase text-muted">Teslimatlar</h6>
                                <?php foreach ($item['deliveries'] as $delivery): ?>
                                    <div class="delivery-card border rounded p-2 mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-success text-uppercase"><?= Security::sanitize($delivery['type']); ?></span>
                                            <span class="small text-muted"><?= Security::sanitize(date('d.m.Y H:i', strtotime($delivery['delivered_at']))); ?></span>
                                        </div>
                                        <?php if ($delivery['type'] === 'code'): ?>
                                            <div class="input-group input-group-sm mt-2">
                                                <input type="text" class="form-control" value="<?= Security::sanitize($delivery['payload']); ?>" readonly>
                                                <button class="btn btn-outline-secondary" type="button" data-copy="<?= Security::sanitize($delivery['payload']); ?>">Kopyala</button>
                                            </div>
                                        <?php else: ?>
                                            <pre class="small mt-2 mb-0"><?= Security::sanitize($delivery['payload']); ?></pre>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning mt-3 mb-0">Teslimat henüz tamamlanmadı.</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Sipariş Özeti</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2"><span>Ara Toplam</span><strong><?= number_format((float) $order['total_net'], 2); ?> <?= Security::sanitize($order['currency']); ?></strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Vergi</span><strong><?= number_format((float) $order['total_tax'], 2); ?> <?= Security::sanitize($order['currency']); ?></strong></div>
                <div class="d-flex justify-content-between mb-2"><span>İndirim</span><strong class="text-success">-<?= number_format((float) ($order['discount_total'] ?? 0), 2); ?> <?= Security::sanitize($order['currency']); ?></strong></div>
                <hr>
                <div class="d-flex justify-content-between fs-5"><span>Ödenen</span><strong><?= number_format((float) $order['total_gross'], 2); ?> <?= Security::sanitize($order['currency']); ?></strong></div>
                <div class="mt-3">
                    <span class="badge bg-secondary">Durum: <?= Security::sanitize($order['status']); ?></span>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Teslimat Notu</div>
            <div class="card-body">
                <p class="small text-muted mb-0">Teslimatlar, ödemeden sonra otomatik olarak hesabınıza eklenir. Kopyaladığınız lisans kodlarını güvenli yerde saklayın.</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
