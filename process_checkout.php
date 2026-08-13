<?php
session_start();
require_once 'includes/db_connect.php';

// Prevent direct access without POST submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit();
}

// 1. Verify Cart is not empty
if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    die("Your cart is empty. Please add items or services before checking out.");
}

// 2. Sanitize Customer Inputs
$client_name  = trim($_POST['client_name'] ?? '');
$client_phone = trim($_POST['client_phone'] ?? '');
$client_email = filter_var(trim($_POST['client_email'] ?? ''), FILTER_VALIDATE_EMAIL) ?: null;
$notes        = trim($_POST['notes'] ?? '');

if (empty($client_name) || empty($client_phone)) {
    die("Please fill in all required fields (Name and Phone Number).");
}

// 3. Calculate Cart Total
$grand_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $price = floatval($item['price'] ?? 0);
    $qty   = intval($item['quantity'] ?? 1);
    $grand_total += ($price * $qty);
}

// 4. Generate Unique Master Reference Code (e.g., BES-7B9A2)
$ref_code = 'BES-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

try {
    // Start Database Transaction for Atomicity
    $pdo->beginTransaction();

    // Insert Master Order
    $stmt_order = $pdo->prepare("
        INSERT INTO orders (reference_code, client_name, client_phone, client_email, payment_method, notes, status, total_amount)
        VALUES (:ref_code, :name, :phone, :email, 'Pay on Delivery / Invoice', :notes, 'PENDING', :total)
    ");

    $stmt_order->execute([
        ':ref_code' => $ref_code,
        ':name'     => $client_name,
        ':phone'    => $client_phone,
        ':email'    => $client_email,
        ':notes'    => $notes,
        ':total'    => $grand_total
    ]);

    $order_id = $pdo->lastInsertId();

    // Insert Line Items
    $stmt_item = $pdo->prepare("
        INSERT INTO order_items (order_id, item_type, item_title, quantity, unit_price, subtotal)
        VALUES (:order_id, :item_type, :title, :qty, :price, :subtotal)
    ");

    foreach ($_SESSION['cart'] as $item) {
        $type     = in_array($item['item_type'], ['hardware', 'subscription', 'booking'], true) ? $item['item_type'] : 'hardware';
        $title    = $item['title'] ?? 'Requested Item';
        $qty      = max(1, intval($item['quantity'] ?? 1));
        $price    = floatval($item['price'] ?? 0);
        $subtotal = $price * $qty;

        $stmt_item->execute([
            ':order_id'  => $order_id,
            ':item_type' => $type,
            ':title'     => $title,
            ':qty'       => $qty,
            ':price'     => $price,
            ':subtotal'  => $subtotal
        ]);
    }

    // Commit Transaction to DB
    $pdo->commit();

    // Clear Cart
    unset($_SESSION['cart']);

    // Redirect to Printable Ticket / Confirmation Page
    header("Location: ticket.php?ref=" . urlencode($ref_code));
    exit();

} catch (PDOException $e) {
    // Rollback DB actions on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Checkout Error: " . $e->getMessage());
    die("An error occurred while processing your request. Please try again or contact support directly.");
}