<?php use App\Core\Security; ?>
<?php include __DIR__ . '/partials/header.php'; ?>

<section class="mb-5">
    <div class="hero-slider rounded-4 overflow-hidden position-relative" data-hero-slider>
        <?php if (!empty($banners)): ?>
            <?php foreach ($banners as $index => $banner): ?>
                <div class="hero-slide <?= $index === 0 ? 'is-active' : ''; ?>" style="background-image: url('<?= Security::sanitize($banner['image'] ?? '/assets/img/placeholder.jpg'); ?>');">
                    <div class="hero-overlay p-5">
                        <h2 class="display-5 fw-bold mb-3"><?= Security::sanitize($banner['title'] ?? 'Dijital Kampanyalar'); ?></h2>
                        <p class="lead mb-4"><?= Security::sanitize($banner['subtitle'] ?? 'En yeni dijital ürünleri keşfedin.'); ?></p>
                        <?php if (!empty($banner['cta_url'])): ?>
                            <a href="<?= Security::sanitize($banner['cta_url']); ?>" class="btn btn-primary btn-lg"><?= Security::sanitize($banner['cta_text'] ?? 'Keşfet'); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="hero-slide is-active" style="background: linear-gradient(135deg, #0d6efd, #6610f2);">
                <div class="hero-overlay p-5">
                    <h2 class="display-5 fw-bold mb-3">Dijital Ürünlerde İndirim</h2>
                    <p class="lead mb-4">Popüler lisans anahtarları ve oyun içi ürünlerde fırsatları kaçırmayın.</p>
                    <a href="/products" class="btn btn-primary btn-lg">Ürünleri Gör</a>
                </div>
            </div>
        <?php endif; ?>
        <div class="hero-controls position-absolute bottom-0 end-0 p-3">
            <button class="btn btn-light btn-sm me-2" type="button" data-hero-prev>‹</button>
            <button class="btn btn-light btn-sm" type="button" data-hero-next>›</button>
        </div>
    </div>
</section>

<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h5 mb-0">Öne Çıkan Kategoriler</h3>
        <a href="/products" class="btn btn-sm btn-outline-primary">Tümü</a>
    </div>
    <div class="row g-3">
        <?php foreach ($categories as $category): ?>
            <div class="col-6 col-md-3 col-lg-2">
                <a href="/kategori/<?= Security::sanitize($category['slug']); ?>" class="category-card text-decoration-none">
                    <div class="category-icon mb-2">🎮</div>
                    <div class="fw-semibold text-body"><?= Security::sanitize($category['name']); ?></div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h5 mb-0">Popüler Ürünler</h3>
        <a href="/products" class="btn btn-sm btn-outline-primary">Tüm Ürünler</a>
    </div>
    <div class="row g-4">
        <?php foreach ($featured as $product): ?>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm product-card" data-product-card>
                    <div class="ratio ratio-16x9 bg-body-secondary">&nbsp;</div>
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-light text-dark mb-2"><?= Security::sanitize($product['type']); ?></span>
                        <h4 class="h6"><?= Security::sanitize($product['name']); ?></h4>
                        <p class="text-muted small flex-grow-1"><?= Security::sanitize(substr($product['description'] ?? '', 0, 80)); ?>...</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <strong><?= number_format((float) $product['price'], 2); ?> <?= Security::sanitize($product['currency']); ?></strong>
                            <button class="btn btn-sm btn-primary" data-add-to-cart data-product-id="<?= (int) $product['id']; ?>" data-token="<?= csrf_token(); ?>">Sepete Ekle</button>
                        </div>
                        <a href="/urun/<?= Security::sanitize($product['slug']); ?>" class="stretched-link" aria-label="Ürün detay"></a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
            <div class="mb-3 mb-lg-0">
                <h5 class="mb-1">Yeni kampanya ve kodlardan haberdar olun</h5>
                <p class="small text-muted mb-0">E-posta listemize katılarak özel indirimlere erişin.</p>
            </div>
            <form class="d-flex flex-column flex-sm-row gap-2" action="/destek" method="get">
                <input type="email" class="form-control" placeholder="E-posta adresiniz" required>
                <button class="btn btn-primary" type="submit">Kaydol</button>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
