<?php ob_start(); ?>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Genel Ayarlar</div>
            <div class="card-body">
                <form method="post" action="/admin/settings/save">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div class="mb-3">
                        <label class="form-label">Site Adı</label>
                        <input type="text" class="form-control" name="site_name" value="<?= htmlspecialchars($settings['site_name']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Logo URL</label>
                        <input type="text" class="form-control" name="site_logo" value="<?= htmlspecialchars($settings['site_logo']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Varsayılan Para Birimi</label>
                        <select class="form-select" name="currency_default">
                            <?php foreach (['TRY','USD','EUR'] as $currency): ?>
                                <option value="<?= $currency; ?>" <?= $settings['currency_default'] === $currency ? 'selected' : ''; ?>><?= $currency; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Varsayılan Vergi Oranı</label>
                        <input type="number" step="0.01" class="form-control" name="tax_default_rate" value="<?= htmlspecialchars((string) $settings['tax_default_rate']); ?>">
                    </div>
                    <button class="btn btn-primary" type="submit">Kaydet</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">SMTP Ayarları</div>
            <div class="card-body">
                <form method="post" action="/admin/settings/save">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <input type="hidden" name="site_name" value="<?= htmlspecialchars($settings['site_name']); ?>">
                    <input type="hidden" name="site_logo" value="<?= htmlspecialchars($settings['site_logo']); ?>">
                    <input type="hidden" name="currency_default" value="<?= htmlspecialchars($settings['currency_default']); ?>">
                    <input type="hidden" name="tax_default_rate" value="<?= htmlspecialchars($settings['tax_default_rate']); ?>">
                    <div class="mb-3">
                        <label class="form-label">Sunucu</label>
                        <input type="text" class="form-control" name="mail_host" value="<?= htmlspecialchars($settings['mail_host']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Port</label>
                        <input type="number" class="form-control" name="mail_port" value="<?= htmlspecialchars((string) $settings['mail_port']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kullanıcı Adı</label>
                        <input type="text" class="form-control" name="mail_username" value="<?= htmlspecialchars($settings['mail_username']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parola</label>
                        <input type="password" class="form-control" name="mail_password" value="<?= htmlspecialchars($settings['mail_password']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gönderen E-posta</label>
                        <input type="email" class="form-control" name="mail_from" value="<?= htmlspecialchars($settings['mail_from']); ?>">
                    </div>
                    <button class="btn btn-primary" type="submit">Kaydet</button>
                </form>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Test E-postası</div>
            <div class="card-body">
                <form method="post" action="/admin/settings/test-email">
                    <input type="hidden" name="_token" value="<?= $csrf; ?>">
                    <div class="mb-3">
                        <label class="form-label">Test Adresi</label>
                        <input type="email" class="form-control" name="test_email" required>
                    </div>
                    <button class="btn btn-outline-primary" type="submit">Gönder</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $slot = ob_get_clean();
include __DIR__ . '/../layouts/admin.php';
