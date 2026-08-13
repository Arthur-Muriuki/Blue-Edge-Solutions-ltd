<?php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once 'includes/db_connect.php';

// 1. Read incoming JSON payload
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON payload received.']);
    exit();
}

$cart_items = $data['cart'] ?? $data['items'] ?? [];

if (empty($cart_items)) {
    echo json_encode(['success' => false, 'error' => 'Your cart is empty.']);
    exit();
}

// Default client details for WhatsApp orders
$client_name  = !empty($data['name']) ? trim($data['name']) : 'WhatsApp Customer';
$client_phone = !empty($data['phone']) ? trim($data['phone']) : 'WhatsApp Lead';
$client_email = !empty($data['email']) ? trim($data['email']) : null;
$notes        = !empty($data['notes']) ? trim($data['notes']) : 'Direct order via WhatsApp';

try {
    // 2. Create tables if they do not exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            reference_code VARCHAR(20) NOT NULL UNIQUE,
            client_name VARCHAR(100) DEFAULT 'WhatsApp Customer',
            client_phone VARCHAR(50) DEFAULT 'N/A',
            client_email VARCHAR(100) DEFAULT NULL,
            notes TEXT DEFAULT NULL,
            total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            status VARCHAR(20) NOT NULL DEFAULT 'PENDING',
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

    // 3. Auto-patch existing 'orders' table if created prior to column updates
    $required_columns = [
        'reference_code' => "VARCHAR(50) DEFAULT NULL",
        'client_name'    => "VARCHAR(100) DEFAULT 'WhatsApp Customer'",
        'client_phone'   => "VARCHAR(50) DEFAULT 'N/A'",
        'client_email'   => "VARCHAR(100) DEFAULT NULL",
        'notes'          => "TEXT DEFAULT NULL",
        'total_amount'   => "DECIMAL(10,2) NOT NULL DEFAULT 0.00",
        'status'         => "VARCHAR(20) NOT NULL DEFAULT 'PENDING'"
    ];

    foreach ($required_columns as $col => $definition) {
        try {
            $pdo->exec("ALTER TABLE orders ADD COLUMN $col $definition");
        } catch (PDOException $e) {
            // Ignored if column already exists
        }
    }

    // 4. Calculate order total
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $qty   = intval($item['qty'] ?? $item['quantity'] ?? 1);
        $price = floatval($item['price'] ?? 0);
        $total_amount += ($price * $qty);
    }

    // 5. Generate unique ticket reference code
    $reference_code = 'BES-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

    // 6. Save order & items within transaction
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO orders (reference_code, client_name, client_phone, client_email, notes, total_amount, status)
        VALUES (:ref, :name, :phone, :email, :notes, :total, 'PENDING')
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
            ':title'      => $item['title'] ?? 'Item',
            ':price'      => floatval($item['price'] ?? 0),
            ':quantity'   => intval($item['qty'] ?? $item['quantity'] ?? 1),
            ':item_type'  => $item['type'] ?? 'product'
        ]);
    }

    $pdo->commit();

    // 7. Dynamically construct complete ticket URL
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    $ticket_url = "{$protocol}://{$host}{$dir}/ticket.php?ref=" . $reference_code;

    echo json_encode([
        'success'        => true,
        'reference_code' => $reference_code,
        'ticket_url'     => $ticket_url
    ]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false,
        'error'   => 'Database error: ' . $e->getMessage()
    ]);
}