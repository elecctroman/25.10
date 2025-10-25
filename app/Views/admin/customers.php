<?php ob_start(); ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Yeni Müşteri</div>
            <div class="card-body">
                <form method="post" action="/admin/customers/store">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div class="mb-3">
                        <label class="form-label">Ad Soyad</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-posta</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parola</label>
                        <input type="text" class="form-control" name="password" placeholder="Opsiyonel">
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Kaydet</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Müşteri Listesi</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>Ad</th><th>E-posta</th><th>Durum</th><th>Toplam Sipariş</th></tr></thead>
                        <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?= htmlspecialchars($customer['name']); ?></td>
                                <td><?= htmlspecialchars($customer['email']); ?></td>
                                <td><?= htmlspecialchars($customer['status']); ?></td>
                                <td><?= count($ordersByCustomer[$customer['id']] ?? []); ?></td>
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
