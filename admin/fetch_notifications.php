<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

header('Content-Type: application/json');

if (empty($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'unread_count' => 0]);
    exit;
}

$recent_notifications = [];

try {
    // 1. Pending Orders
    $orderStmt = $pdo->query("
        SELECT id, 'order' AS notif_type, full_name AS title, 
               CONCAT('New order #', id, ' ($', FORMAT(total_amount, 2), ')') AS description, 
               created_at, 'index.php#orders-section' AS link
        FROM orders WHERE status = 'Pending' AND is_read = 0 
        ORDER BY created_at DESC LIMIT 4
    ");
    $pending_orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Service & Contact Inquiries
    $inquiries = [];
    if ($pdo->query("SHOW TABLES LIKE 'contact_inquiries'")->rowCount() > 0) {
        $inqStmt = $pdo->query("
            SELECT id, 'inquiry' AS notif_type, name AS title, 
                   CONCAT('Inquiry: ', service_type) AS description, 
                   created_at, 'index.php#inquiries-section' AS link
            FROM contact_inquiries WHERE is_read = 0 
            ORDER BY created_at DESC LIMIT 4
        ");
        $inquiries = $inqStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Low Stock Alerts
    $low_stock = [];
    if (empty($_SESSION['dismiss_all_stock'])) {
        $dismissed = $_SESSION['dismissed_stock_ids'] ?? [];
        $stock_query = "SELECT id, 'stock' AS notif_type, name AS title, CONCAT('Low Stock Alert: Only ', stock, ' left!') AS description, IFNULL(updated_at, NOW()) AS created_at, 'manage_products.php' AS link FROM products WHERE stock <= 5";
        if (!empty($dismissed)) {
            $in_clause = implode(',', array_map('intval', $dismissed));
            $stock_query .= " AND id NOT IN ($in_clause)";
        }
        $stock_query .= " ORDER BY stock ASC LIMIT 4";
        $stockStmt = $pdo->query($stock_query);
        $low_stock = $stockStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $recent_notifications = array_merge($pending_orders, $inquiries, $low_stock);
    usort($recent_notifications, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    $recent_notifications = array_slice($recent_notifications, 0, 6);

    $pending_count = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending' AND is_read = 0")->fetchColumn();
    $inquiry_count = 0;
    if ($pdo->query("SHOW TABLES LIKE 'contact_inquiries'")->rowCount() > 0) {
        $inquiry_count = (int)$pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE is_read = 0")->fetchColumn();
    }
    
    $stock_count = 0;
    if (empty($_SESSION['dismiss_all_stock'])) {
        $dismissed = $_SESSION['dismissed_stock_ids'] ?? [];
        if (!empty($dismissed)) {
            $in_clause = implode(',', array_map('intval', $dismissed));
            $stock_count = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE stock <= 5 AND id NOT IN ($in_clause)")->fetchColumn();
        } else {
            $stock_count = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE stock <= 5")->fetchColumn();
        }
    }

    $total_unread = $pending_count + $inquiry_count + $stock_count;

    echo json_encode([
        'success' => true, 
        'unread_count' => $total_unread,
        'notifications' => $recent_notifications
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}