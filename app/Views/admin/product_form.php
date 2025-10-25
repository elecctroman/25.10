<?php ob_start(); ?>
<form method="post" action="<?= isset($product) ? '/admin/products/update' : '/admin/products/store'; ?>">
    <input type="hidden" name="_token" value="<?= $csrf; ?>">
    <?php if (!empty($product['id'])): ?>
        <input type="hidden" name="id" value="<?= (int) $product['id']; ?>">
    <?php endif; ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Ürün Adı</label>
                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($product['name'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SKU</label>
                        <input type="text" class="form-control" name="sku" value="<?= htmlspecialchars($product['sku'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea class="form-control" name="description" rows="4"><?= htmlspecialchars($product['description'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span>Varyantlar</span>
                    <button class="btn btn-sm btn-outline-primary" type="button" onclick="addVariantRow()">Varyant Ekle</button>
                </div>
                <div class="card-body">
                    <div id="variantRows">
                        <?php if (!empty($product['variants'])): ?>
                            <?php foreach ($product['variants'] as $variant): ?>
                                <div class="row g-2 align-items-end mb-3 variant-row">
                                    <input type="hidden" name="variant_id[]" value="<?= (int) $variant['id']; ?>">
                                    <div class="col-md-4">
                                        <label class="form-label">Ad</label>
                                        <input type="text" class="form-control" name="variant_name[]" value="<?= htmlspecialchars($variant['name']); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Fiyat Farkı</label>
                                        <input type="number" step="0.01" class="form-control" name="variant_price[]" value="<?= htmlspecialchars($variant['price_delta']); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">SKU</label>
                                        <input type="text" class="form-control" name="variant_sku[]" value="<?= htmlspecialchars($variant['sku']); ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.variant-row').remove();">Sil</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" name="category_id">
                            <option value="">Seçiniz</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int) $category['id']; ?>" <?= isset($product['category_id']) && $product['category_id'] == $category['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tür</label>
                        <select class="form-select" name="type">
                            <option value="license" <?= ($product['type'] ?? '') === 'license' ? 'selected' : ''; ?>>Lisans</option>
                            <option value="epin" <?= ($product['type'] ?? '') === 'epin' ? 'selected' : ''; ?>>E-PIN</option>
                            <option value="account" <?= ($product['type'] ?? '') === 'account' ? 'selected' : ''; ?>>Hesap</option>
                            <option value="topup" <?= ($product['type'] ?? '') === 'topup' ? 'selected' : ''; ?>>Top-up</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fiyat</label>
                        <input type="number" step="0.01" class="form-control" name="price" value="<?= htmlspecialchars($product['price'] ?? '0'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Para Birimi</label>
                        <select class="form-select" name="currency">
                            <option value="TRY" <?= ($product['currency'] ?? '') === 'TRY' ? 'selected' : ''; ?>>TRY</option>
                            <option value="USD" <?= ($product['currency'] ?? '') === 'USD' ? 'selected' : ''; ?>>USD</option>
                            <option value="EUR" <?= ($product['currency'] ?? '') === 'EUR' ? 'selected' : ''; ?>>EUR</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vergi Oranı (%)</label>
                        <input type="number" step="0.01" class="form-control" name="tax_rate" value="<?= htmlspecialchars($product['tax_rate'] ?? '0'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Durum</label>
                        <select class="form-select" name="status">
                            <option value="active" <?= ($product['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Aktif</option>
                            <option value="inactive" <?= ($product['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Pasif</option>
                        </select>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Kaydet</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
function addVariantRow() {
    const container = document.getElementById('variantRows');
    const wrapper = document.createElement('div');
    wrapper.classList.add('row', 'g-2', 'align-items-end', 'mb-3', 'variant-row');
    wrapper.innerHTML = `
        <input type="hidden" name="variant_id[]" value="">
        <div class="col-md-4">
            <label class="form-label">Ad</label>
            <input type="text" class="form-control" name="variant_name[]">
        </div>
        <div class="col-md-3">
            <label class="form-label">Fiyat Farkı</label>
            <input type="number" step="0.01" class="form-control" name="variant_price[]">
        </div>
        <div class="col-md-3">
            <label class="form-label">SKU</label>
            <input type="text" class="form-control" name="variant_sku[]">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.variant-row').remove();">Sil</button>
        </div>`;
    container.appendChild(wrapper);
}
</script>
<?php $slot = ob_get_clean();
include __DIR__ . '/../layouts/admin.php';
