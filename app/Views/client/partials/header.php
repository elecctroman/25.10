<?php

use App\Core\Security;
use App\Services\CategoryService;

global $container;

if (!isset($container) && isset($GLOBALS['container'])) {
    $container = $GLOBALS['container'];
}

$config = $container['config'] ?? null;

$categoryService = new CategoryService();
$menuCategories = $categoryService->menuTree();
$cartItems = $_SESSION['cart.items'] ?? [];
$cartCount = 0;
foreach ($cartItems as $item) {
    $cartCount += (int) ($item['qty'] ?? 0);
}
$customer = customer_user();
$supportedLocales = $config ? $config->get('locales.supported', []) : [];
$currentLocale = $config ? $config->get('locales.default', 'tr') : 'tr';
$appName = $config ? $config->get('app.name', 'Dijital Mağaza') : 'Dijital Mağaza';
?><!DOCTYPE html>
<html lang="tr" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Security::sanitize($title ?? 'Mağaza'); ?> - <?= Security::sanitize($appName); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/client.css?v=1">
    <script>
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const storedTheme = localStorage.getItem('theme') || (prefersDark ? 'dark' : 'light');
        document.documentElement.setAttribute('data-bs-theme', storedTheme);
    </script>
</head>
<body class="d-flex flex-column min-vh-100">
<header class="shadow-sm sticky-top bg-body">
    <nav class="navbar navbar-expand-lg container py-2">
        <a class="navbar-brand fw-bold" href="/"><?= Security::sanitize($appName); ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#clientNav" aria-controls="clientNav" aria-expanded="false" aria-label="Menüyü Aç">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="clientNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                <?php foreach ($menuCategories as $category): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="/kategori/<?= Security::sanitize($category['slug']); ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= Security::sanitize($category['name']); ?>
                        </a>
                        <?php if (!empty($category['children'])): ?>
                            <ul class="dropdown-menu">
                                <?php foreach ($category['children'] as $child): ?>
                                    <li><a class="dropdown-item" href="/kategori/<?= Security::sanitize($child['slug']); ?>"><?= Security::sanitize($child['name']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <form class="d-flex me-lg-3 mb-2 mb-lg-0" role="search" action="/products" method="get">
                <input class="form-control me-2" type="search" name="q" placeholder="Ürün ara" value="<?= Security::sanitize($_GET['q'] ?? ''); ?>" aria-label="Ara">
                <button class="btn btn-outline-primary" type="submit">Ara</button>
            </form>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary theme-toggle" type="button" data-theme-toggle>
                    <span class="bi bi-moon">🌙</span>
                </button>
                <?php if ($customer): ?>
                    <div class="dropdown">
                        <a class="btn btn-outline-primary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= Security::sanitize($customer['name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/account">Hesabım</a></li>
                            <li><a class="dropdown-item" href="/account/orders">Siparişlerim</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="post" action="/account/logout" class="px-3 py-2">
                                    <input type="hidden" name="_token" value="<?= csrf_token(); ?>">
                                    <button class="btn btn-link p-0" type="submit">Çıkış Yap</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a class="btn btn-outline-primary" href="/account/login">Giriş Yap</a>
                    <a class="btn btn-primary" href="/account/register">Kayıt Ol</a>
                <?php endif; ?>
                <a class="btn btn-outline-success position-relative" href="/cart" aria-label="Sepetim">
                    🛒
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" data-cart-count><?= $cartCount; ?></span>
                </a>
            </div>
        </div>
    </nav>
</header>

<main class="flex-grow-1">
    <div class="container py-4">
        <?php include __DIR__ . '/alerts.php'; ?>
