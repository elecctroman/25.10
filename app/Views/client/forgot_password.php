<?php include __DIR__ . '/partials/header.php'; ?>

<div class="row justify-content-center py-5">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 text-center mb-3">Şifre Sıfırlama</h1>
                <p class="small text-muted text-center">E-posta adresinizi girin; sıfırlama bağlantısı gönderelim.</p>
                <form method="post" action="/account/forgot" class="vstack gap-3">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div>
                        <label class="form-label">E-posta</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Bağlantı Gönder</button>
                </form>
                <p class="text-center small mt-3"><a href="/account/login">Giriş ekranına dön</a></p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
