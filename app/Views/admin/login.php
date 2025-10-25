<?php $token = csrf_token(); ?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yönetici Girişi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="h4 mb-3 text-center">Yönetici Girişi</h1>
                    <?php if (!empty($flash)): ?>
                        <div class="alert alert-<?= $flash['type']; ?>"> <?= htmlspecialchars($flash['text']); ?> </div>
                    <?php endif; ?>
                    <form method="post" action="/login" novalidate>
                        <input type="hidden" name="_token" value="<?= $token; ?>">
                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Parola</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Giriş Yap</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
