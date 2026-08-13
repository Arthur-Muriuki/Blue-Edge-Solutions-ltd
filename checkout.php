<?php
header('Content-Type: application/json');

require_once 'includes/db_connect.php';

// 1. Ensure POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Invalid request method']));
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || empty($data['cart'])) {
    http_response_code(400);
    exit(json_encode(['error' => 'Cart is empty']));
}

$cart = $data['cart'];
$total_amount = 0;

// 2. Calculate Total
foreach ($cart as $item) {
    $total_amount += (floatval($item['price']) * intval($item['qty']));
}

// 3. Generate Reference Code
$ref_code = 'ORD-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

try {
    $pdo->beginTransaction();

    // Insert Order
    $stmt = $pdo->prepare("INSERT INTO orders (reference_code, total_amount, status) VALUES (:ref, :total, 'PENDING')");
    $stmt->execute([
        'ref'   => $ref_code, 
        'total' => $total_amount
    ]);
    $order_id = $pdo->lastInsertId();

    // Insert Order Items
    $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_title, quantity, price) VALUES (:order_id, :product_id, :title, :qty, :price)");
    
    foreach ($cart as $item) {
        $stmt_item->execute([
            'order_id'   => $order_id,
            'product_id' => intval($item['id']),
            'title'      => $item['title'],
            'qty'        => intval($item['qty']),
            'price'      => floatval($item['price'])
        ]);
    }

    $pdo->commit();

    // 4. Determine Protocol (HTTPS vs HTTP) safely using standard if statements
    $is_https = false;
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $is_https = true;
    } elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
        $is_https = true;
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $is_https = true;
    }

    $protocol = $is_https ? "https://" : "http://";
    $domain   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base_dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

    // 5. Build Dynamic Ticket URL
    $ticket_url = $protocol . $domain . $base_dir . "/ticket.php?ref=" . $ref_code;

    // 6. Return Clean JSON Response
    echo json_encode([
        'success'    => true,
        'reference'  => $ref_code,
        'ticket_url' => $ticket_url
    ]);
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit;
}
?>