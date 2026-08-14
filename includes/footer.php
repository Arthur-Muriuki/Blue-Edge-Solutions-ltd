<?php 
// Fallback if $base_url isn't defined on a root page
$base = $base_url ?? ''; 
?>
<footer class="main-footer">
    <div class="container footer-container">
        <div class="footer-brand">
            <h3>Blue Edge Solutions</h3>
            <p>Enterprise-grade IT Infrastructure, Cloud Management, and Cyber Security.</p>
        </div>
        <div class="footer-links">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?php echo $base; ?>services.php">Our Services</a></li>
                <li><a href="<?php echo $base; ?>shop.php">Hardware Shop</a></li>
                <li><a href="<?php echo $base; ?>contact.php">Support</a></li>
                <li><a href="<?php echo $base; ?>usercraft.php">UserCraft</a></li>
                <li><a href="<?php echo $base; ?>help.php">Help Center</a></li>
                <li><a href="<?php echo $base; ?>admin/login.php">Staff Access</a></li>
                <a href="<?php echo $base_url ?? ''; ?>privacy-policy.php" style="color: #cbd5e1; text-decoration: none;">Privacy Policy</a>
            </ul>
        </div>
        <div class="footer-contact">
            <h4>Contact Us</h4>
            <p><i class="ph ph-envelope-simple"></i> info@blueedge-sl.com</p>
            <p><i class="ph ph-phone"></i> +254 722 942 293 / +254 733 775 544</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> Blue Edge Solutions Limited. All rights reserved.</p>
    </div>
</footer>

<a href="https://wa.me/254722942293" class="whatsapp-float" target="_blank" rel="noopener noreferrer" aria-label="Chat with us on WhatsApp">
    <i class="ph-fill ph-whatsapp-logo"></i>
</a>

<!-- Dynamic JavaScript Path -->
<script src="<?php echo $base; ?>assets/js/main.js"></script>

<!-- Cart Modal Styles -->
<style>
    #cartModal { display: none; position: fixed; top: 0; right: 0; width: 100%; max-width: 400px; height: 100vh; background: white; box-shadow: -5px 0 25px rgba(0,0,0,0.15); z-index: 9999; flex-direction: column; }
    #cartOverlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9998; }
</style>

<!-- Dark Overlay -->
<div id="cartOverlay" onclick="toggleCart()"></div>

<!-- Slide-out Cart Modal -->
<div id="cartModal">
    <div style="padding: 20px; background: #002d62; color: white; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="margin: 0; font-size: 1.5rem;">Your Cart</h2>
        <button onclick="toggleCart()" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">&times;</button>
    </div>
    
    <div id="cartItemsContainer" style="padding: 20px; flex-grow: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 15px;">
        <!-- Cart items injected here by JS -->
    </div>

    <div style="padding: 20px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 1.2rem; font-weight: bold; color: #002d62;">
            <span>Total:</span>
            <span id="cartTotal">Ksh 0</span>
        </div>
        <button id="checkoutBtn" style="width: 100%; background: #25D366; color: white; border: none; padding: 15px; border-radius: 6px; font-weight: bold; font-size: 1.1rem; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 10px;">
            Checkout via WhatsApp
        </button>
    </div>
</div>

<!-- SUCCESS ADD TO CART TOAST NOTIFICATION -->
<div id="cartToast" style="display: none; position: fixed; bottom: 30px; right: 30px; background: #002d62; color: #ffffff; padding: 16px 20px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.25); z-index: 10000; align-items: center; gap: 12px; transition: opacity 0.3s ease; border-left: 4px solid #22c55e;">
    <div style="background: #22c55e; color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-weight: bold;">✓</div>
    <div>
        <strong style="display: block; font-size: 0.95rem; color: #ffffff;">Item Added to Cart!</strong>
        <span id="toastItemTitle" style="font-size: 0.85rem; color: #cbd5e1;"></span>
    </div>
    <button onclick="toggleCart()" style="background: #ff7300; color: white; border: none; padding: 8px 12px; border-radius: 4px; font-weight: bold; font-size: 0.8rem; cursor: pointer; margin-left: 10px; white-space: nowrap;">View Cart</button>
</div>

