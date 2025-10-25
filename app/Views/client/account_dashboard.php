<?php use App\Core\Security; ?>
<?php include __DIR__ . '/partials/header.php'; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body text-center">
                <div class="avatar mb-3">👤</div>
                <h2 class="h5 mb-1"><?= Security::sanitize($user['name'] ?? ''); ?></h2>
                <p class="text-muted small mb-0"><?= Security::sanitize($user['email'] ?? ''); ?></p>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Profil Bilgileri</div>
            <div class="card-body">
                <form method="post" action="/account/profile" class="vstack gap-3">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div>
                        <label class="form-label">Ad Soyad</label>
                        <input type="text" class="form-control" name="name" value="<?= Security::sanitize($user['name'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label class="form-label">E-posta</label>
                        <input type="email" class="form-control" name="email" value="<?= Security::sanitize($user['email'] ?? ''); ?>" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Güncelle</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <span class="stat-label">Sipariş</span>
                    <span class="stat-value"><?= (int) ($stats['orders'] ?? 0); ?></span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <span class="stat-label">Bekleyen</span>
                    <span class="stat-value text-warning"><?= (int) ($stats['pending'] ?? 0); ?></span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <span class="stat-label">Tamamlanan</span>
                    <span class="stat-value text-success"><?= (int) ($stats['completed'] ?? 0); ?></span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <span class="stat-label">Toplam Harcama</span>
                    <span class="stat-value"><?= number_format((float) ($stats['spent'] ?? 0), 2); ?> ₺</span>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">Parola Güncelle</div>
            <div class="card-body">
                <form method="post" action="/account/password" class="vstack gap-3">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div>
                        <label class="form-label">Mevcut Parola</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div>
                        <label class="form-label">Yeni Parola</label>
                        <input type="password" class="form-control" name="new_password" minlength="8" required>
                    </div>
                    <div>
                        <label class="form-label">Yeni Parola (Tekrar)</label>
                        <input type="password" class="form-control" name="new_password_confirmation" minlength="8" required>
                    </div>
                    <button class="btn btn-outline-primary w-100" type="submit">Parolayı Değiştir</button>
                </form>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Hızlı Erişim</div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="/account/orders" class="btn btn-light">Siparişlerim</a>
                    <a href="/cart" class="btn btn-light">Sepeti Gör</a>
                    <a href="/destek" class="btn btn-light">Destek Talebi</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
