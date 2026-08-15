<?php
// checkout.php - Direct Instant WhatsApp Order Dispatcher
error_reporting(E_ALL);
ini_set('display_errors', 0);

// 1. Ensure PHP Session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -------------------------------------------------------------------------
// CONFIGURATION: Put your WhatsApp phone number here (Country Code + Number)
// -------------------------------------------------------------------------
$whatsapp_phone = '254700000000'; 

// 2. Auto-detect database connection
if (file_exists(__DIR__ . '/db_connect.php')) {
    require_once __DIR__ . '/db_connect.php';
} elseif (file_exists(__DIR__ . '/includes/db_connect.php')) {
    require_once __DIR__ . '/includes/db_connect.php';
}

// 3. Read incoming cart data (from POST JSON, $_POST, or $_SESSION)
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data && !empty($_POST)) {
    $data = $_POST;
}

$cart_items = $data['cart'] ?? $data['items'] ?? $_SESSION['cart'] ?? [];

// Sync persistent guest cookie cart if session is empty
if (empty($cart_items) && isset($_COOKIE['saved_guest_cart'])) {
    $decoded_cart = json_decode($_COOKIE['saved_guest_cart'], true);
    if (is_array($decoded_cart) && !empty($decoded_cart)) {
        $cart_items = $decoded_cart;
    }
}

// If cart is completely empty, send them back to the shop
if (empty($cart_items)) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Your cart is empty.']);
        exit();
    } else {
        header('Location: shop.php');
        exit();
    }
}

// Extract optional client details if provided, or default to WhatsApp Lead
$client_name  = !empty($data['client_name']) ? trim($data['client_name']) : (!empty($data['name']) ? trim($data['name']) : 'WhatsApp Client');
$client_phone = !empty($data['phone']) ? trim($data['phone']) : 'WhatsApp Direct';
$client_email = !empty($data['email']) ? trim($data['email']) : null;
$notes        = !empty($data['notes']) ? trim($data['notes']) : 'Direct order via WhatsApp';

$reference_code = 'BES-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

try {
    if (isset($pdo)) {
        // Auto-create database tables if not existing
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                reference_code VARCHAR(50) NOT NULL UNIQUE,
                client_name VARCHAR(100) DEFAULT 'WhatsApp Client',
                client_phone VARCHAR(50) DEFAULT 'N/A',
                client_email VARCHAR(100) DEFAULT NULL,
                notes TEXT DEFAULT NULL,
                total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                status VARCHAR(20) NOT NULL DEFAULT 'Pending',
                is_read TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT DEFAULT NULL,
                title VARCHAR(255) NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                quantity INT NOT NULL DEFAULT 1,
                item_type VARCHAR(50) DEFAULT 'product',
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // Calculate totals and build text summary for WhatsApp
        $total_amount = 0;
        $items_summary = "";

        foreach ($cart_items as $item) {
            $qty      = intval($item['qty'] ?? $item['quantity'] ?? 1);
            $price    = floatval($item['price'] ?? 0);
            $subtotal = $price * $qty;
            $total_amount += $subtotal;

            $title = $item['title'] ?? $item['name'] ?? 'Item';
            $items_summary .= "• *{$title}* (x{$qty}) - Ksh " . number_format($subtotal, 2) . "\n";
        }

        // Save inside Database Transaction
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO orders (reference_code, client_name, client_phone, client_email, notes, total_amount, status, is_read)
            VALUES (:ref, :name, :phone, :email, :notes, :total, 'Pending', 0)
        ");
        $stmt->execute([
            ':ref'   => $reference_code,
            ':name'  => $client_name,
            ':phone' => $client_phone,
            ':email' => $client_email,
            ':notes' => $notes,
            ':total' => $total_amount
        ]);

        $order_id = $pdo->lastInsertId();

        $item_stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, title, price, quantity, item_type)
            VALUES (:order_id, :product_id, :title, :price, :quantity, :item_type)
        ");

        foreach ($cart_items as $item) {
            $item_stmt->execute([
                ':order_id'   => $order_id,
                ':product_id' => !empty($item['id']) ? intval($item['id']) : null,
                ':title'      => $item['title'] ?? $item['name'] ?? 'Item',
                ':price'      => floatval($item['price'] ?? 0),
                ':quantity'   => intval($item['qty'] ?? $item['quantity'] ?? 1),
                ':item_type'  => $item['type'] ?? $item['item_type'] ?? 'product'
            ]);
        }

        $pdo->commit();
    } else {
        // Fallback calculation if DB connection is inactive
        $total_amount = 0;
        $items_summary = "";
        foreach ($cart_items as $item) {
            $qty      = intval($item['qty'] ?? $item['quantity'] ?? 1);
            $price    = floatval($item['price'] ?? 0);
            $subtotal = $price * $qty;
            $total_amount += $subtotal;

            $title = $item['title'] ?? $item['name'] ?? 'Item';
            $items_summary .= "• *{$title}* (x{$qty}) - Ksh " . number_format($subtotal, 2) . "\n";
        }
    }

    // --- CONSTRUCT THE WHATSAPP MESSAGE ---
    $wa_msg  = "🛍️ *NEW ORDER - BLUE EDGE SOLUTIONS*\n";
    $wa_msg .= "-----------------------------------\n";
    $wa_msg .= "*Order Ref:* #{$reference_code}\n";
    $wa_msg .= "-----------------------------------\n";
    $wa_msg .= "*Items Ordered:*\n{$items_summary}";
    $wa_msg .= "-----------------------------------\n";
    $wa_msg .= "*Total Amount:* Ksh " . number_format($total_amount, 2) . "\n";
    $wa_msg .= "-----------------------------------\n";
    $wa_msg .= "Hello! I would like to place this order.";

    $clean_phone  = preg_replace('/[^0-9]/', '', $whatsapp_phone);
    $whatsapp_url = "https://wa.me/{$clean_phone}?text=" . urlencode($wa_msg);

    // Save ticket reference cookie & clear active session cart
    setcookie('last_ticket_ref', $reference_code, [
        'expires'  => time() + (86400 * 90),
        'path'     => '/',
        'samesite' => 'Lax'
    ]);

    setcookie('saved_guest_cart', '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'samesite' => 'Lax'
    ]);

    unset($_SESSION['cart']);

    // --- EXECUTE INSTANT REDIRECT / RESPONSE ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode([
            'success'      => true,
            'whatsapp_url' => $whatsapp_url,
            'reference'    => $reference_code
        ]);
        exit();
    } else {
        // Immediate browser redirect straight to WhatsApp
        header("Location: " . $whatsapp_url);
        exit();
    }

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // In case DB errors out, generate WhatsApp link anyway so you don't lose the customer!
    $clean_phone  = preg_replace('/[^0-9]/', '', $whatsapp_phone);
    $fallback_url = "https://wa.me/{$clean_phone}?text=" . urlencode("Hello! I would like to make an order.");
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'whatsapp_url' => $fallback_url]);
    } else {
        header("Location: " . $fallback_url);
    }
    exit();
}