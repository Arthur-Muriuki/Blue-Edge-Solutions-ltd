<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

header('Content-Type: application/json');

if (empty($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$type   = $input['type'] ?? '';
$id     = isset($input['id']) ? (int)$input['id'] : 0;
$action = $input['action'] ?? '';

try {
    if ($action === 'mark_all') {
        // Mark all orders as read
        $pdo->exec("UPDATE orders SET is_read = 1 WHERE status = 'Pending'");
        
        // Mark all inquiries as read if table exists
        if ($pdo->query("SHOW TABLES LIKE 'contact_inquiries'")->rowCount() > 0) {
            $pdo->exec("UPDATE contact_inquiries SET is_read = 1");
        }
        
        // Store stock dismissal timestamp in session
        $_SESSION['dismiss_all_stock'] = true;

    } else if ($id > 0) {
        if ($type === 'order') {
            $stmt = $pdo->prepare("UPDATE orders SET is_read = 1 WHERE id = ?");
            $stmt->execute([$id]);
        } else if ($type === 'inquiry') {
            $stmt = $pdo->prepare("UPDATE contact_inquiries SET is_read = 1 WHERE id = ?");
            $stmt->execute([$id]);
        } else if ($type === 'stock') {
            // Track dismissed stock product IDs in session
            if (!isset($_SESSION['dismissed_stock_ids'])) {
                $_SESSION['dismissed_stock_ids'] = [];
            }
            if (!in_array($id, $_SESSION['dismissed_stock_ids'])) {
                $_SESSION['dismissed_stock_ids'][] = $id;
            }
        }
    }

    // Recalculate remaining unread count
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

    echo json_encode(['success' => true, 'unread_count' => $total_unread]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}