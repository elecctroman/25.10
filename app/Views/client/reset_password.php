<?php use App\Core\Security; ?>
<?php include __DIR__ . '/partials/header.php'; ?>

<div class="row justify-content-center py-5">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 text-center mb-4">Yeni Parola Belirleyin</h1>
                <form method="post" action="/account/reset" class="vstack gap-3">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <input type="hidden" name="token" value="<?= Security::sanitize($token ?? ''); ?>">
                    <div>
                        <label class="form-label">Yeni Parola</label>
                        <input type="password" class="form-control" name="password" minlength="8" required>
                    </div>
                    <div>
                        <label class="form-label">Parola Tekrar</label>
                        <input type="password" class="form-control" name="password_confirmation" minlength="8" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Parolayı Güncelle</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
