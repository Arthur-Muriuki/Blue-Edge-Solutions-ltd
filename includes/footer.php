<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$isUsercraft = ($currentPage === 'usercraft.php' || (isset($page_title) && strpos(strtolower($page_title), 'usercraft') !== false));
$base = $base_url ?? '';
?>

<!-- MOBILE NAVIGATION OVERLAY DRAWER -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="toggleMobileMenu()"></div>
<div class="mobile-menu" id="mobileMenu">
    <div class="mobile-menu-header">
        <span class="brand-logo" style="font-size: 1.2rem; font-weight: 800; color: white;">
            <?php echo $isUsercraft ? 'UserCraft<span>.</span>' : 'Blue Edge<span>.</span>'; ?>
        </span>
        <button type="button" class="mobile-menu-close" onclick="toggleMobileMenu()" aria-label="Close Menu">&times;</button>
    </div>
    <ul class="mobile-nav-links">
        <li><a href="<?php echo $base; ?>index.php">Home</a></li>
        <li><a href="<?php echo $base; ?>about.php">About Us</a></li>
        <li><a href="<?php echo $base; ?>services.php">Services</a></li>
        <li><a href="<?php echo $base; ?>shop.php">Shop</a></li>
        <li><a href="<?php echo $base; ?>contact.php">Contact</a></li>
        <li><a href="<?php echo $base; ?>blog.php">Blog</a></li>
        <li><a href="<?php echo $base; ?>usercraft.php">UserCraft</a></li>
        <li><a href="<?php echo $base; ?>help.php">Help Center</a></li>
    </ul>
    <div style="margin-top: auto; padding-top: 20px;">
        <a href="<?php echo $base; ?>contact.php" class="btn btn-primary" style="display: block; text-align: center; width: 100%;">Get in Touch</a>
    </div>
</div>

<footer class="main-footer" style="background: <?php echo $isUsercraft ? '#0f172a' : '#001f44'; ?>; color: #cbd5e1; padding: 40px 0 20px; margin-top: 60px; border-top: <?php echo $isUsercraft ? '3px solid #ff7300' : 'none'; ?>;">
    <div class="container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; margin-bottom: 30px;">
        <div>
            <?php if ($isUsercraft): ?>
                <h3 style="color: white; margin-bottom: 15px;">UserCraft Consult</h3>
                <p style="font-size: 0.9rem; line-height: 1.6;">Precision IT support and seamless systems integration. A proud affiliate of Blue Edge Solutions.</p>
            <?php else: ?>
                <h3 style="color: white; margin-bottom: 15px;">Blue Edge Solutions</h3>
                <p style="font-size: 0.9rem; line-height: 1.6;">Providing reliable IT infrastructure, hardware sales, cloud support, and custom software systems.</p>
            <?php endif; ?>
        </div>

        <div>
            <h4 style="color: white; margin-bottom: 15px;">Quick Links</h4>
            <ul style="list-style: none; padding: 0; line-height: 2;">
                <li><a href="<?php echo $base; ?>index.php" style="color: #cbd5e1; text-decoration: none;">Home</a></li>
                <li><a href="<?php echo $base; ?>services.php" style="color: #cbd5e1; text-decoration: none;">Services</a></li>
                <li><a href="<?php echo $base; ?>shop.php" style="color: #cbd5e1; text-decoration: none;">Hardware Shop</a></li>
                <li><a href="<?php echo $base; ?>usercraft.php" style="color: <?php echo $isUsercraft ? '#ff7300' : '#cbd5e1'; ?>; text-decoration: none; font-weight: <?php echo $isUsercraft ? 'bold' : 'normal'; ?>;">UserCraft</a></li>
            </ul>
        </div>

        <div>
            <h4 style="color: white; margin-bottom: 15px;">Legal & Trust</h4>
            <ul style="list-style: none; padding: 0; line-height: 2;">
                <li><a href="<?php echo $base; ?>privacy-policy.php" style="color: #cbd5e1; text-decoration: none;">Privacy Policy</a></li>
                <li><a href="<?php echo $base; ?>contact.php" style="color: #cbd5e1; text-decoration: none;">Help & Support</a></li>
            </ul>
        </div>

        <div>
            <h4 style="color: white; margin-bottom: 15px;">Contact Info</h4>
            <p style="font-size: 0.9rem; line-height: 1.8; margin: 0;">
                <?php if ($isUsercraft): ?>
                    📍 Caxton House 1st Floor, Kenyatta Ave<br>
                    📦 P.O. Box 4119-00100 Nairobi<br>
                    📞 0722-146-546<br>
                    ✉️ usercraft@gmail.com
                <?php else: ?>
                    📍 Nairobi, Kenya<br>
                    📞 +254 722 942 293<br>
                    ✉️ info@blueedge.co.ke
                <?php endif; ?>
            </p>
        </div>
    </div>

    <div style="border-top: 1px solid #1e293b; padding-top: 20px; text-align: center; font-size: 0.85rem; color: #94a3b8;">
        <?php if ($isUsercraft): ?>
            &copy; <?php echo date('Y'); ?> UserCraft Consult. An Affiliate of Blue Edge Solutions Limited. All rights reserved.
        <?php else: ?>
            &copy; <?php echo date('Y'); ?> Blue Edge Solutions Limited. All rights reserved.
        <?php endif; ?>
    </div>
