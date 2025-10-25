<?php ob_start(); ?>
<?php if (!$order): ?>
    <div class="alert alert-warning">Sipariş bulunamadı.</div>
<?php else: ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Sipariş Bilgileri</div>
            <div class="card-body">
                <p><strong>Numara:</strong> #<?= htmlspecialchars($order['order_no']); ?></p>
                <p><strong>E-posta:</strong> <?= htmlspecialchars($order['email']); ?></p>
                <p><strong>Tutar:</strong> <?= htmlspecialchars($order['total_gross']); ?> <?= htmlspecialchars($order['currency']); ?></p>
                <p><strong>Durum:</strong> <span class="badge bg-secondary text-uppercase"><?= htmlspecialchars($order['status']); ?></span></p>
                <form method="post" action="/admin/orders/status">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <input type="hidden" name="order_id" value="<?= (int) $order['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">Durum Güncelle</label>
                        <select class="form-select" name="status">
                            <?php foreach (['pending','paid','delivered','refunded','cancelled'] as $status): ?>
                                <option value="<?= $status; ?>" <?= $order['status'] === $status ? 'selected' : ''; ?>><?= ucfirst($status); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Kaydet</button>
                </form>
            </div>
        </div>
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white">Teslimat Ekle</div>
            <div class="card-body">
                <form method="post" action="/admin/orders/delivery">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <input type="hidden" name="order_id" value="<?= (int) $order['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">Sipariş Kalemi</label>
                        <select class="form-select" name="order_item_id">
                            <?php foreach ($order['items'] as $item): ?>
                                <option value="<?= (int) $item['id']; ?>"><?= htmlspecialchars($item['product_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teslim Türü</label>
                        <select class="form-select" name="type">
                            <option value="inline_text">Metin</option>
                            <option value="file">Dosya</option>
                            <option value="code">Kod</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">İçerik</label>
                        <textarea class="form-control" name="payload" rows="4"></textarea>
                    </div>
                    <button class="btn btn-outline-primary w-100" type="submit">Teslimat Kaydet</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">Sipariş Kalemleri</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>Ürün</th><th>Adet</th><th>Birim Fiyat</th><th>Vergi</th><th>Toplam</th></tr></thead>
                        <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_name']); ?><div class="small text-muted">Varyant: <?= htmlspecialchars($item['variant_name']); ?></div></td>
                                <td><?= (int) $item['qty']; ?></td>
                                <td><?= number_format((float) $item['unit_price'], 2); ?></td>
                                <td><?= number_format((float) $item['tax_rate'], 2); ?>%</td>
                                <td><?= number_format((float) $item['total'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Teslimat Kayıtları</div>
            <div class="card-body">
                <?php foreach ($order['items'] as $index => $item): ?>
                    <h6><?= htmlspecialchars($item['product_name']); ?></h6>
                    <ul class="list-group mb-3">
                        <?php foreach (\App\Models\Delivery::byOrderItem((int) $item['id']) as $delivery): ?>
                            <li class="list-group-item">
                                <div><strong><?= htmlspecialchars($delivery['type']); ?></strong> &mdash; <?= htmlspecialchars($delivery['delivered_at']); ?></div>
                                <pre class="mb-0 small"><?= htmlspecialchars($delivery['payload']); ?></pre>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $slot = ob_get_clean();
include __DIR__ . '/../layouts/admin.php';
