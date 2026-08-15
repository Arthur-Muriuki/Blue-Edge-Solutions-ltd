// assets/js/carts.js - Synchronized Cart & Drawer Engine

(function () {
    if (window.BES_CART_ENGINE_LOADED) return;
    window.BES_CART_ENGINE_LOADED = true;

    var CART_KEY = 'bes_cart_items';
    var ADMIN_WHATSAPP_NUMBER = '254722942293';

    // 1. Fetch Cart Items
    window.getCart = function() {
        try {
            return JSON.parse(localStorage.getItem(CART_KEY)) || [];
        } catch (e) {
            return [];
        }
    };

    // 2. Save Cart & Sync Cookie
    window.saveCart = function(cart) {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
        const expires = new Date(Date.now() + 30 * 86400 * 1000).toUTCString();
        document.cookie = `saved_guest_cart=${encodeURIComponent(JSON.stringify(cart))}; expires=${expires}; path=/; SameSite=Lax`;
    };

    // 3. Custom Toast Notification
    window.showToast = function(message) {
        let toast = document.getElementById('custom-cart-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'custom-cart-toast';
            toast.style.cssText = `
                position: fixed; bottom: 30px; right: 30px; background: #002d62; color: #ffffff;
                padding: 14px 22px; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.25);
                font-family: system-ui, -apple-system, sans-serif; font-size: 0.92rem; font-weight: 600;
                display: flex; align-items: center; gap: 12px; z-index: 999999; border-left: 5px solid #ff7300;
                transform: translateY(100px); opacity: 0; transition: all 0.35s ease; pointer-events: none;
            `;
            document.body.appendChild(toast);
        }

        toast.innerHTML = `<span style="font-size: 1.2rem; line-height:1;">🛒</span> <span>${message}</span>`;
        requestAnimationFrame(() => {
            toast.style.transform = 'translateY(0)';
            toast.style.opacity = '1';
        });

        clearTimeout(window.toastTimer);
        window.toastTimer = setTimeout(() => {
            toast.style.transform = 'translateY(100px)';
            toast.style.opacity = '0';
        }, 3000);
    };

    // 4. Render Cart Drawer Contents
    window.renderCartDrawer = function(cart) {
        const container = document.getElementById('cartDrawerItems');
        const subtotalEl = document.getElementById('cartDrawerSubtotal');
        let subtotal = 0;

        if (container) {
            if (!cart || cart.length === 0) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 50px 20px; color: #64748b;">
                        <div style="font-size: 2.5rem; margin-bottom: 10px;">🛒</div>
                        <p style="margin: 0; font-weight: 600;">Your cart is currently empty.</p>
                    </div>`;
            } else {
                let html = '<div style="display: flex; flex-direction: column; gap: 12px;">';
                cart.forEach((item, index) => {
                    const price = parseFloat(item.price) || 0;
                    const qty = parseInt(item.qty) || 1;
                    subtotal += price * qty;

                    html += `
                        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                            <div style="flex: 1; padding-right: 10px;">
                                <div style="font-size: 0.9rem; font-weight: 700; color: #002d62; margin-bottom: 4px;">${item.title}</div>
                                <div style="font-size: 0.82rem; color: #64748b;">
                                    Ksh ${price.toLocaleString()} x ${qty}
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <button onclick="changeCartQty(${index}, -1)" style="background: #e2e8f0; border: none; width: 24px; height: 24px; border-radius: 4px; font-weight: bold; cursor: pointer;">-</button>
                                <span style="font-weight: 600; font-size: 0.88rem; min-width: 16px; text-align: center;">${qty}</span>
                                <button onclick="changeCartQty(${index}, 1)" style="background: #e2e8f0; border: none; width: 24px; height: 24px; border-radius: 4px; font-weight: bold; cursor: pointer;">+</button>
                                <button onclick="removeFromCart(${index})" style="background: transparent; border: none; color: #dc2626; font-size: 1.2rem; cursor: pointer; margin-left: 6px; line-height: 1;">&times;</button>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            }
        } else if (cart && cart.length > 0) {
            subtotal = cart.reduce((sum, item) => sum + ((parseFloat(item.price) || 0) * (parseInt(item.qty) || 1)), 0);
        }

        if (subtotalEl) {
            subtotalEl.innerText = `Ksh ${subtotal.toLocaleString()}`;
        }
    };

    // 5. Update UI
    window.updateCartUI = function() {
        const cart = getCart();
        const totalQty = cart.reduce((sum, item) => sum + parseInt(item.qty || 1), 0);

        const countElements = document.querySelectorAll('#cartCount, #mobileCartCount, .cart-count');
        countElements.forEach(elem => {
            elem.innerText = totalQty;
        });

        renderCartDrawer(cart);
    };

    // 6. Add To Cart
    window.addToCart = function(id, title, price, itemType = 'product', qty = 1) {
        let cart = getCart();
        const parsedPrice = parseFloat(price) || 0;
        const parsedQty = parseInt(qty) || 1;

        const existingIndex = cart.findIndex(item => item.id == id && item.item_type === itemType);

        if (existingIndex > -1) {
            cart[existingIndex].qty += parsedQty;
        } else {
            cart.push({ id: id, title: title, price: parsedPrice, qty: parsedQty, item_type: itemType });
        }

        saveCart(cart);
        updateCartUI();
        showToast(`"${title}" added to cart!`);
    };

    // 7. Quantity & Item Controls
    window.changeCartQty = function(index, delta) {
        let cart = getCart();
        if (cart[index]) {
            cart[index].qty += delta;
            if (cart[index].qty <= 0) cart.splice(index, 1);
            saveCart(cart);
            updateCartUI();
        }
    };

    window.removeFromCart = function(index) {
        let cart = getCart();
        if (cart[index]) {
            cart.splice(index, 1);
            saveCart(cart);
            updateCartUI();
        }
    };

    window.clearCart = function() {
        localStorage.removeItem(CART_KEY);
        document.cookie = "saved_guest_cart=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        updateCartUI();
    };

    // 8. Process Checkout
    window.processCheckout = function(checkoutUrl = 'checkout.php') {
        const cart = getCart();
        if (!cart || cart.length === 0) {
            showToast('Your cart is empty!');
            return;
        }

        const waWindow = window.open('about:blank', '_blank');
        showToast('Processing order...');

        fetch(checkoutUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cart: cart })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let ref = data.reference_code || data.reference || '';
                let total = data.total_amount || 0;
                let ticketUrl = data.ticket_url || (ref ? `checkout.php?order=${ref}` : '');

                let message = `*NEW ORDER ${ref ? '#' + ref : ''}*\n\n*Items Ordered:*\n`;
                cart.forEach((item, i) => {
                    const price = parseFloat(item.price) || 0;
                    const qty = parseInt(item.qty) || 1;
                    message += `${i + 1}. ${item.title} (x${qty}) - Ksh ${(price * qty).toLocaleString()}\n`;
                });

                if (total) {
                    message += `\n*Total Amount:* Ksh ${parseFloat(total).toLocaleString()}\n`;
                }
                if (ticketUrl) {
                    message += `*Order Ticket:* ${ticketUrl}\n`;
                }

                message += `\nHello! I would like to complete this order.`;
                const whatsappUrl = data.whatsapp_url || `https://wa.me/${ADMIN_WHATSAPP_NUMBER}?text=${encodeURIComponent(message)}`;

                clearCart();

                if (waWindow) {
                    waWindow.location.href = whatsappUrl;
                } else {
                    window.open(whatsappUrl, '_blank');
                }

                if (ticketUrl) {
                    window.location.href = ticketUrl;
                }
            } else {
                if (waWindow) waWindow.close();
                showToast(data.message || 'Failed to process checkout.');
            }
        })
        .catch(error => {
            if (waWindow) waWindow.close();
            console.error('Checkout error:', error);
            showToast('An error occurred during checkout.');
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateCartUI);
    } else {
        updateCartUI();
    }
})();