document.addEventListener('DOMContentLoaded', () => {
    initHeroSlider();
    initCartActions();
    initPasswordToggle();
    initCopyButtons();
});

function initHeroSlider() {
    const slides = document.querySelectorAll('[data-hero-slider] .hero-slide');
    if (!slides.length) return;
    let index = 0;
    const showSlide = (i) => {
        slides.forEach((slide, sIndex) => {
            slide.classList.toggle('is-active', sIndex === i);
        });
        index = i;
    };
    const next = () => showSlide((index + 1) % slides.length);
    const prev = () => showSlide((index - 1 + slides.length) % slides.length);
    document.querySelector('[data-hero-next]')?.addEventListener('click', next);
    document.querySelector('[data-hero-prev]')?.addEventListener('click', prev);
    setInterval(next, 5000);
}

function initCartActions() {
    document.querySelectorAll('[data-add-to-cart]').forEach(button => {
        button.addEventListener('click', async (event) => {
            event.preventDefault();
            const productId = button.dataset.productId;
            const form = button.closest('[data-product-form]');
            const payload = new FormData();
            payload.append('_token', button.dataset.token || form?.querySelector('input[name="_token"]').value || '');
            payload.append('product_id', productId);
            payload.append('variant_id', form?.querySelector('select[name="variant_id"]').value || '');
            payload.append('qty', form?.querySelector('input[name="qty"]').value || 1);
            try {
                const response = await fetch('/cart/add', {method: 'POST', body: payload});
                const data = await response.json();
                refreshToken(data.token);
                if (!data.success) throw new Error(data.message || 'Bir hata oluştu');
                updateCartSummary(data.summary);
                AppUI.showToast('Ürün sepete eklendi', 'success');
            } catch (error) {
                AppUI.showToast(error.message, 'danger');
            }
        });
    });

    document.querySelectorAll('[data-cart-item]').forEach(row => {
        const decrease = row.querySelector('[data-cart-decrease]');
        const increase = row.querySelector('[data-cart-increase]');
        const input = row.querySelector('input[name="qty"]');
        const key = row.dataset.cartItem;
        decrease?.addEventListener('click', () => updateQuantity(key, Math.max(1, parseInt(input.value, 10) - 1)));
        increase?.addEventListener('click', () => updateQuantity(key, parseInt(input.value, 10) + 1));
        row.querySelector('[data-remove-item]')?.addEventListener('click', () => updateQuantity(key, 0));
    });

    const couponForm = document.querySelector('[data-coupon-form]');
    couponForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const formData = new FormData(couponForm);
        try {
            const response = await fetch('/cart/apply-coupon', {method: 'POST', body: formData});
            const data = await response.json();
            refreshToken(data.token);
            if (!data.success) throw new Error(data.message || 'Kupon uygulanamadı');
            updateCartSummary(data.summary);
            AppUI.showToast('Kupon uygulandı', 'success');
        } catch (error) {
            AppUI.showToast(error.message, 'danger');
        }
    });
}

async function updateQuantity(key, qty) {
    const tokenField = document.querySelector('[data-coupon-form] input[name="_token"]');
    const payload = new FormData();
    payload.append('_token', tokenField ? tokenField.value : '');
    payload.append('key', key);
    payload.append('qty', qty);
    try {
        const response = await fetch('/cart/update', {method: 'POST', body: payload});
        const data = await response.json();
        refreshToken(data.token);
        if (!data.success) throw new Error(data.message || 'Sepet güncellenemedi');
        updateCartSummary(data.summary);
        if (qty === 0) {
            document.querySelector(`[data-cart-item="${key}"]`)?.remove();
        } else {
            const row = document.querySelector(`[data-cart-item="${key}"]`);
            if (row) {
                row.querySelector('input[name="qty"]').value = qty;
                row.querySelector('[data-line-total]').textContent = formatCurrency(lineTotal(data.summary, key));
            }
        }
        AppUI.showToast('Sepet güncellendi', 'success');
    } catch (error) {
        AppUI.showToast(error.message, 'danger');
    }
}

function lineTotal(summary, key) {
    const item = summary.items[key];
    if (!item) return 0;
    return (item.price * item.qty) + (item.price * item.qty * item.tax_rate / 100);
}

function updateCartSummary(summary) {
    const container = document.querySelector('[data-cart-summary]');
    if (!container || !summary) return;
    container.querySelectorAll('strong').forEach(el => {
        const label = el.previousSibling?.textContent?.trim();
        switch (label) {
            case 'Ara Toplam':
                el.textContent = formatCurrency(summary.subtotal, summary.currency);
                break;
            case 'Vergi':
                el.textContent = formatCurrency(summary.tax, summary.currency);
                break;
            case 'İndirim':
                el.textContent = '-' + formatCurrency(summary.discount, summary.currency);
                break;
            case 'Genel Toplam':
            case 'Ödenecek Tutar':
                el.textContent = formatCurrency(summary.total, summary.currency);
                break;
        }
    });
    const badge = document.querySelector('[data-cart-count]');
    if (badge) {
        const totalItems = Object.values(summary.items || {}).reduce((acc, item) => acc + Number(item.qty), 0);
        badge.textContent = totalItems;
    }
}

function formatCurrency(amount, currency = 'TRY') {
    return new Intl.NumberFormat('tr-TR', {style: 'currency', currency}).format(amount || 0);
}

function initPasswordToggle() {
    document.querySelectorAll('[data-toggle-password]').forEach(button => {
        button.addEventListener('click', () => {
            const input = button.closest('.input-group')?.querySelector('[data-password], input[type="password"]');
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            button.textContent = isPassword ? 'Gizle' : 'Göster';
        });
    });
}

function initCopyButtons() {
    document.querySelectorAll('[data-copy]').forEach(button => {
        button.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(button.dataset.copy);
                AppUI.showToast('Kopyalandı', 'success');
            } catch (error) {
                AppUI.showToast('Kopyalama başarısız', 'danger');
            }
        });
    });
}

function refreshToken(token) {
    if (!token) return;
    document.querySelectorAll('input[name="_token"]').forEach(input => {
        input.value = token;
    });
    document.querySelectorAll('[data-add-to-cart]').forEach(button => {
        button.dataset.token = token;
    });
}