<!-- GLOBAL CART JAVASCRIPT -->
<script>
    let cart = JSON.parse(localStorage.getItem('blueedge_cart')) || [];
    const whatsappNumber = "254722942293"; 

    function saveCart() {
        localStorage.setItem('blueedge_cart', JSON.stringify(cart));
        renderCart();
    }

    function toggleCart() {
        const modal = document.getElementById('cartModal');
        const overlay = document.getElementById('cartOverlay');
        if (!modal || !overlay) return;
        const isOpen = modal.style.display === 'flex';
        modal.style.display = isOpen ? 'none' : 'flex';
        overlay.style.display = isOpen ? 'none' : 'block';
        if (!isOpen) renderCart();
    }

    function showToast(itemTitle) {
        const toast = document.getElementById('cartToast');
        const titleSpan = document.getElementById('toastItemTitle');
        if (!toast) return;

        titleSpan.innerText = itemTitle;
        toast.style.display = 'flex';
        toast.style.opacity = '1';

        clearTimeout(window.toastTimeout);
        window.toastTimeout = setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => { toast.style.display = 'none'; }, 300);
        }, 3500);
    }

    function addToCart(id, title, price) {
        const existing = cart.find(item => item.id == id);
        if (existing) {
            existing.qty++;
        } else {
            cart.push({ id, title, price: parseFloat(price), qty: 1 });
        }
        saveCart();
        
        // Show success toast popup
        showToast(title);
    }

    function removeFromCart(id) {
        cart = cart.filter(item => item.id != id);
        saveCart();
    }

    function renderCart() {
        const container = document.getElementById('cartItemsContainer');
        const totalCount = cart.reduce((sum, item) => sum + item.qty, 0);

        document.querySelectorAll('#cartCount, #mobileCartCount').forEach(el => {
            if (el) el.innerText = totalCount;
        });
        
        if (!container) return;

        if (cart.length === 0) {
            container.innerHTML = '<p style="text-align:center; color:#64748b; margin-top: 50px;">Your cart is empty.</p>';
            const totalEl = document.getElementById('cartTotal');
            if (totalEl) totalEl.innerText = 'Ksh 0';
            return;
        }

        let html = '';
        let total = 0;

        cart.forEach(item => {
            const itemTotal = item.price * item.qty;
            total += itemTotal;
            html += `
                <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 15px; border-bottom: 1px solid #e2e8f0;">
                    <div style="flex-grow: 1;">
                        <h4 style="margin: 0 0 5px 0; color: #002d62; font-size: 0.95rem;">${item.title}</h4>
                        <span style="color: #64748b; font-size: 0.9rem;">${item.qty} x Ksh ${item.price.toLocaleString()}</span>
                    </div>
                    <button onclick="removeFromCart(${item.id})" style="background: none; border: none; color: #ef4444; font-size: 1.2rem; cursor: pointer;">&times;</button>
                </div>
            `;
        });

        container.innerHTML = html;
        const totalEl = document.getElementById('cartTotal');
        if (totalEl) totalEl.innerText = `Ksh ${total.toLocaleString()}`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const checkoutBtn = document.getElementById('checkoutBtn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', async () => {
                if (cart.length === 0) return alert("Your cart is empty!");

                checkoutBtn.innerText = "Processing...";
                checkoutBtn.disabled = true;

                try {
                    const response = await fetch('checkout.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ cart: cart })
                    });
                    
                    const data = await response.json();

                    if (data.success) {
                        cart = [];
                        saveCart();
                        const waText = `Hello Blue Edge Solutions, I would like to place an order. Can you please confirm availability and provide the next steps?\n\nMy order reference is:\n${data.ticket_url}`;
                        const waUrl = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(waText)}`;
                        window.location.href = waUrl;
                    } else {
                        alert(data.error || "Error saving order. Please try again.");
                        checkoutBtn.innerText = "Checkout via WhatsApp";
                        checkoutBtn.disabled = false;
                    }
                } catch (err) {
                    console.error(err);
                    alert("Connection error. Please try again.");
                    checkoutBtn.innerText = "Checkout via WhatsApp";
                    checkoutBtn.disabled = false;
                }
            });
        }

        renderCart();
    });
</script>
<!-- Cookie Consent Banner -->
<?php if (!isset($_COOKIE['cookie_consent'])): ?>
<div id="cookie-banner" style="position: fixed; bottom: 0; left: 0; right: 0; background: #002d62; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; z-index: 9999; box-shadow: 0 -2px 10px rgba(0,0,0,0.2); font-size: 0.9rem;">
    <div>
        We use essential cookies to maintain your shopping cart and order tracking. Read our <a href="<?php echo $base_url ?? ''; ?>privacy-policy.php" style="color: #ff7300; font-weight: bold; text-decoration: underline;">Privacy Policy</a>.
    </div>
    <button onclick="acceptCookies()" style="background: #ff7300; color: white; border: none; padding: 8px 18px; border-radius: 4px; font-weight: bold; cursor: pointer; margin-left: 15px;">
        Got it!
    </button>
</div>

<script>
function acceptCookies() {
    let d = new Date();
    d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000)); // 1 Year
    document.cookie = "cookie_consent=true; expires=" + d.toUTCString() + "; path=/; SameSite=Lax";
    document.getElementById('cookie-banner').style.display = 'none';
}
</script>
<?php endif; ?>
</body>
</html>