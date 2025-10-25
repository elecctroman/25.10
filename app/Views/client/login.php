<?php use App\Core\Security; ?>
<?php include __DIR__ . '/partials/header.php'; ?>

<div class="row justify-content-center py-5">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="row g-0 align-items-stretch">
                <div class="col-lg-5 d-none d-lg-flex bg-gradient-primary text-white flex-column justify-content-center p-4">
                    <h2 class="h4 fw-semibold mb-3">Tek Hesap, Tüm Lisanslar</h2>
                    <p class="small text-white-75">Siparişlerinizi yönetin, lisans anahtarlarınıza anında erişin ve kampanyalardan ilk siz haberdar olun.</p>
                </div>
                <div class="col-lg-7 p-4 p-lg-5">
                    <h1 class="h4 text-center mb-4 fw-semibold">Hesabınıza Giriş Yapın</h1>
                    <form method="post" action="/account/login" class="vstack gap-3" id="loginForm" novalidate>
                        <input type="hidden" name="_token" value="<?= $csrf; ?>">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="loginEmail" name="email" placeholder="eposta@example.com" required autocomplete="email">
                            <label for="loginEmail">E-posta</label>
                        </div>
                        <div class="form-floating position-relative">
                            <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Parola" required minlength="8" data-password>
                            <label for="loginPassword">Parola</label>
                            <button class="btn btn-sm btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2" type="button" data-toggle-password>Göster</button>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small">
                            <a href="/account/forgot" class="link-secondary">Şifremi Unuttum</a>
                            <span class="text-body-secondary">Minimum 8 karakter</span>
                        </div>
                        <button class="btn btn-gradient w-100" type="submit">Giriş Yap</button>
                    </form>
                    <p class="text-center small mt-4 mb-0">Henüz hesabınız yok mu? <a href="/account/register">Hemen kayıt olun</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
