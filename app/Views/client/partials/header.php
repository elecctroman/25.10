<?php

use App\Core\Security;
use App\Services\CategoryService;

global $container;

if (!isset($container) && isset($GLOBALS['container'])) {
    $container = $GLOBALS['container'];
}

$config = $container['config'] ?? null;
$session = $container['session'] ?? null;

$categoryService = new CategoryService();
$menuCategories = $categoryService->menuTree();
$cartItems = $_SESSION['cart.items'] ?? [];
$cartCount = 0;
foreach ($cartItems as $item) {
    $cartCount += (int) ($item['qty'] ?? 0);
}
$customer = customer_user();
$appName = $config ? $config->get('app.name', 'Dijital Mağaza') : 'Dijital Mağaza';
$supportEmail = $config ? $config->get('mail.from_email', 'support@example.com') : 'support@example.com';
$flashMessages = $flash ?? [];
?><!DOCTYPE html>
<html lang="tr" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Security::sanitize($title ?? 'Mağaza'); ?> - <?= Security::sanitize($appName); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/theme.css?v=1">
    <link rel="stylesheet" href="/assets/css/client.css?v=2">
    <script>
        (function() {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const storedTheme = localStorage.getItem('theme') || (prefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', storedTheme);
        })();
    </script>
</head>
<body class="d-flex flex-column min-vh-100" data-app-shell>
<?php include __DIR__ . '/../../partials/ui/loader.php'; ?>
<header class="app-header shadow-sm">
    <div class="app-topbar py-2 bg-gradient-primary text-white">
        <div class="container d-flex align-items-center justify-content-between gap-3">
            <a class="navbar-brand d-flex align-items-center gap-2 text-white fw-semibold" href="/">
                <span class="brand-avatar rounded-circle bg-white bg-opacity-25 p-2"><i class="bi bi-layers"></i></span>
                <span><?= Security::sanitize($appName); ?></span>
            </a>
            <div class="d-none d-lg-flex align-items-center gap-3">
                <span class="small text-white-50"><i class="bi bi-envelope-open me-1"></i><?= Security::sanitize($supportEmail); ?></span>
                <button class="btn btn-sm btn-outline-light d-flex align-items-center gap-2" type="button" data-theme-toggle>
                    <i class="bi bi-sun-fill" data-theme-icon="light"></i>
                    <i class="bi bi-moon-stars-fill d-none" data-theme-icon="dark"></i>
                    <span class="d-none d-xl-inline">Tema</span>
                </button>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg bg-body border-bottom">
        <div class="container py-2">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#clientNav" aria-controls="clientNav" aria-expanded="false" aria-label="Menüyü Aç">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="clientNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                    <?php foreach ($menuCategories as $category): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="/kategori/<?= Security::sanitize($category['slug']); ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-grid me-2 text-primary"></i><?= Security::sanitize($category['name']); ?>
                            </a>
                            <?php if (!empty($category['children'])): ?>
                                <ul class="dropdown-menu shadow">
                                    <?php foreach ($category['children'] as $child): ?>
                                        <li><a class="dropdown-item" href="/kategori/<?= Security::sanitize($child['slug']); ?>"><?= Security::sanitize($child['name']); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <form class="floating-search" role="search" action="/products" method="get">
                    <div class="input-group input-group-lg shadow-sm">
                        <span class="input-group-text bg-transparent border-0"><i class="bi bi-search"></i></span>
                        <input class="form-control border-0" type="search" name="q" placeholder="Ürün ara" value="<?= Security::sanitize($_GET['q'] ?? ''); ?>" aria-label="Ara">
                        <button class="btn btn-primary px-4" type="submit">Ara</button>
                    </div>
                </form>
                <div class="navbar-actions d-flex flex-column flex-lg-row align-items-lg-center gap-2 ms-lg-3">
                    <?php if ($customer): ?>
                        <div class="dropdown">
                            <a class="btn btn-outline-primary d-flex align-items-center gap-2 dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="avatar-sm"><i class="bi bi-person-circle"></i></span>
                                <span><?= Security::sanitize($customer['name']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><a class="dropdown-item" href="/account"><i class="bi bi-speedometer2 me-2"></i>Hesabım</a></li>
                                <li><a class="dropdown-item" href="/account/orders"><i class="bi bi-bag-check me-2"></i>Siparişlerim</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="post" action="/account/logout" class="px-3 py-2">
                                        <input type="hidden" name="_token" value="<?= csrf_token(); ?>">
                                        <button class="btn btn-link p-0 d-flex align-items-center gap-2" type="submit"><i class="bi bi-box-arrow-right"></i>Çıkış Yap</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a class="btn btn-outline-primary d-flex align-items-center gap-2" href="/account/login"><i class="bi bi-box-arrow-in-right"></i>Giriş</a>
                        <a class="btn btn-primary d-flex align-items-center gap-2" href="/account/register"><i class="bi bi-person-plus"></i>Kayıt Ol</a>
                    <?php endif; ?>
                    <a class="btn btn-outline-success position-relative d-flex align-items-center gap-2" href="/cart" aria-label="Sepetim">
                        <i class="bi bi-cart3"></i>
                        <span>Sepet</span>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" data-cart-count><?= $cartCount; ?></span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>

<main class="flex-grow-1">
    <div class="container py-4">
        <?php include __DIR__ . '/alerts.php'; ?>
