// Wait for the HTML document to fully load before running the script
document.addEventListener('DOMContentLoaded', () => {
    
    // Select the button and the menu
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.querySelector('.mobile-menu');
    const menuIcon = mobileMenuBtn.querySelector('i');

    // Add a click event listener
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            // Toggle the 'active' class on the menu
            mobileMenu.classList.toggle('active');
            
            // Optional: Change the icon from a hamburger (list) to an 'X' (x)
            if (mobileMenu.classList.contains('active')) {
                menuIcon.classList.remove('ph-list');
                menuIcon.classList.add('ph-x');
            } else {
                menuIcon.classList.remove('ph-x');
                menuIcon.classList.add('ph-list');
            }
        });
    }
});