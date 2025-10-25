<?php use App\Core\Security; ?>
<?php include __DIR__ . '/partials/header.php'; ?>

<div class="row justify-content-center py-5">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 text-center mb-4">Hesabınıza Giriş Yapın</h1>
                <form method="post" action="/account/login" class="vstack gap-3" id="loginForm">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div>
                        <label class="form-label">E-posta</label>
                        <div class="input-group">
                            <span class="input-group-text">📧</span>
                            <input type="email" class="form-control" name="email" required autocomplete="email">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Parola</label>
                        <div class="input-group">
                            <span class="input-group-text">🔒</span>
                            <input type="password" class="form-control" name="password" required minlength="8" data-password>
                            <button class="btn btn-outline-secondary" type="button" data-toggle-password>Göster</button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="/account/forgot" class="small">Şifremi Unuttum</a>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Giriş Yap</button>
                </form>
                <p class="text-center small mt-4">Henüz hesabınız yok mu? <a href="/account/register">Hemen kayıt olun</a>.</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
