<?php
// update_order_status.php - Updates order status & logs sub-admin activity
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Locate and require database connection
if (file_exists(__DIR__ . '/db_connect.php')) {
    require_once __DIR__ . '/db_connect.php';
} elseif (file_exists(__DIR__ . '/includes/db_connect.php')) {
    require_once __DIR__ . '/includes/db_connect.php';
} elseif (file_exists(__DIR__ . '/../includes/db_connect.php')) {
    require_once __DIR__ . '/../includes/db_connect.php';
} else {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

// Check admin authentication
if (empty($_SESSION['admin_logged_in']) || !isset($pdo)) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$order_id = $input['order_id'] ?? $input['id'] ?? $_POST['order_id'] ?? $_POST['id'] ?? null;
$new_status = $input['status'] ?? $_POST['status'] ?? null;

if (!$order_id || !$new_status) {
    echo json_encode(['success' => false, 'error' => 'Order ID and new status are required']);
    exit();
}

try {
    // 1. Fetch current order details
    $stmt = $pdo->prepare("SELECT id, reference_code FROM orders WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(['success' => false, 'error' => 'Order not found']);
        exit();
    }

    $ref_code = $order['reference_code'] ?? $order['id'];

    // 2. Update status in database
    $updateStmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
    $updateStmt->execute([':status' => $new_status, ':id' => $order_id]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Order Update Failed: ' . $e->getMessage()]);
    exit();
}

// 3. SEPARATE TRY-CATCH FOR ACTIVITY LOGGING (To catch strict DB schema errors)
try {
    // Use NULL instead of 0 if session ID is missing to prevent Foreign Key failures
    $admin_id = !empty($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
    $admin_username = !empty($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Unknown Staff';
    
    $logStmt = $pdo->prepare("
        INSERT INTO activity_logs (admin_id, username, action, details, is_read, created_at) 
        VALUES (:admin_id, :username, :action, :details, 0, NOW())
    ");
    
    $logStmt->execute([
        ':admin_id' => $admin_id,
        ':username' => $admin_username,
        ':action'   => 'Order Updated',
        ':details'  => "Marked Order #{$ref_code} as {$new_status}"
    ]);

    echo json_encode(['success' => true, 'message' => 'Order updated and action logged successfully.']);

} catch (PDOException $e) {
    // If it fails here, the order updated, but logging failed. We return the exact SQL error.
    echo json_encode([
        'success' => false, 
        'error' => 'Order updated, BUT logging failed: ' . $e->getMessage()
    ]);
}