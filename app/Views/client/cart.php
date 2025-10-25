<?php use App\Core\Security; ?>
<?php include __DIR__ . '/partials/header.php'; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Sepetiniz</div>
            <div class="card-body">
                <?php if (empty($cart['items'])): ?>
                    <div class="alert alert-info">Sepetiniz boş. Ürünleri keşfetmek için <a href="/products">buraya tıklayın</a>.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                            <tr>
                                <th>Ürün</th>
                                <th class="text-center">Adet</th>
                                <th class="text-end">Birim Fiyat</th>
                                <th class="text-end">Toplam</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($cart['items'] as $key => $item): ?>
                                <tr data-cart-item="<?= Security::sanitize($key); ?>">
                                    <td>
                                        <div class="fw-semibold"><a href="/urun/<?= Security::sanitize($item['slug']); ?>" class="text-decoration-none"><?= Security::sanitize($item['name']); ?></a></div>
                                        <?php if (!empty($item['variant_name'])): ?>
                                            <div class="text-muted small">Varyant: <?= Security::sanitize($item['variant_name']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center" style="width: 160px;">
                                        <div class="input-group input-group-sm">
                                            <button class="btn btn-outline-secondary" type="button" data-cart-decrease>-</button>
                                            <input type="number" name="qty" class="form-control text-center" value="<?= (int) $item['qty']; ?>" min="1">
                                            <button class="btn btn-outline-secondary" type="button" data-cart-increase>+</button>
                                        </div>
                                    </td>
                                    <td class="text-end small"><?= number_format((float) $item['price'], 2); ?> <?= Security::sanitize($item['currency']); ?></td>
                                    <td class="text-end fw-semibold" data-line-total><?= number_format(($item['price'] * $item['qty']) + (($item['price'] * $item['qty']) * $item['tax_rate'] / 100), 2); ?> <?= Security::sanitize($item['currency']); ?></td>
                                    <td class="text-end"><button class="btn btn-sm btn-outline-danger" type="button" data-remove-item>&times;</button></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Özet</div>
            <div class="card-body" data-cart-summary>
                <div class="d-flex justify-content-between mb-2">
                    <span>Ara Toplam</span>
                    <strong><?= number_format((float) ($cart['subtotal'] ?? 0), 2); ?> <?= Security::sanitize($cart['currency'] ?? 'TRY'); ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Vergi</span>
                    <strong><?= number_format((float) ($cart['tax'] ?? 0), 2); ?> <?= Security::sanitize($cart['currency'] ?? 'TRY'); ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>İndirim</span>
                    <strong class="text-success">-<?= number_format((float) ($cart['discount'] ?? 0), 2); ?> <?= Security::sanitize($cart['currency'] ?? 'TRY'); ?></strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span>Genel Toplam</span>
                    <strong><?= number_format((float) ($cart['total'] ?? 0), 2); ?> <?= Security::sanitize($cart['currency'] ?? 'TRY'); ?></strong>
                </div>
                <form class="d-flex gap-2 mb-3" data-coupon-form>
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <input type="text" class="form-control" name="code" placeholder="Kupon kodu" required>
                    <button class="btn btn-outline-primary" type="submit">Uygula</button>
                </form>
                <a href="/checkout" class="btn btn-primary w-100 mb-2<?= empty($cart['items']) ? ' disabled' : ''; ?>">Ödemeye Geç</a>
                <a href="/products" class="btn btn-outline-secondary w-100">Alışverişe Devam Et</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
