<?php use App\Core\Security; ?>
<?php use App\Models\LicenseKey; ?>
<?php include __DIR__ . '/partials/header.php'; ?>

<?php $availableStock = LicenseKey::countAvailable((int) $product['id']); ?>

<div class="row g-5">
    <div class="col-lg-6">
        <div class="ratio ratio-16x9 bg-body-secondary rounded"></div>
    </div>
    <div class="col-lg-6">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="/">Ana Sayfa</a></li>
                <?php if (!empty($product['category_slug'])): ?>
                    <li class="breadcrumb-item"><a href="/kategori/<?= Security::sanitize($product['category_slug']); ?>"><?= Security::sanitize($product['category_name']); ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?= Security::sanitize($product['name']); ?></li>
            </ol>
        </nav>
        <h1 class="h3 mb-3"><?= Security::sanitize($product['name']); ?></h1>
        <p class="text-muted"><?= nl2br(Security::sanitize($product['description'] ?? '')); ?></p>
        <div class="d-flex align-items-center gap-3 mb-3">
            <span class="fs-3 fw-bold text-primary"><?= number_format((float) $product['price'], 2); ?> <?= Security::sanitize($product['currency']); ?></span>
            <span class="badge bg-success">Stok: <?= $availableStock > 0 ? $availableStock : 'Sınırsız'; ?></span>
        </div>
        <form class="vstack gap-3" data-product-form>
            <input type="hidden" name="_token" value="<?= csrf_token(); ?>">
            <input type="hidden" name="product_id" value="<?= (int) $product['id']; ?>">
            <?php if (!empty($product['variants'])): ?>
                <div>
                    <label class="form-label">Varyant Seçimi</label>
                    <select class="form-select" name="variant_id">
                        <?php foreach ($product['variants'] as $variant): ?>
                            <option value="<?= (int) $variant['id']; ?>" data-price-delta="<?= (float) $variant['price_delta']; ?>">
                                <?= Security::sanitize($variant['name']); ?> (<?= number_format($product['price'] + $variant['price_delta'], 2); ?> <?= Security::sanitize($product['currency']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <div>
                <label class="form-label">Adet</label>
                <input type="number" name="qty" value="1" min="1" max="<?= max($availableStock, 10); ?>" class="form-control" required>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="button" data-add-to-cart data-product-id="<?= (int) $product['id']; ?>" data-token="<?= csrf_token(); ?>">Sepete Ekle</button>
                <a class="btn btn-outline-secondary" href="/checkout">Hemen Satın Al</a>
            </div>
        </form>
        <div class="mt-4">
            <h5>Özellikler</h5>
            <ul class="list-unstyled small">
                <li>Tür: <?= Security::sanitize($product['type']); ?></li>
                <li>Vergi Oranı: <?= (float) $product['tax_rate']; ?>%</li>
                <li>Para Birimi: <?= Security::sanitize($product['currency']); ?></li>
            </ul>
        </div>
    </div>
</div>

<section class="mt-5">
    <h2 class="h5 mb-3">Benzer Ürünler</h2>
    <div class="row g-4">
        <?php foreach ($related as $item): ?>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="ratio ratio-16x9 bg-body-secondary">&nbsp;</div>
                    <div class="card-body d-flex flex-column">
                        <h3 class="h6"><?= Security::sanitize($item['name']); ?></h3>
                        <p class="text-muted small flex-grow-1"><?= Security::sanitize(substr($item['description'] ?? '', 0, 80)); ?>...</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><?= number_format((float) $item['price'], 2); ?> <?= Security::sanitize($item['currency']); ?></span>
                            <a href="/urun/<?= Security::sanitize($item['slug']); ?>" class="btn btn-sm btn-outline-primary">Detay</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
