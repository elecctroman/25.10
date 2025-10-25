<?php use App\Core\Security; ?>
<?php include __DIR__ . '/partials/header.php'; ?>

<div class="row justify-content-center py-5">
    <div class="col-md-7 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 text-center mb-4">Yeni Hesap Oluşturun</h1>
                <form method="post" action="/account/register" class="vstack gap-3" id="registerForm">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div>
                        <label class="form-label">Ad Soyad</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div>
                        <label class="form-label">E-posta</label>
                        <input type="email" class="form-control" name="email" required autocomplete="email">
                    </div>
                    <div>
                        <label class="form-label">Parola</label>
                        <div class="input-group">
                            <span class="input-group-text">🔒</span>
                            <input type="password" class="form-control" name="password" minlength="8" required data-password>
                            <button class="btn btn-outline-secondary" type="button" data-toggle-password>Göster</button>
                        </div>
                        <div class="form-text">En az 8 karakter, harf ve rakam içermelidir.</div>
                    </div>
                    <div>
                        <label class="form-label">Parola (Tekrar)</label>
                        <input type="password" class="form-control" name="password_confirmation" minlength="8" required>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="termsRegister" required>
                        <label class="form-check-label" for="termsRegister">Kullanım şartlarını kabul ediyorum.</label>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Kayıt Ol</button>
                </form>
                <p class="text-center small mt-4">Zaten hesabınız var mı? <a href="/account/login">Giriş yapın</a>.</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
