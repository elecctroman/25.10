<?php ob_start(); ?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Bugünkü Satış</h5>
                <p class="display-6">₺<?= number_format($stats['today'], 2); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Son 7 Gün</h5>
                <p class="display-6">₺<?= number_format($stats['week'], 2); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Son 30 Gün</h5>
                <p class="display-6">₺<?= number_format($stats['month'], 2); ?></p>
            </div>
        </div>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white">En Çok Satan Ürünler</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead><tr><th>Ürün</th><th>Adet</th><th>Tutar</th></tr></thead>
                        <tbody>
                        <?php foreach ($topProducts as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['name']); ?></td>
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
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">Son Siparişler</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>#</th><th>E-posta</th><th>Tutar</th><th>Durum</th></tr></thead>
                        <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><a href="/admin/orders/view?id=<?= (int) $order['id']; ?>">#<?= htmlspecialchars($order['order_no']); ?></a></td>
                                <td><?= htmlspecialchars($order['email']); ?></td>
                                <td>₺<?= number_format((float) $order['total_gross'], 2); ?></td>
                                <td><span class="badge bg-secondary text-uppercase"><?= htmlspecialchars($order['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Sistem Logları</div>
            <div class="card-body">
                <h6>Hata Logları</h6>
                <ul class="list-group list-group-flush mb-3">
                    <?php foreach ($recentErrors as $error): ?>
                        <li class="list-group-item">
                            <strong><?= strtoupper($error['level']); ?>:</strong> <?= htmlspecialchars($error['message']); ?>
                            <div class="text-muted small"><?= htmlspecialchars($error['created_at']); ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <h6>Denetim Kayıtları</h6>
                <ul class="list-group list-group-flush">
                    <?php foreach ($recentAudits as $audit): ?>
                        <li class="list-group-item">
                            <?= htmlspecialchars($audit['action']); ?> &ndash; <?= htmlspecialchars($audit['created_at']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php $slot = ob_get_clean();
include __DIR__ . '/../layouts/admin.php';
