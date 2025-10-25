<?php ob_start(); ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Yeni Kupon</div>
            <div class="card-body">
                <form method="post" action="/admin/coupons/store">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div class="mb-3">
                        <label class="form-label">Kod</label>
                        <input type="text" class="form-control" name="code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tip</label>
                        <select class="form-select" name="type">
                            <option value="percent">Yüzde</option>
                            <option value="amount">Tutar</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Değer</label>
                        <input type="number" step="0.01" class="form-control" name="value" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Min. Tutar</label>
                        <input type="number" step="0.01" class="form-control" name="min_total">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Max. İndirim</label>
                        <input type="number" step="0.01" class="form-control" name="max_discount">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Başlangıç</label>
                        <input type="datetime-local" class="form-control" name="start_at">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bitiş</label>
                        <input type="datetime-local" class="form-control" name="end_at">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kullanım Limiti</label>
                        <input type="number" class="form-control" name="usage_limit">
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
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Kuponlar</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>Kod</th><th>Tip</th><th>Değer</th><th>Durum</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($coupons['data'] as $coupon): ?>
                            <tr>
                                <td><?= htmlspecialchars($coupon['code']); ?></td>
                                <td><?= htmlspecialchars($coupon['type']); ?></td>
                                <td><?= htmlspecialchars($coupon['value']); ?></td>
                                <td><?= htmlspecialchars($coupon['status']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#couponModal<?= (int) $coupon['id']; ?>">Düzenle</button>
                                </td>
                            </tr>
                            <div class="modal fade" id="couponModal<?= (int) $coupon['id']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="post" action="/admin/coupons/update">
                                            <input type="hidden" name="_token" value="<?= $csrf; ?>">
                                            <input type="hidden" name="id" value="<?= (int) $coupon['id']; ?>">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Kupon Düzenle</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Kod</label>
                                                    <input type="text" class="form-control" name="code" value="<?= htmlspecialchars($coupon['code']); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Tip</label>
                                                    <select class="form-select" name="type">
                                                        <option value="percent" <?= $coupon['type'] === 'percent' ? 'selected' : ''; ?>>Yüzde</option>
                                                        <option value="amount" <?= $coupon['type'] === 'amount' ? 'selected' : ''; ?>>Tutar</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Değer</label>
                                                    <input type="number" step="0.01" class="form-control" name="value" value="<?= htmlspecialchars($coupon['value']); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Min. Tutar</label>
                                                    <input type="number" step="0.01" class="form-control" name="min_total" value="<?= htmlspecialchars($coupon['min_total']); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Max. İndirim</label>
                                                    <input type="number" step="0.01" class="form-control" name="max_discount" value="<?= htmlspecialchars($coupon['max_discount']); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Başlangıç</label>
                                                    <input type="datetime-local" class="form-control" name="start_at" value="<?= $coupon['start_at'] ? date('Y-m-d\TH:i', strtotime($coupon['start_at'])) : ''; ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Bitiş</label>
                                                    <input type="datetime-local" class="form-control" name="end_at" value="<?= $coupon['end_at'] ? date('Y-m-d\TH:i', strtotime($coupon['end_at'])) : ''; ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Kullanım Limiti</label>
                                                    <input type="number" class="form-control" name="usage_limit" value="<?= htmlspecialchars($coupon['usage_limit']); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Durum</label>
                                                    <select class="form-select" name="status">
                                                        <option value="active" <?= $coupon['status'] === 'active' ? 'selected' : ''; ?>>Aktif</option>
                                                        <option value="inactive" <?= $coupon['status'] === 'inactive' ? 'selected' : ''; ?>>Pasif</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                                <button class="btn btn-primary" type="submit">Güncelle</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
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
