<?php ob_start(); ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Toplu Anahtar İçe Aktar</div>
            <div class="card-body">
                <form method="post" action="/admin/licenses/import">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div class="mb-3">
                        <label class="form-label">Ürün</label>
                        <select class="form-select" name="product_id" required>
                            <?php foreach ($products as $product): ?>
                                <option value="<?= (int) $product['id']; ?>"><?= htmlspecialchars($product['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Anahtarlar (her satırda bir)</label>
                        <textarea class="form-control" name="codes" rows="8" required></textarea>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">İçe Aktar</button>
                </form>
            </div>
        </div>
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white">Toplu Durum Güncelle</div>
            <div class="card-body">
                <form method="post" action="/admin/licenses/status">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div class="mb-3">
                        <label class="form-label">Anahtar ID'leri (virgülle)</label>
                        <input type="text" class="form-control" name="id_list" placeholder="1,2,3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Durum</label>
                        <select class="form-select" name="status">
                            <option value="available">Uygun</option>
                            <option value="reserved">Rezerve</option>
                            <option value="sold">Satıldı</option>
                        </select>
                    </div>
                    <button class="btn btn-outline-primary w-100" type="submit" onclick="prepareIds(event)">Güncelle</button>
                    <div id="idsHidden"></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Anahtar Havuzu</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>ID</th><th>Ürün</th><th>Kod</th><th>Durum</th><th>Oluşturulma</th></tr></thead>
                        <tbody>
                        <?php foreach ($keys['data'] as $key): ?>
                            <tr>
                                <td><?= (int) $key['id']; ?></td>
                                <td><?= (int) $key['product_id']; ?></td>
                                <td><code><?= htmlspecialchars($key['code']); ?></code></td>
                                <td><?= htmlspecialchars($key['status']); ?></td>
                                <td><?= htmlspecialchars($key['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function prepareIds(event) {
    const input = document.querySelector('[name="id_list"]');
    const hiddenContainer = document.getElementById('idsHidden');
    hiddenContainer.innerHTML = '';
    if (!input.value) {
        return;
    }
    input.value.split(',').map(id => id.trim()).filter(Boolean).forEach(id => {
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'ids[]';
        hidden.value = id;
        hiddenContainer.appendChild(hidden);
    });
}
</script>
<?php $slot = ob_get_clean();
include __DIR__ . '/../layouts/admin.php';
