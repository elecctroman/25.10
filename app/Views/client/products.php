<?php use App\Core\Security; ?>
<?php include __DIR__ . '/partials/header.php'; ?>

<div class="row g-4">
    <aside class="col-lg-3">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Filtreler</div>
            <div class="card-body">
                <form action="<?= Security::sanitize($_SERVER['REQUEST_URI']); ?>" method="get" class="vstack gap-3">
                    <div>
                        <label class="form-label">Arama</label>
                        <input type="text" class="form-control" name="q" value="<?= Security::sanitize($filters['q'] ?? ''); ?>" placeholder="Anahtar kelime">
                    </div>
                    <div class="row g-2">
                        <div class="col">
                            <label class="form-label">Min ₺</label>
                            <input type="number" min="0" step="0.01" class="form-control" name="min_price" value="<?= Security::sanitize($filters['min_price'] ?? ''); ?>">
                        </div>
                        <div class="col">
                            <label class="form-label">Max ₺</label>
                            <input type="number" min="0" step="0.01" class="form-control" name="max_price" value="<?= Security::sanitize($filters['max_price'] ?? ''); ?>">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Tür</label>
                        <select class="form-select" name="type">
                            <option value="">Tümü</option>
                            <?php foreach (['license' => 'Lisans', 'epin' => 'E-Pin', 'account' => 'Hesap', 'topup' => 'Bakiyeler'] as $key => $label): ?>
                                <option value="<?= $key; ?>" <?= ($filters['type'] ?? '') === $key ? 'selected' : ''; ?>><?= $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-primary" type="submit">Uygula</button>
                        <a class="btn btn-link" href="/products">Sıfırla</a>
                    </div>
                </form>
            </div>
        </div>
    </aside>
    <section class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h4 mb-0"><?= Security::sanitize($category['name'] ?? $title ?? 'Ürünler'); ?></h1>
                <p class="text-muted small mb-0">Toplam <?= (int) ($products['total'] ?? 0); ?> sonuç bulundu.</p>
            </div>
            <a href="/cart" class="btn btn-outline-success">Sepeti Gör</a>
        </div>
        <div class="row g-4">
            <?php foreach ($products['data'] as $product): ?>
                <div class="col-12 col-md-6">
                    <div class="card h-100 shadow-sm product-card" data-product-card>
                        <div class="ratio ratio-16x9 bg-body-secondary">&nbsp;</div>
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-light text-dark mb-2"><?= Security::sanitize($product['type']); ?></span>
                            <h2 class="h6 mb-2"><?= Security::sanitize($product['name']); ?></h2>
                            <p class="text-muted small flex-grow-1"><?= Security::sanitize(substr($product['description'] ?? '', 0, 120)); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <strong><?= number_format((float) $product['price'], 2); ?> <?= Security::sanitize($product['currency']); ?></strong>
                                <button class="btn btn-sm btn-primary" data-add-to-cart data-product-id="<?= (int) $product['id']; ?>" data-token="<?= csrf_token(); ?>">Sepete Ekle</button>
                            </div>
                            <a href="/urun/<?= Security::sanitize($product['slug']); ?>" class="stretched-link"></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        $totalPages = (int) ceil(($products['total'] ?? 0) / ($products['per_page'] ?? 1));
        $currentPage = (int) ($products['current_page'] ?? 1);
        if ($totalPages > 1): ?>
            <nav class="mt-4" aria-label="Sayfalar">
                <ul class="pagination justify-content-center">
                    <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                        <?php $query = $_GET; $query['page'] = $page; ?>
                        <li class="page-item <?= $page === $currentPage ? 'active' : ''; ?>">
                            <a class="page-link" href="?<?= http_build_query($query); ?>"><?= $page; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </section>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
