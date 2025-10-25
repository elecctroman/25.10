<?php use App\Core\Security; ?>
<?php include __DIR__ . '/partials/header.php'; ?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-header bg-transparent border-bottom-0 pb-0">
                <h2 class="h5 fw-semibold">Fatura &amp; İletişim</h2>
                <p class="small text-body-secondary mb-0">Ödeme sonrası dijital teslimatınız bu bilgilere göre yapılacaktır.</p>
            </div>
            <div class="card-body">
                <form method="post" action="/checkout" class="vstack gap-3" id="checkoutForm" novalidate>
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="checkoutName" name="name" value="<?= Security::sanitize($user['name'] ?? ''); ?>" <?= $user ? 'readonly' : 'required'; ?> placeholder="Adınız Soyadınız">
                        <label for="checkoutName">Ad Soyad</label>
                    </div>
                    <div class="form-floating">
                        <input type="email" class="form-control" id="checkoutEmail" name="email" value="<?= Security::sanitize($user['email'] ?? ''); ?>" required placeholder="eposta@example.com">
                        <label for="checkoutEmail">E-posta</label>
                    </div>
                    <div class="form-floating">
                        <textarea class="form-control" id="checkoutNote" name="note" style="height: 120px" placeholder="Siparişinize dair not ekleyin (opsiyonel)"></textarea>
                        <label for="checkoutNote">Notunuz (opsiyonel)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="terms" required>
                        <label class="form-check-label" for="terms">
                            Mesafeli satış sözleşmesini okudum ve kabul ediyorum.
                        </label>
                    </div>
                    <button class="btn btn-gradient w-100 d-flex justify-content-center align-items-center gap-2" type="submit">
                        <i class="bi bi-shield-lock"></i>
                        Siparişi Güvenle Tamamla
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 checkout-summary">
            <div class="card-header bg-transparent border-bottom-0">
                <h2 class="h5 fw-semibold mb-0">Sipariş Özeti</h2>
            </div>
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
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-patch-check text-primary fs-3"></i>
                <div class="small text-body-secondary">
                    <strong>Güvenli ödeme:</strong> SSL şifreleme ile korunur ve lisans anahtarlarınız siparişinizden hemen sonra hesabınıza tanımlanır.
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
