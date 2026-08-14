// Wait for the HTML document to fully load before running the script
document.addEventListener('DOMContentLoaded', () => {
    
    // Select the button and the menu
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.querySelector('.mobile-menu');
    const menuIcon = mobileMenuBtn ? mobileMenuBtn.querySelector('i') : null;

    // Mobile Menu Toggle
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
            
            if (mobileMenu.classList.contains('active')) {
                if (menuIcon) {
                    menuIcon.classList.remove('ph-list');
                    menuIcon.classList.add('ph-x');
                }
            } else {
                if (menuIcon) {
                    menuIcon.classList.remove('ph-x');
                    menuIcon.classList.add('ph-list');
                }
            }
        });
    }

});