</footer>

<!-- SLIDE-OUT CART DRAWER PANEL -->
<div id="cartDrawerOverlay" onclick="toggleCart()" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 99998; transition: opacity 0.3s;"></div>

<div id="cartDrawer" style="position: fixed; top: 0; right: -400px; width: 100%; max-width: 380px; height: 100%; background: white; box-shadow: -5px 0 25px rgba(0,0,0,0.18); z-index: 99999; transition: right 0.3s ease; display: flex; flex-direction: column;">
    <div style="padding: 20px; background: #002d62; color: white; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">🛒 Shopping Cart</h3>
        <button onclick="toggleCart()" style="background: none; border: none; color: white; font-size: 1.6rem; cursor: pointer; line-height: 1;">&times;</button>
    </div>

    <div id="cartDrawerItems" style="flex-grow: 1; padding: 20px; overflow-y: auto;"></div>

    <div style="padding: 20px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; font-weight: bold; font-size: 1.1rem; color: #0f172a;">
            <span>Subtotal:</span>
            <span id="cartDrawerSubtotal" style="color: #ff7300;">Ksh 0</span>
        </div>
        <button onclick="processCheckout('<?php echo $base; ?>checkout.php')" style="display: block; width: 100%; background: #ff7300; color: white; text-align: center; padding: 12px; border-radius: 6px; font-weight: bold; border: none; cursor: pointer; font-size: 1rem;">
            Proceed to Checkout
        </button>
    </div>
</div>

<!-- FLOATING COOKIE CONSENT BANNER -->
<?php if (!isset($_COOKIE['cookie_consent'])): ?>
<div id="cookieConsentBanner" style="position: fixed; bottom: 20px; left: 20px; max-width: 400px; width: calc(100% - 40px); background: #ffffff; color: #0f172a; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.18); border: 1px solid #e2e8f0; padding: 20px; z-index: 99997;">
    <div style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px;">
        <span style="font-size: 1.5rem; line-height: 1;">🍪</span>
        <div>
            <h4 style="margin: 0 0 4px 0; color: #002d62; font-size: 1rem;">We Value Your Privacy</h4>
            <p style="margin: 0; font-size: 0.85rem; color: #475569; line-height: 1.4;">
                We use cookies to save cart items and remember preferences. Read our <a href="<?php echo $base; ?>privacy-policy.php" style="color: #ff7300; font-weight: bold;">Privacy Policy</a>.
            </p>
        </div>
    </div>
    <div style="display: flex; gap: 10px; justify-content: flex-end; align-items: center;">
        <button onclick="declineCookies()" style="background: transparent; color: #64748b; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 6px; font-weight: bold; font-size: 0.85rem; cursor: pointer;">Decline</button>
        <button onclick="acceptCookies()" style="background: #002d62; color: #ffffff; border: none; padding: 8px 18px; border-radius: 6px; font-weight: bold; font-size: 0.85rem; cursor: pointer;">Accept All</button>
    </div>
</div>

<script>
function setConsentCookie(status) {
    const date = new Date();
    date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
    document.cookie = "cookie_consent=" + status + "; expires=" + date.toUTCString() + "; path=/; SameSite=Lax";
    const banner = document.getElementById('cookieConsentBanner');
    if (banner) banner.style.display = 'none';
}
function acceptCookies() { setConsentCookie('accepted'); }
function declineCookies() { setConsentCookie('declined'); }
</script>
<?php endif; ?>

<script src="<?php echo $base; ?>assets/js/main.js"></script>
<script src="<?php echo $base; ?>assets/js/carts.js"></script>

<script>
function toggleCart() {
    const drawer = document.getElementById('cartDrawer');
    const overlay = document.getElementById('cartDrawerOverlay');
    if (!drawer) return;

    const isOpen = drawer.style.right === '0px' || drawer.classList.contains('open');

    if (isOpen) {
        drawer.style.right = '-400px';
        drawer.classList.remove('open');
        if (overlay) overlay.style.display = 'none';
    } else {
        drawer.style.right = '0px';
        drawer.classList.add('open');
        if (overlay) overlay.style.display = 'block';
        if (typeof updateCartUI === 'function') updateCartUI();
    }
}
</script>
</body>
</html>