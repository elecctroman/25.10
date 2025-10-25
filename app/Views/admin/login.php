<?php $token = csrf_token(); ?>
<!doctype html>
<html lang="tr" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yönetici Girişi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/theme.css?v=1">
    <link rel="stylesheet" href="/assets/css/admin.css?v=2">
    <script>
        (function(){
            const storedTheme = localStorage.getItem('theme');
            if (storedTheme) {
                document.documentElement.setAttribute('data-bs-theme', storedTheme);
            }
        })();
    </script>
</head>
<body class="bg-body d-flex align-items-center min-vh-100">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <span class="brand-avatar rounded-circle bg-gradient-soft p-3 fs-4"><i class="bi bi-speedometer2 text-primary"></i></span>
                        <h1 class="h4 mt-3 mb-1 fw-semibold">Yönetici Girişi</h1>
                        <p class="text-body-secondary small">Kontrol paneline erişmek için bilgilerinizi girin.</p>
                    </div>
                    <?php if (!empty($flash)): ?>
                        <?php $flashMessages = is_array($flash) && isset($flash[0]) ? $flash : [$flash]; ?>
                        <?php include __DIR__ . '/../partials/ui/toasts.php'; ?>
                    <?php endif; ?>
                    <form method="post" action="/login" class="vstack gap-3" novalidate>
                        <input type="hidden" name="_token" value="<?= $token; ?>">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email" placeholder="eposta@example.com" required>
                            <label for="email">E-posta</label>
                        </div>
                        <div class="form-floating position-relative">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Parola" required>
                            <label for="password">Parola</label>
                            <button class="btn btn-sm btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2" type="button" data-toggle-password>Göster</button>
                        </div>
                        <button class="btn btn-gradient w-100" type="submit">Giriş Yap</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="toast-container position-fixed top-0 end-0 p-3" id="toast-container" aria-live="polite" aria-atomic="true"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/ui.js?v=1" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-toggle-password]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById('password');
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                button.textContent = isPassword ? 'Gizle' : 'Göster';
            });
        });
    });
</script>
</body>
</html>
