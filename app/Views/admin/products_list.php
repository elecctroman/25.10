<?php ob_start(); ?>
<div class="d-flex justify-content-end mb-3">
    <a class="btn btn-primary" href="/admin/products/create">Yeni Ürün</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>ID</th><th>Ad</th><th>Tür</th><th>Fiyat</th><th>Durum</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= (int) $product['id']; ?></td>
                        <td><?= htmlspecialchars($product['name']); ?><br><small class="text-muted"><?= htmlspecialchars($product['sku']); ?></small></td>
                        <td><?= htmlspecialchars($product['type']); ?></td>
                        <td><?= htmlspecialchars($product['price']); ?> <?= htmlspecialchars($product['currency']); ?></td>
                        <td><span class="badge bg-<?= $product['status'] === 'active' ? 'success' : 'secondary'; ?>"><?= htmlspecialchars($product['status']); ?></span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="/admin/products/edit?id=<?= (int) $product['id']; ?>">Düzenle</a>
                            <form method="post" action="/admin/products/delete" class="d-inline">
                                <input type="hidden" name="_token" value="<?= $csrf; ?>">
                                <input type="hidden" name="id" value="<?= (int) $product['id']; ?>">
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Silmek istediğinize emin misiniz?');">Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $slot = ob_get_clean();
include __DIR__ . '/../layouts/admin.php';
