<?php
use App\Core\Security;

$security = $security ?? null;
$authUser = auth_user();
$flashMessages = $flash ?? [];
?>
<!doctype html>
<html lang="tr" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Yönetim Paneli'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/theme.css?v=1">
    <link rel="stylesheet" href="/assets/css/admin.css?v=2">
    <script>
        (function() {
            const storedTheme = localStorage.getItem('theme');
            if (storedTheme) {
                document.documentElement.setAttribute('data-bs-theme', storedTheme);
            }
        })();
    </script>
</head>
<body class="bg-body" data-app-shell>
<?php include __DIR__ . '/../partials/ui/loader.php'; ?>
<div class="admin-shell">
    <aside class="admin-sidebar" data-sidebar>
        <div class="d-flex align-items-center gap-3 mb-4">
            <span class="brand-avatar rounded-circle bg-white bg-opacity-25 p-3"><i class="bi bi-speedometer2"></i></span>
            <div>
                <span class="d-block fw-semibold">Yönetim</span>
                <small class="text-white-50">Kontrol Paneli</small>
            </div>
        </div>
        <ul class="nav flex-column gap-2">
            <li><a class="nav-link<?= ($_SERVER['REQUEST_URI'] === '/admin/dashboard') ? ' active' : ''; ?>" href="/admin/dashboard"><i class="bi bi-graph-up"></i>Dashboard</a></li>
            <li><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/categories') ? ' active' : ''; ?>" href="/admin/categories"><i class="bi bi-folder"></i>Kategoriler</a></li>
            <li><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/products') ? ' active' : ''; ?>" href="/admin/products"><i class="bi bi-box-seam"></i>Ürünler</a></li>
            <li><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/licenses') ? ' active' : ''; ?>" href="/admin/licenses"><i class="bi bi-key"></i>Lisans Havuzu</a></li>
            <li><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/orders') ? ' active' : ''; ?>" href="/admin/orders"><i class="bi bi-bag"></i>Siparişler</a></li>
            <li><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/customers') ? ' active' : ''; ?>" href="/admin/customers"><i class="bi bi-people"></i>Müşteriler</a></li>
            <li><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/coupons') ? ' active' : ''; ?>" href="/admin/coupons"><i class="bi bi-ticket-perforated"></i>Kuponlar</a></li>
            <li><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/providers') ? ' active' : ''; ?>" href="/admin/providers"><i class="bi bi-plug"></i>Sağlayıcılar</a></li>
            <?php if (($authUser['role'] ?? '') === 'admin'): ?>
                <li><a class="nav-link<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/settings') ? ' active' : ''; ?>" href="/admin/settings"><i class="bi bi-gear"></i>Ayarlar</a></li>
            <?php endif; ?>
        </ul>
    </aside>
    <div class="admin-content">
        <header class="admin-topbar">
            <div>
                <button class="btn btn-outline-secondary d-lg-none mb-3" type="button" data-toggle="sidebar"><i class="bi bi-list"></i></button>
                <h1 class="h4 mb-1 fw-semibold"><?= htmlspecialchars($title ?? ''); ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Anasayfa</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($title ?? ''); ?></li>
                    </ol>
                </nav>
            </div>
            <div class="quick-actions">
                <div class="search-box d-none d-lg-block">
                    <i class="bi bi-search"></i>
                    <input type="search" class="form-control" placeholder="Panelde ara" aria-label="Panelde ara">
                </div>
                <button class="btn btn-outline-secondary d-flex align-items-center gap-2" type="button" data-theme-toggle>
                    <i class="bi bi-sun-fill" data-theme-icon="light"></i>
                    <i class="bi bi-moon-stars-fill d-none" data-theme-icon="dark"></i>
                </button>
                <div class="dropdown">
                    <button class="btn btn-outline-primary d-flex align-items-center gap-2" data-bs-toggle="dropdown" type="button">
                        <i class="bi bi-person-circle"></i>
                        <span><?= htmlspecialchars($authUser['name'] ?? ''); ?></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow">
                        <a class="dropdown-item" href="/admin/settings"><i class="bi bi-sliders me-2"></i>Profil &amp; Ayarlar</a>
                        <div class="dropdown-divider"></div>
                        <form method="post" action="/logout" class="px-3 py-2">
                            <input type="hidden" name="_token" value="<?= csrf_token(); ?>">
                            <button class="btn btn-link p-0 d-flex align-items-center gap-2" type="submit"><i class="bi bi-box-arrow-right"></i>Çıkış</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>
        <?php include __DIR__ . '/../partials/ui/toasts.php'; ?>
        <main class="pb-5">
            <?= $slot ?? ''; ?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/ui.js?v=1" defer></script>
<script src="/assets/js/admin.js?v=1" defer></script>
</body>
</html>
