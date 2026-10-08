import './bootstrap';

/**
 * SIM-PKL - shared vanilla JS behaviours.
 * Tidak menggunakan framework frontend (React/Vue/Alpine).
 */

document.addEventListener('DOMContentLoaded', () => {
    initMobileDrawer();
    initDropdowns();
    initModals();
    initFlashAutoHide();
});

/** Sidebar mobile drawer toggle */
function initMobileDrawer() {
    const toggleBtn = document.querySelector('[data-drawer-toggle]');
    const drawer = document.querySelector('[data-drawer]');
    const overlay = document.querySelector('[data-drawer-overlay]');

    if (!toggleBtn || !drawer || !overlay) return;

    const open = () => {
        drawer.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    };
    const close = () => {
        drawer.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    };

    toggleBtn.addEventListener('click', open);
    overlay.addEventListener('click', close);
}

/** Generic dropdown (notification bell, profile menu) */
function initDropdowns() {
    document.querySelectorAll('[data-dropdown-toggle]').forEach((toggle) => {
        const targetId = toggle.getAttribute('data-dropdown-toggle');
        const menu = document.getElementById(targetId);
        if (!menu) return;

        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            document.querySelectorAll('[data-dropdown-menu]').forEach((m) => {
                if (m !== menu) m.classList.add('hidden');
            });
            menu.classList.toggle('hidden');
        });
    });

    document.addEventListener('click', (e) => {
        document.querySelectorAll('[data-dropdown-menu]').forEach((menu) => {
            if (!menu.contains(e.target)) menu.classList.add('hidden');
        });
    });
}

/** Confirmation / generic modal system, opened via data-modal-open="modal-id" */
function initModals() {
    document.querySelectorAll('[data-modal-open]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const modal = document.getElementById(btn.getAttribute('data-modal-open'));
            if (modal) modal.classList.remove('hidden');
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('[data-modal]');
            if (modal) modal.classList.add('hidden');
        });
    });

    document.querySelectorAll('[data-modal]').forEach((modal) => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.add('hidden');
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[data-modal]:not(.hidden)').forEach((m) => m.classList.add('hidden'));
        }
    });
}

/** Auto-hide flash message/toast after a few seconds */
function initFlashAutoHide() {
    document.querySelectorAll('[data-flash]').forEach((flash) => {
        setTimeout(() => {
            flash.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            setTimeout(() => flash.remove(), 300);
        }, 4000);
    });
}

/**
 * Helper: disable a form's submit button and show a loading label
 * so users cannot double-submit important actions.
 * Usage: <form data-loading-text="Memproses...">
 */
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    const loadingText = form.getAttribute('data-loading-text');
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn && loadingText) {
        submitBtn.disabled = true;
        submitBtn.dataset.originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = loadingText;
    }
});
