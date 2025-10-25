<?php use App\Core\Security; ?>
<?php include __DIR__ . '/partials/header.php'; ?>

<div class="row justify-content-center py-5">
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="row g-0 align-items-center">
                <div class="col-lg-5 bg-gradient-soft h-100 p-4 d-none d-lg-flex flex-column justify-content-center">
                    <h2 class="h4 fw-semibold text-primary mb-3">Dakikalar İçinde Başlayın</h2>
                    <ul class="list-unstyled small text-body-secondary vstack gap-2">
                        <li><i class="bi bi-check2-circle text-primary me-2"></i>Hızlı dijital teslimat</li>
                        <li><i class="bi bi-check2-circle text-primary me-2"></i>Kupon ve kampanyalara erişim</li>
                        <li><i class="bi bi-check2-circle text-primary me-2"></i>Özel müşteri desteği</li>
                    </ul>
                </div>
                <div class="col-lg-7 p-4 p-lg-5">
                    <h1 class="h4 text-center mb-4 fw-semibold">Yeni Hesap Oluşturun</h1>
                    <form method="post" action="/account/register" class="vstack gap-3" id="registerForm" novalidate>
                        <input type="hidden" name="_token" value="<?= $csrf; ?>">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="registerName" name="name" placeholder="Adınız Soyadınız" required>
                            <label for="registerName">Ad Soyad</label>
                        </div>
                        <div class="form-floating">
                            <input type="email" class="form-control" id="registerEmail" name="email" placeholder="eposta@example.com" required autocomplete="email">
                            <label for="registerEmail">E-posta</label>
                        </div>
                        <div class="form-floating position-relative">
                            <input type="password" class="form-control" id="registerPassword" name="password" minlength="8" required placeholder="Parola" data-password>
                            <label for="registerPassword">Parola</label>
                            <button class="btn btn-sm btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2" type="button" data-toggle-password>Göster</button>
                            <div class="form-text mt-2">En az 8 karakter, harf ve rakam içermelidir.</div>
                        </div>
                        <div class="form-floating">
                            <input type="password" class="form-control" id="registerPasswordConfirm" name="password_confirmation" minlength="8" required placeholder="Parola Tekrar">
                            <label for="registerPasswordConfirm">Parola (Tekrar)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="termsRegister" required>
                            <label class="form-check-label" for="termsRegister">Kullanım şartlarını kabul ediyorum.</label>
                        </div>
                        <button class="btn btn-gradient w-100" type="submit">Kayıt Ol</button>
                    </form>
                    <p class="text-center small mt-4 mb-0">Zaten hesabınız var mı? <a href="/account/login">Giriş yapın</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
