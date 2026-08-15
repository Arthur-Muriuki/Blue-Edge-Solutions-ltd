/**
 * Global UI Script - Blue Edge Solutions & UserCraft
 */
document.addEventListener('DOMContentLoaded', () => {

    // --- 1. MOBILE NAVIGATION TOGGLE ---
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileOverlay = document.getElementById('mobileMenuOverlay');

    window.toggleMobileMenu = function() {
        if (!mobileMenu) return;
        const isActive = mobileMenu.classList.contains('active');

        if (isActive) {
            mobileMenu.classList.remove('active');
            if (mobileOverlay) mobileOverlay.classList.remove('active');
            updateMenuButtonIcon(false);
        } else {
            mobileMenu.classList.add('active');
            if (mobileOverlay) mobileOverlay.classList.add('active');
            updateMenuButtonIcon(true);
        }
    };

    function updateMenuButtonIcon(isOpen) {
        if (!mobileMenuBtn) return;
        const icon = mobileMenuBtn.querySelector('i');
        if (icon) {
            if (isOpen) {
                icon.classList.remove('ph-list');
                icon.classList.add('ph-x');
            } else {
                icon.classList.remove('ph-x');
                icon.classList.add('ph-list');
            }
        }
    }

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMobileMenu();
        });
    }

    // Close mobile menu when clicking outside or pressing Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (mobileMenu && mobileMenu.classList.contains('active')) {
                toggleMobileMenu();
            }
            const cartDrawer = document.getElementById('cartDrawer');
            if (cartDrawer && (cartDrawer.style.right === '0px' || cartDrawer.classList.contains('open'))) {
                if (typeof toggleCart === 'function') toggleCart();
            }
        }
    });

    // --- 2. SMOOTH SCROLL FOR ANCHORS ---
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId && targetId !== '#') {
                const targetElem = document.querySelector(targetId);
                if (targetElem) {
                    e.preventDefault();
                    targetElem.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    });
});