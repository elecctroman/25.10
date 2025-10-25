(function () {
    const sidebarToggle = document.querySelector('[data-toggle="sidebar"]');
    const sidebar = document.querySelector('.sidebar');
    const themeToggle = document.querySelector('[data-toggle="theme"]');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    }

    if (themeToggle) {
        const preferred = localStorage.getItem('theme-mode') || 'light';
        if (preferred === 'dark') {
            document.documentElement.dataset.bsTheme = 'dark';
        }
        themeToggle.addEventListener('click', function () {
            const current = document.documentElement.dataset.bsTheme === 'dark' ? 'dark' : 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            if (next === 'dark') {
                document.documentElement.dataset.bsTheme = 'dark';
            } else {
                document.documentElement.removeAttribute('data-bs-theme');
            }
            localStorage.setItem('theme-mode', next);
        });
    }

    const toastTriggers = document.querySelectorAll('[data-toast-target]');
    toastTriggers.forEach(function (trigger) {
        const targetSelector = trigger.getAttribute('data-toast-target');
        const toastEl = document.querySelector(targetSelector);
        if (!toastEl) {
            return;
        }
        trigger.addEventListener('click', function () {
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        });
    });
})();
