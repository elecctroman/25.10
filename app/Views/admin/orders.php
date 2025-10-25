<?php ob_start(); ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Manuel Sipariş Oluştur</div>
            <div class="card-body">
                <form method="post" action="/admin/orders/create">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div class="mb-3">
                        <label class="form-label">Müşteri E-posta</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ürün</label>
                        <select class="form-select" name="product_id" required>
                            <?php foreach ($products as $product): ?>
                                <option value="<?= (int) $product['id']; ?>"><?= htmlspecialchars($product['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adet</label>
                        <input type="number" class="form-control" name="qty" value="1" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teslim Stratejisi</label>
                        <select class="form-select" name="delivery_strategy">
                            <option value="auto_key">Anahtar Otomatik</option>
                            <option value="manual">Manuel</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Durum</label>
                        <select class="form-select" name="status">
                            <option value="pending">Beklemede</option>
                            <option value="paid">Ödendi</option>
                            <option value="delivered">Teslim Edildi</option>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Oluştur</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Siparişler</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>#</th><th>Müşteri</th><th>Tutar</th><th>Durum</th><th>Oluşturulma</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($orders['data'] as $order): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($order['order_no']); ?></td>
                                <td><?= htmlspecialchars($order['email']); ?></td>
                                <td><?= htmlspecialchars($order['total_gross']); ?> <?= htmlspecialchars($order['currency']); ?></td>
                                <td><span class="badge bg-info text-uppercase"><?= htmlspecialchars($order['status']); ?></span></td>
                                <td><?= htmlspecialchars($order['created_at']); ?></td>
                                <td><a class="btn btn-sm btn-outline-secondary" href="/admin/orders/view?id=<?= (int) $order['id']; ?>">Görüntüle</a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $slot = ob_get_clean();
include __DIR__ . '/../layouts/admin.php';
