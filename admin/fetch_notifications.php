<?php
// fetch_notifications.php - Staff & Order Live Polling Endpoint
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

if (empty($_SESSION['admin_logged_in']) || !isset($pdo)) {
    echo json_encode(['success' => false, 'unread_count' => 0, 'notifications' => []]);
    exit();
}

try {
    // 1. AUTO-CLEANUP: Purge pending orders older than 3 days
    try {
        $pdo->exec("DELETE FROM orders WHERE status = 'Pending' AND created_at < NOW() - INTERVAL 3 DAY");
    } catch (PDOException $e) {}

    // 2. FETCH PENDING ORDERS
    $pending_orders = [];
    $pending_count = 0;
    try {
        $orderStmt = $pdo->query("
            SELECT id, 'order' AS notif_type, 
                   COALESCE(client_name, reference_code, CONCAT('Order #', id)) AS title, 
                   CONCAT('New order #', reference_code, ' (Ksh ', FORMAT(total_amount, 0), ')') AS description, 
                   NULL AS admin_avatar,
                   created_at, 'index.php#orders-section' AS link
            FROM orders 
            WHERE status = 'Pending' AND (is_read = 0 OR is_read IS NULL)
            ORDER BY created_at DESC LIMIT 5
        ");
        $pending_orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);
        $pending_count = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending' AND (is_read = 0 OR is_read IS NULL)")->fetchColumn();
    } catch (PDOException $e) {}

    // 3. FETCH STAFF ACTIONS / ACTIVITY LOGS
    $staff_actions = [];
    $staff_action_count = 0;
    try {
        if ($pdo->query("SHOW TABLES LIKE 'activity_logs'")->rowCount() > 0) {
            $actStmt = $pdo->query("
                SELECT id, 'staff_action' AS notif_type, 
                       admin_name AS title, 
                       description, 
                       admin_avatar,
                       created_at, 'activity_logs.php' AS link
                FROM activity_logs 
                WHERE (is_read = 0 OR is_read IS NULL)
                ORDER BY created_at DESC LIMIT 5
            ");
            $staff_actions = $actStmt->fetchAll(PDO::FETCH_ASSOC);
            $staff_action_count = (int)$pdo->query("SELECT COUNT(*) FROM activity_logs WHERE (is_read = 0 OR is_read IS NULL)")->fetchColumn();
        }
    } catch (PDOException $e) {}

    // 4. FETCH CONTACT INQUIRIES
    $inquiries = [];
    $inquiry_count = 0;
    try {
        if ($pdo->query("SHOW TABLES LIKE 'contact_inquiries'")->rowCount() > 0) {
            $inqStmt = $pdo->query("
                SELECT id, 'inquiry' AS notif_type, name AS title, 
                       CONCAT('Inquiry: ', service_type) AS description, 
                       NULL AS admin_avatar,
                       created_at, 'index.php#inquiries-section' AS link
                FROM contact_inquiries WHERE (is_read = 0 OR is_read IS NULL)
                ORDER BY created_at DESC LIMIT 4
            ");
            $inquiries = $inqStmt->fetchAll(PDO::FETCH_ASSOC);
            $inquiry_count = (int)$pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE (is_read = 0 OR is_read IS NULL)")->fetchColumn();
        }
    } catch (PDOException $e) {}

    // 5. FETCH LOW STOCK ALERTS
    $low_stock = [];
    $stock_count = 0;
    try {
        if (empty($_SESSION['dismiss_all_stock'])) {
            $dismissed = $_SESSION['dismissed_stock_ids'] ?? [];
            $stock_query = "SELECT id, 'stock' AS notif_type, name AS title, CONCAT('Low Stock Alert: Only ', stock, ' left!') AS description, NULL AS admin_avatar, IFNULL(updated_at, NOW()) AS created_at, 'manage_products.php' AS link FROM products WHERE stock <= 5";
            if (!empty($dismissed)) {
                $in_clause = implode(',', array_map('intval', $dismissed));
                $stock_query .= " AND id NOT IN ($in_clause)";
            }
            $stock_query .= " ORDER BY stock ASC LIMIT 4";
            $stockStmt = $pdo->query($stock_query);
            $low_stock = $stockStmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($dismissed)) {
                $in_clause = implode(',', array_map('intval', $dismissed));
                $stock_count = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE stock <= 5 AND id NOT IN ($in_clause)")->fetchColumn();
            } else {
                $stock_count = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE stock <= 5")->fetchColumn();
            }
        }
    } catch (PDOException $e) {}

    // Combine and sort by date
    $recent_notifications = array_merge($pending_orders, $staff_actions, $inquiries, $low_stock);
    usort($recent_notifications, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    $recent_notifications = array_slice($recent_notifications, 0, 8);

    $total_unread = $pending_count + $staff_action_count + $inquiry_count + $stock_count;

    echo json_encode([
        'success' => true, 
        'unread_count' => $total_unread,
        'notifications' => $recent_notifications
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}