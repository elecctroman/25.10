<?php ob_start(); ?>
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-bottom-0">
                <h2 class="h5 fw-semibold mb-0">Manuel Sipariş Oluştur</h2>
            </div>
            <div class="card-body">
                <div class="stepper" data-stepper>
                    <div class="step active" data-step data-step-index="0">Müşteri</div>
                    <div class="step" data-step data-step-index="1">Ürün</div>
                    <div class="step" data-step data-step-index="2">Teslimat</div>
                </div>
                <form method="post" action="/admin/orders/create" class="vstack gap-3">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div data-step-panel>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="orderEmail" name="email" placeholder="musteri@example.com" required>
                            <label for="orderEmail">Müşteri E-posta</label>
                        </div>
                        <button class="btn btn-outline-primary w-100" type="button" data-step-next>Devam Et</button>
                    </div>
                    <div class="d-none" data-step-panel>
                        <div class="form-floating mb-3">
                            <select class="form-select" id="orderProduct" name="product_id" required>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= (int) $product['id']; ?>"><?= htmlspecialchars($product['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="orderProduct">Ürün</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="number" class="form-control" id="orderQty" name="qty" value="1" min="1" placeholder="Adet">
                            <label for="orderQty">Adet</label>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-outline-secondary" type="button" data-step-prev>Geri</button>
                            <button class="btn btn-outline-primary" type="button" data-step-next>Devam</button>
                        </div>
                    </div>
                    <div class="d-none" data-step-panel>
                        <div class="form-floating mb-3">
                            <select class="form-select" id="orderStrategy" name="delivery_strategy">
                                <option value="auto_key">Anahtar Otomatik</option>
                                <option value="manual">Manuel</option>
                            </select>
                            <label for="orderStrategy">Teslim Stratejisi</label>
                        </div>
                        <div class="form-floating mb-3">
                            <select class="form-select" id="orderStatus" name="status">
                                <option value="pending">Beklemede</option>
                                <option value="paid">Ödendi</option>
                                <option value="delivered">Teslim Edildi</option>
                            </select>
                            <label for="orderStatus">Sipariş Durumu</label>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-outline-secondary" type="button" data-step-prev>Geri</button>
                            <button class="btn btn-gradient" type="submit">Siparişi Oluştur</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-bottom-0 d-flex justify-content-between align-items-center">
                <h2 class="h5 fw-semibold mb-0">Siparişler</h2>
                <span class="badge bg-primary-subtle text-primary-emphasis">Toplam: <?= (int) $orders['total']; ?></span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="text-body-secondary"><tr><th>#</th><th>Müşteri</th><th>Tutar</th><th>Durum</th><th>Oluşturulma</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($orders['data'] as $order): ?>
                            <tr>
                                <td class="fw-semibold">#<?= htmlspecialchars($order['order_no']); ?></td>
                                <td><?= htmlspecialchars($order['email']); ?></td>
                                <td><?= htmlspecialchars($order['total_gross']); ?> <?= htmlspecialchars($order['currency']); ?></td>
                                <td><span class="badge bg-primary-subtle text-primary-emphasis text-uppercase"><?= htmlspecialchars($order['status']); ?></span></td>
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
