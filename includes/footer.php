<footer class="main-footer" style="background: #001f44; color: #cbd5e1; padding: 40px 0 20px; margin-top: 60px;">
        <div class="container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; margin-bottom: 30px;">
            <div>
                <h3 style="color: white; margin-bottom: 15px;">Blue Edge Solutions</h3>
                <p style="font-size: 0.9rem; line-height: 1.6;">Providing reliable IT infrastructure, hardware sales, cloud support, and custom software systems.</p>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 15px;">Quick Links</h4>
                <ul style="list-style: none; padding: 0; line-height: 2;">
                    <li><a href="<?php echo $base ?? ''; ?>index.php" style="color: #cbd5e1; text-decoration: none;">Home</a></li>
                    <li><a href="<?php echo $base ?? ''; ?>services.php" style="color: #cbd5e1; text-decoration: none;">Services</a></li>
                    <li><a href="<?php echo $base ?? ''; ?>shop.php" style="color: #cbd5e1; text-decoration: none;">Hardware Shop</a></li>
                    <li><a href="<?php echo $base ?? ''; ?>ticket.php" style="color: #cbd5e1; text-decoration: none;">Track Order / Ticket</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 15px;">Legal & Trust</h4>
                <ul style="list-style: none; padding: 0; line-height: 2;">
                    <li><a href="<?php echo $base ?? ''; ?>privacy-policy.php" style="color: #cbd5e1; text-decoration: none;">Privacy Policy</a></li>
                    <li><a href="<?php echo $base ?? ''; ?>contact.php" style="color: #cbd5e1; text-decoration: none;">Help & Support</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 15px;">Contact Info</h4>
                <p style="font-size: 0.9rem; line-height: 1.8; margin: 0;">
                    📍 Nairobi, Kenya<br>
                    📞 +254 722 942 293<br>
                    ✉️ info@blueedge.co.ke
                </p>
            </div>
        </div>

        <div style="border-top: 1px solid #1e293b; padding-top: 20px; text-align: center; font-size: 0.85rem; color: #94a3b8;">
            &copy; <?php echo date('Y'); ?> Blue Edge Solutions Limited. All rights reserved.
        </div>
    </footer>

    <!-- ========================================== -->
    <!-- FLOATING BOTTOM-LEFT COOKIE CONSENT BANNER -->
    <!-- ========================================== -->
    <?php if (!isset($_COOKIE['cookie_consent'])): ?>
    <div id="cookieConsentBanner" style="position: fixed; bottom: 20px; left: 20px; max-width: 400px; width: calc(100% - 40px); background: #ffffff; color: #0f172a; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.18); border: 1px solid #e2e8f0; padding: 20px; z-index: 99999; transition: all 0.3s ease;">
        <div style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px;">
            <span style="font-size: 1.5rem; line-height: 1;">🍪</span>
            <div>
                <h4 style="margin: 0 0 4px 0; color: #002d62; font-size: 1rem;">We Value Your Privacy</h4>
                <p style="margin: 0; font-size: 0.85rem; color: #475569; line-height: 1.4;">
                    We use cookies to save cart items, remember device preferences, and look up tickets. Read our <a href="<?php echo $base ?? ''; ?>privacy-policy.php" style="color: #ff7300; font-weight: bold; text-decoration: underline;">Privacy Policy</a>.
                </p>
            </div>
        </div>
        <div style="display: flex; gap: 10px; justify-content: flex-end; align-items: center;">
            <button onclick="declineCookies()" style="background: transparent; color: #64748b; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 6px; font-weight: bold; font-size: 0.85rem; cursor: pointer; transition: all 0.2s;">
                Decline
            </button>
            <button onclick="acceptCookies()" style="background: #002d62; color: #ffffff; border: none; padding: 8px 18px; border-radius: 6px; font-weight: bold; font-size: 0.85rem; cursor: pointer; transition: background 0.2s;">
                Accept All
            </button>
        </div>
    </div>

    <script>
    function setConsentCookie(status) {
        const date = new Date();
        date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
        document.cookie = "cookie_consent=" + status + "; expires=" + date.toUTCString() + "; path=/; SameSite=Lax";
        
        const banner = document.getElementById('cookieConsentBanner');
        if (banner) {
            banner.style.opacity = '0';
            banner.style.transform = 'translateY(10px)';
            setTimeout(() => banner.style.display = 'none', 300);
        }
    }

    function acceptCookies() {
        setConsentCookie('accepted');
    }

    function declineCookies() {
        setConsentCookie('declined');
    }
    </script>
    <?php endif; ?>

</body>
</html>