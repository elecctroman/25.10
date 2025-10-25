<?php ob_start(); ?>
<div class="admin-stats mb-4">
    <div><?php $icon = 'bi-cash-stack'; $label = 'Bugünkü Satış'; $value = '₺' . number_format($stats['today'], 2); $chartValues = [40,60,45,75,62]; include __DIR__ . '/../components/stat_card.php'; ?></div>
    <div><?php $icon = 'bi-calendar-week'; $label = 'Son 7 Gün'; $value = '₺' . number_format($stats['week'], 2); $chartValues = [30,45,70,68,80]; include __DIR__ . '/../components/stat_card.php'; ?></div>
    <div><?php $icon = 'bi-calendar3'; $label = 'Son 30 Gün'; $value = '₺' . number_format($stats['month'], 2); $badge = '%12 artış'; $chartValues = [55,65,72,80,95]; include __DIR__ . '/../components/stat_card.php'; ?></div>
</div>

<section class="admin-quick-links mb-5">
    <h2 class="h6 text-uppercase text-body-secondary mb-3">Hızlı İşlemler</h2>
    <div class="quick-links">
        <?php $title = 'Yeni Ürün'; $href = '/admin/products/create'; $icon = 'bi-plus-circle'; $description = 'Dakikalar içinde yeni bir dijital ürün ekleyin.'; include __DIR__ . '/../components/quick_link_card.php'; ?>
        <?php $title = 'Anahtar Havuzu'; $href = '/admin/licenses'; $icon = 'bi-key'; $description = 'Satışa hazır lisans anahtarlarını yönetin.'; include __DIR__ . '/../components/quick_link_card.php'; ?>
        <?php $title = 'Kupon Oluştur'; $href = '/admin/coupons/create'; $icon = 'bi-ticket-perforated'; $description = 'Kampanya veya özel indirim kuponu tanımlayın.'; include __DIR__ . '/../components/quick_link_card.php'; ?>
        <?php $title = 'Sağlayıcı Ayarları'; $href = '/admin/providers'; $icon = 'bi-plug'; $description = 'Entegrasyon sağlayıcılarınızı yapılandırın.'; include __DIR__ . '/../components/quick_link_card.php'; ?>
    </div>
</section>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom-0">
                <h2 class="h5 fw-semibold mb-0">En Çok Satan Ürünler</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="text-body-secondary"><tr><th>Ürün</th><th>Adet</th><th>Tutar</th></tr></thead>
                        <tbody>
                        <?php foreach ($topProducts as $product): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($product['name']); ?></td>
                                <td><?= (int) $product['total_qty']; ?></td>
                                <td>₺<?= number_format((float) $product['total_amount'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom-0">
                <h2 class="h5 fw-semibold mb-0">Son Siparişler</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead class="text-body-secondary"><tr><th>#</th><th>Müşteri</th><th>Tutar</th><th>Durum</th></tr></thead>
                        <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><a class="fw-semibold" href="/admin/orders/view?id=<?= (int) $order['id']; ?>">#<?= htmlspecialchars($order['order_no']); ?></a></td>
                                <td><?= htmlspecialchars($order['email']); ?></td>
                                <td>₺<?= number_format((float) $order['total_gross'], 2); ?></td>
                                <td><span class="badge bg-primary-subtle text-primary-emphasis text-uppercase"><?= htmlspecialchars($order['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom-0">
                <h2 class="h5 fw-semibold mb-0">Sistem Logları</h2>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="text-body-secondary text-uppercase small">Hata Logları</h6>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($recentErrors as $error): ?>
                                <li class="list-group-item">
                                    <strong><?= strtoupper($error['level']); ?>:</strong> <?= htmlspecialchars($error['message']); ?>
                                    <div class="text-muted small mt-1"><i class="bi bi-clock me-1"></i><?= htmlspecialchars($error['created_at']); ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-body-secondary text-uppercase small">Denetim Kayıtları</h6>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($recentAudits as $audit): ?>
                                <li class="list-group-item">
                                    <?= htmlspecialchars($audit['action']); ?>
                                    <div class="text-muted small mt-1"><i class="bi bi-person"></i> <?= htmlspecialchars((string) $audit['user_id']); ?> · <?= htmlspecialchars($audit['created_at']); ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $slot = ob_get_clean();
include __DIR__ . '/../layouts/admin.php';
