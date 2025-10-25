<?php use App\Core\Security; ?>
<?php include __DIR__ . '/partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Siparişlerim</h1>
    <a href="/products" class="btn btn-outline-primary">Alışverişe Devam Et</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Sipariş No</th>
                    <th>Tarih</th>
                    <th>Durum</th>
                    <th class="text-end">Tutar</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong>#<?= Security::sanitize($order['order_no']); ?></strong></td>
                        <td><?= Security::sanitize(date('d.m.Y H:i', strtotime($order['created_at']))); ?></td>
                        <td><span class="badge bg-info text-dark"><?= Security::sanitize($order['status']); ?></span></td>
                        <td class="text-end fw-semibold"><?= number_format((float) $order['total_gross'], 2); ?> <?= Security::sanitize($order['currency']); ?></td>
                        <td class="text-end"><a href="/account/orders/<?= Security::sanitize($order['order_no']); ?>" class="btn btn-sm btn-outline-primary">Detay</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
