(function () {
    const storageKey = 'theme';

    const AppUI = {
        init() {
            this.applyStoredTheme();
            this.registerThemeToggle();
            this.initTooltips();
            this.initToasts();
            this.bindSidebarToggle();
            this.highlightBottomNav();
            window.addEventListener('app:toast', (event) => {
                const { message, type, options } = event.detail || {};
                this.showToast(message, type, options);
            });
        },

        applyStoredTheme() {
            const stored = localStorage.getItem(storageKey);
            if (stored) {
                document.documentElement.setAttribute('data-bs-theme', stored);
            }
            this.reflectThemeIcons();
        },

        registerThemeToggle() {
            document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
                button.addEventListener('click', () => {
                    const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
                    const next = current === 'dark' ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-bs-theme', next);
                    localStorage.setItem(storageKey, next);
                    this.reflectThemeIcons();
                });
            });
        },

        reflectThemeIcons() {
            const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
            document.querySelectorAll('[data-theme-icon]').forEach((icon) => {
                const mode = icon.getAttribute('data-theme-icon');
                if (mode === current) {
                    icon.classList.remove('d-none');
                } else {
                    icon.classList.add('d-none');
                }
            });
        },

        initTooltips() {
            if (!window.bootstrap) return;
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
                new bootstrap.Tooltip(el);
            });
        },

        initToasts() {
            if (!window.bootstrap) return;
            const container = document.getElementById('toast-container');
            if (!container) return;
            container.querySelectorAll('.toast[data-auto-init]').forEach((toastEl) => {
                const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
                toast.show();
                toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
            });
        },

        showToast(message, type = 'info', options = {}) {
            if (!message) return;
            const container = document.getElementById('toast-container') || this.createToastContainer();
            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-bg-${type} border-0 fade`;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            toastEl.innerHTML = `<div class="d-flex"><div class="toast-body fw-medium">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Kapat"></button></div>`;
            container.appendChild(toastEl);
            const toast = new bootstrap.Toast(toastEl, { delay: options.delay || 3200 });
            toast.show();
            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        },

        createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.setAttribute('aria-live', 'polite');
            container.setAttribute('aria-atomic', 'true');
            document.body.appendChild(container);
            return container;
        },

        bindSidebarToggle() {
            const toggler = document.querySelector('[data-toggle="sidebar"]');
            const sidebar = document.querySelector('[data-sidebar]');
            if (!toggler || !sidebar) return;
            toggler.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        },

        highlightBottomNav() {
            const items = document.querySelectorAll('.bottom-nav__item');
            if (!items.length) return;
            const path = window.location.pathname;
            items.forEach((item) => {
                const href = item.getAttribute('href');
                if (!href) return;
                if (href === '/' && path === '/') {
                    item.classList.add('active');
                } else if (href !== '/' && path.startsWith(href)) {
                    item.classList.add('active');
                }
            });
        },

        showLoader() {
            const loader = document.getElementById('app-loader');
            if (!loader) return;
            loader.classList.remove('d-none');
        },

        hideLoader() {
            const loader = document.getElementById('app-loader');
            if (!loader) return;
            loader.classList.add('d-none');
        }
    };

    window.AppUI = AppUI;

    document.addEventListener('DOMContentLoaded', () => AppUI.init());
})();
