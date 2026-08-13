// assets/js/carts.js - Manages shopping cart items & checkout submission

const CART_KEY = 'bes_cart_items';

// 1. Get items from browser storage
function getCart() {
    return JSON.parse(localStorage.getItem(CART_KEY)) || [];
}

// 2. Save items to browser storage
function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
}

// 3. Add any item (hardware, subscription, or booking)
function addToCart(id, title, price, itemType = 'hardware', qty = 1) {
    let cart = getCart();

    const existingIndex = cart.findIndex(item => item.id === id && item.item_type === itemType);

    if (existingIndex > -1) {
        cart[existingIndex].qty += parseInt(qty);
    } else {
        cart.push({
            id: id,
            title: title,
            price: parseFloat(price),
            qty: parseInt(qty),
            item_type: itemType
        });
    }

    saveCart(cart);
    alert(`"${title}" added to cart!`);
}

// 4. Clear cart after successful checkout
function clearCart() {
    localStorage.removeItem(CART_KEY);
}

// 5. Send cart and client details to checkout.php
async function processCheckout(clientDetails) {
    const cart = getCart();

    if (cart.length === 0) {
        alert('Your cart is empty!');
        return;
    }

    const payload = {
        client_name: clientDetails.name,
        client_phone: clientDetails.phone,
        client_email: clientDetails.email || '',
        notes: clientDetails.notes || '',
        cart: cart
    };

    try {
        const response = await fetch('checkout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (response.ok && result.success) {
            clearCart();
            window.location.href = result.ticket_url; // Go to ticket page
        } else {
            alert('Error: ' + (result.error || 'Unable to place order.'));
        }
    } catch (error) {
        console.error('Checkout error:', error);
        alert('Network error. Please try again.');
    }
}