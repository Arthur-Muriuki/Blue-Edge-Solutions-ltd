<?php
// process_whatsapp_order.php - Public endpoint for processing customer WhatsApp orders
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Locate and require database connection
if (file_exists(__DIR__ . '/includes/db_connect.php')) {
    require_once __DIR__ . '/includes/db_connect.php';
} elseif (file_exists(__DIR__ . '/config/db.php')) {
    require_once __DIR__ . '/config/db.php';
} elseif (file_exists(__DIR__ . '/db_connect.php')) {
    require_once __DIR__ . '/db_connect.php';
} else {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$cart = $input['cart'] ?? [];

if (empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
    exit();
}

$reference_code = 'BES-' . strtoupper(substr(uniqid(), -6));
$total_amount = 0;

foreach ($cart as $item) {
    $price = floatval($item['price'] ?? 0);
    $qty = intval($item['qty'] ?? 1);
    $total_amount += ($price * $qty);
}

try {
    $pdo->beginTransaction();

    // 1. Save main order
    $stmt = $pdo->prepare("INSERT INTO orders (reference_code, total_amount, channel, status, created_at) VALUES (?, ?, 'WhatsApp', 'Pending', NOW())");
    $stmt->execute([$reference_code, $total_amount]);
    $order_id = $pdo->lastInsertId();

    // 2. Save individual items
    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, item_name, price, qty) VALUES (?, ?, ?, ?)");
    foreach ($cart as $item) {
        $title = $item['title'] ?? $item['name'] ?? 'Item';
        $price = floatval($item['price'] ?? 0);
        $qty = intval($item['qty'] ?? 1);
        $itemStmt->execute([$order_id, $title, $price, $qty]);
    }

    // 3. Auto-create activity_logs table if missing
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS activity_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT DEFAULT NULL,
            admin_name VARCHAR(100) NOT NULL,
            admin_avatar VARCHAR(255) DEFAULT NULL,
            admin_role VARCHAR(50) DEFAULT 'SYSTEM',
            action_type VARCHAR(50) NOT NULL,
            description TEXT NOT NULL,
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // 4. Log customer activity for Super-Admin bell notifications
    $notifMsg = "New WhatsApp Order #{$reference_code} (Ksh " . number_format($total_amount) . ")";
    $logStmt = $pdo->prepare("
        INSERT INTO activity_logs (admin_id, admin_name, admin_role, action_type, description, is_read)
        VALUES (NULL, 'Customer (WhatsApp)', 'GUEST', 'new_order', :desc, 0)
    ");
    $logStmt->execute([':desc' => $notifMsg]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'reference_code' => $reference_code,
        'total_amount' => $total_amount
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}