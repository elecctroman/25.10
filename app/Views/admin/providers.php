<?php ob_start(); ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Yeni Sağlayıcı</div>
            <div class="card-body">
                <form method="post" action="/admin/providers/store">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div class="mb-3">
                        <label class="form-label">Ad</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Anahtar</label>
                        <input type="text" class="form-control" name="key" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Key</label>
                        <input type="text" class="form-control" name="api_key">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Durum</label>
                        <select class="form-select" name="status">
                            <option value="active">Aktif</option>
                            <option value="inactive">Pasif</option>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Kaydet</button>
                </form>
            </div>
        </div>
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white">Stub Sağlayıcı Bakiyesi</div>
            <div class="card-body">
                <p class="fs-4">₺<?= number_format((float) $balance['balance'], 2); ?></p>
                <p class="text-muted">Son güncelleme: <?= date('d.m.Y H:i'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">Kayıtlı Sağlayıcılar</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>Ad</th><th>Anahtar</th><th>Durum</th></tr></thead>
                        <tbody>
                        <?php foreach ($providers['data'] as $provider): ?>
                            <tr>
                                <td><?= htmlspecialchars($provider['name']); ?></td>
                                <td><?= htmlspecialchars($provider['key']); ?></td>
                                <td><?= htmlspecialchars($provider['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Sağlayıcı Olayları</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>ID</th><th>Tip</th><th>Durum</th><th>Oluşturulma</th></tr></thead>
                        <tbody>
                        <?php foreach ($events['data'] as $event): ?>
                            <tr>
                                <td><?= (int) $event['id']; ?></td>
                                <td><?= htmlspecialchars($event['event_type']); ?></td>
                                <td><?= htmlspecialchars($event['status']); ?></td>
                                <td><?= htmlspecialchars($event['created_at']); ?></td>
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
