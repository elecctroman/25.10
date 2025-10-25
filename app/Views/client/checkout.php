<?php use App\Core\Security; ?>
<?php include __DIR__ . '/partials/header.php'; ?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Fatura Bilgileri</div>
            <div class="card-body">
                <form method="post" action="/checkout" class="vstack gap-3" id="checkoutForm">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div>
                        <label class="form-label">Ad Soyad</label>
                        <input type="text" class="form-control" name="name" value="<?= Security::sanitize($user['name'] ?? ''); ?>" <?= $user ? 'readonly' : 'required'; ?>>
                    </div>
                    <div>
                        <label class="form-label">E-posta</label>
                        <input type="email" class="form-control" name="email" value="<?= Security::sanitize($user['email'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label class="form-label">Notunuz</label>
                        <textarea class="form-control" name="note" rows="3" placeholder="Siparişinize dair not ekleyin (opsiyonel)"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="terms" required>
                        <label class="form-check-label" for="terms">
                            Mesafeli satış sözleşmesini okudum ve kabul ediyorum.
                        </label>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Siparişi Tamamla</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Sipariş Özeti</div>
            <div class="card-body">
                <?php foreach ($cart['items'] as $item): ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <div class="fw-semibold"><?= Security::sanitize($item['name']); ?></div>
                            <div class="text-muted small">Adet: <?= (int) $item['qty']; ?></div>
                        </div>
                        <div><?= number_format((float) $item['price'], 2); ?> <?= Security::sanitize($item['currency']); ?></div>
                    </div>
                <?php endforeach; ?>
                <hr>
                <div class="d-flex justify-content-between"><span>Ara Toplam</span><strong><?= number_format((float) ($cart['subtotal'] ?? 0), 2); ?> <?= Security::sanitize($cart['currency']); ?></strong></div>
                <div class="d-flex justify-content-between"><span>Vergi</span><strong><?= number_format((float) ($cart['tax'] ?? 0), 2); ?> <?= Security::sanitize($cart['currency']); ?></strong></div>
                <div class="d-flex justify-content-between"><span>İndirim</span><strong class="text-success">-<?= number_format((float) ($cart['discount'] ?? 0), 2); ?> <?= Security::sanitize($cart['currency']); ?></strong></div>
                <hr>
                <div class="d-flex justify-content-between fs-5"><span>Ödenecek Tutar</span><strong><?= number_format((float) ($cart['total'] ?? 0), 2); ?> <?= Security::sanitize($cart['currency']); ?></strong></div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Güvenlik</div>
            <div class="card-body small text-muted">
                <ul class="list-unstyled mb-0">
                    <li>• Ödemeler güvenli SSL sertifikası ile korunur.</li>
                    <li>• Dijital teslimatlar anında hesabınıza iletilir.</li>
                    <li>• Kupon ve kampanyalar ödeme esnasında uygulanır.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
