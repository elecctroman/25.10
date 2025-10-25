<?php
use App\Core\Security;
$security = $security ?? null;
$authUser = auth_user();
?>
<!doctype html>
<html lang="tr" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Yönetim Paneli'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="d-flex">
    <nav class="sidebar bg-dark text-white p-3">
        <div class="d-flex align-items-center mb-4">
            <span class="fs-4 fw-bold">Panel</span>
        </div>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item"><a class="nav-link<?= ($_SERVER['REQUEST_URI'] === '/admin/dashboard') ? ' active' : ''; ?>" href="/admin/dashboard">Dashboard</a></li>
            <li><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/categories') ? ' active' : ''; ?>" href="/admin/categories">Kategoriler</a></li>
            <li><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/products') ? ' active' : ''; ?>" href="/admin/products">Ürünler</a></li>
            <li><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/licenses') ? ' active' : ''; ?>" href="/admin/licenses">Lisans Havuzu</a></li>
            <li><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/orders') ? ' active' : ''; ?>" href="/admin/orders">Siparişler</a></li>
            <li><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/customers') ? ' active' : ''; ?>" href="/admin/customers">Müşteriler</a></li>
            <li><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/coupons') ? ' active' : ''; ?>" href="/admin/coupons">Kuponlar</a></li>
            <li><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/providers') ? ' active' : ''; ?>" href="/admin/providers">Sağlayıcılar</a></li>
            <?php if (($authUser['role'] ?? '') === 'admin'): ?>
            <li><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/settings') ? ' active' : ''; ?>" href="/admin/settings">Ayarlar</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="content-wrapper flex-grow-1">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <button class="btn btn-outline-secondary d-lg-none" data-toggle="sidebar">Menü</button>
            <div>
                <h1 class="h4 mb-0"><?= htmlspecialchars($title ?? ''); ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Anasayfa</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($title ?? ''); ?></li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small"><?= htmlspecialchars($authUser['name'] ?? ''); ?></span>
                <form method="post" action="/logout">
                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>">
                    <button class="btn btn-outline-danger btn-sm" type="submit">Çıkış</button>
                </form>
                <button class="btn btn-outline-secondary btn-sm" type="button" data-toggle="theme">Tema</button>
            </div>
        </header>
        <?php if (!empty($flash)): ?>
            <div class="alert alert-<?= $flash['type']; ?>"><?= htmlspecialchars($flash['text']); ?></div>
        <?php endif; ?>
        <main>
            <?= $slot ?? ''; ?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/admin.js"></script>
</body>
</html>
