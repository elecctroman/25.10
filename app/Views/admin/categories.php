<?php ob_start(); ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Yeni Kategori</div>
            <div class="card-body">
                <form method="post" action="/admin/categories/create" class="needs-validation" novalidate>
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div class="mb-3">
                        <label class="form-label">Ad</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Üst Kategori</label>
                        <select class="form-select" name="parent_id">
                            <option value="">Yok</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int) $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
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
            <div class="card-header bg-white">Kategori Listesi</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>ID</th><th>Ad</th><th>Durum</th><th>İşlemler</th></tr></thead>
                        <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= (int) $category['id']; ?></td>
                                <td><?= htmlspecialchars($category['name']); ?></td>
                                <td><span class="badge bg-<?= $category['status'] === 'active' ? 'success' : 'secondary'; ?>"><?= htmlspecialchars($category['status']); ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal<?= (int) $category['id']; ?>">Düzenle</button>
                                    <form method="post" action="/admin/categories/delete" class="d-inline">
                                        <input type="hidden" name="_token" value="<?= $csrf; ?>">
                                        <input type="hidden" name="id" value="<?= (int) $category['id']; ?>">
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Silmek istediğinize emin misiniz?');">Sil</button>
                                    </form>
                                </td>
                            </tr>
                            <div class="modal fade" id="editModal<?= (int) $category['id']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="post" action="/admin/categories/update">
                                            <input type="hidden" name="_token" value="<?= $csrf; ?>">
                                            <input type="hidden" name="id" value="<?= (int) $category['id']; ?>">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Kategori Düzenle</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Ad</label>
                                                    <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($category['name']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Üst Kategori</label>
                                                    <select class="form-select" name="parent_id">
                                                        <option value="">Yok</option>
                                                        <?php foreach ($categories as $catOption): ?>
                                                            <option value="<?= (int) $catOption['id']; ?>" <?= $catOption['id'] == $category['parent_id'] ? 'selected' : ''; ?>><?= htmlspecialchars($catOption['name']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Durum</label>
                                                    <select class="form-select" name="status">
                                                        <option value="active" <?= $category['status'] === 'active' ? 'selected' : ''; ?>>Aktif</option>
                                                        <option value="inactive" <?= $category['status'] === 'inactive' ? 'selected' : ''; ?>>Pasif</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                <button type="submit" class="btn btn-primary">Kaydet</button>
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
