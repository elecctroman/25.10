<?php include __DIR__ . '/partials/header.php'; ?>

<div class="row justify-content-center py-5">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Destek Talebi</h1>
                <p class="text-muted small">Sorularınızı ve sorunlarınızı bize iletin, kısa sürede dönüş yapalım.</p>
                <form method="post" action="/destek" class="vstack gap-3">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div>
                        <label class="form-label">E-posta</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div>
                        <label class="form-label">Konu</label>
                        <input type="text" class="form-control" name="subject" required>
                    </div>
                    <div>
                        <label class="form-label">Mesaj</label>
                        <textarea class="form-control" name="message" rows="5" required></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Gönder</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
