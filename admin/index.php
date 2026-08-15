<?php
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/my_custom_errors.log');

// 1. Session check to protect the page
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If not logged in, send them to the main public homepage
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../index.php");
    exit();
}
    
require_once '../includes/db_connect.php';

// --- INITIALIZE REQUIRED TABLES ---
// Moved this out of the order update logic so the table generates automatically on page load
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS activity_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT DEFAULT NULL,
            admin_name VARCHAR(100) NOT NULL,
            admin_avatar VARCHAR(255) DEFAULT NULL,
            admin_role VARCHAR(50) DEFAULT 'STAFF_ADMIN',
            action_type VARCHAR(50) NOT NULL,
            description TEXT NOT NULL,
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
} catch (PDOException $e) {
    error_log("Failed to initialize activity_logs table: " . $e->getMessage());
}

$message = '';
$error = '';

// 2. Handle Status Updates (PENDING, COMPLETED, CANCELLED) + Automated Inventory Adjustment + Activity Logging
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id   = intval($_POST['order_id']);
    $new_status = trim($_POST['status']);
    
    // Fetch previous order status and reference code
    $stmtPrev = $pdo->prepare("SELECT status, reference_code FROM orders WHERE id = :id");
    $stmtPrev->execute(['id' => $order_id]);
    $orderRow = $stmtPrev->fetch(PDO::FETCH_ASSOC);
    $old_status = $orderRow['status'] ?? false;
    $order_ref  = $orderRow['reference_code'] ?? $order_id;

    if ($old_status !== false && $old_status !== $new_status) {
        $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
        if ($stmt->execute(['status' => $new_status, 'id' => $order_id])) {
            
            // Fetch order items to handle stock adjustments
            $itemsStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
            $itemsStmt->execute(['order_id' => $order_id]);
            $orderItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            // DEDUCT STOCK: When order is marked COMPLETED
            if ($new_status === 'COMPLETED' && $old_status !== 'COMPLETED') {
                foreach ($orderItems as $item) {
                    $p_id = $item['product_id'] ?? $item['item_id'] ?? null;
                    $qty  = intval($item['quantity'] ?? $item['qty'] ?? 1);

                    if ($p_id) {
                        $pCheck = $pdo->prepare("SELECT item_type FROM products WHERE id = :id");
                        $pCheck->execute(['id' => $p_id]);
                        $prod = $pCheck->fetch(PDO::FETCH_ASSOC);
                        $type = $prod['item_type'] ?? 'product';

                        // Deduct stock for physical products only
                        if ($type === 'product') {
                            $deductStmt = $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - :qty) WHERE id = :id");
                            $deductStmt->execute(['qty' => $qty, 'id' => $p_id]);
                        }
                    }
                }
                $message = "Order marked as COMPLETED and product stock deducted from inventory!";
            } 
            // RESTORE STOCK: If status was reverted from COMPLETED back to PENDING/CANCELLED
            elseif ($old_status === 'COMPLETED' && $new_status !== 'COMPLETED') {
                foreach ($orderItems as $item) {
                    $p_id = $item['product_id'] ?? $item['item_id'] ?? null;
                    $qty  = intval($item['quantity'] ?? $item['qty'] ?? 1);

                    if ($p_id) {
                        $pCheck = $pdo->prepare("SELECT item_type FROM products WHERE id = :id");
                        $pCheck->execute(['id' => $p_id]);
                        $prod = $pCheck->fetch(PDO::FETCH_ASSOC);
                        $type = $prod['item_type'] ?? 'product';

                        if ($type === 'product') {
                            $restoreStmt = $pdo->prepare("UPDATE products SET stock = stock + :qty WHERE id = :id");
                            $restoreStmt->execute(['qty' => $qty, 'id' => $p_id]);
                        }
                    }
                }
                $message = "Order status updated to {$new_status} and product stock restored!";
            } else {
                $message = "Order status updated to {$new_status} successfully!";
            }

            // --- LOG STAFF ACTION INTO ACTIVITY LOGS ---
            try {
                $staff_id     = $_SESSION['admin_id'] ?? null;
                $staff_name   = $_SESSION['admin_fullname'] ?? $_SESSION['admin_username'] ?? 'Staff Member';
                $staff_avatar = $_SESSION['admin_pfp'] ?? $_SESSION['admin_avatar'] ?? null;
                $staff_role   = $_SESSION['admin_role'] ?? 'STAFF_ADMIN';

                $logStmt = $pdo->prepare("
                    INSERT INTO activity_logs (admin_id, admin_name, admin_avatar, admin_role, action_type, description, is_read)
                    VALUES (:id, :name, :avatar, :role, 'order_action', :desc, 0)
                ");
                $logStmt->execute([
                    ':id'     => $staff_id,
                    ':name'   => $staff_name,
                    ':avatar' => $staff_avatar,
                    ':role'   => $staff_role,
                    ':desc'   => "Updated Order #{$order_ref} status to {$new_status}"
                ]);
            } catch (PDOException $e) {
                // Log PDO exception to server logs for debugging without blocking UX
                error_log("Activity Log Error in admin/index.php: " . $e->getMessage());
            }

        } else {
            $error = "Failed to update order status.";
        }
    }
}

// 3. Quick metrics overview
$product_count = 0;
try {
    $product_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
} catch (PDOException $e) {}

$article_count = 0;
try {
    if ($pdo->query("SHOW TABLES LIKE 'articles'")->rowCount() > 0) {
        $article_count = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    }
} catch (PDOException $e) {}

try {
    $pending_orders_count = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'PENDING'")->fetchColumn();
} catch (PDOException $e) {
    $pending_orders_count = 0;
}

// 4. Fetch all orders with line item counts
$sql = "
    SELECT o.*, COUNT(i.id) AS total_items 
    FROM orders o 
    LEFT JOIN order_items i ON o.id = i.order_id 
    GROUP BY o.id 
    ORDER BY o.created_at DESC
";
$orders = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$base_url = '../';
$page_title = "Admin Control Dashboard | Blue Edge Solutions";
include_once '../includes/header.php';
?>

<main style="background-color: #f8fafc; min-height: 80vh; padding: 40px 20px; font-family: 'Segoe UI', Tahoma, Geneva, sans-serif;">
    <div style="max-width: 1100px; margin: 0 auto;">
        
        <!-- Welcome Hero Box -->
        <div style="background: #002d62; color: white; padding: 35px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <h1 style="margin: 0 0 8px 0; font-size: 2rem;">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_fullname'] ?? $_SESSION['admin_username'] ?? 'Admin'); ?>!</h1>
            <p style="margin: 0; color: #cbd5e1; font-size: 1rem;">Manage client tickets, hardware shop inventory, or blog content below.</p>
        </div>

        <!-- Alert Notifications -->
        <?php if ($message): ?>
            <div style="background: #dcfce7; color: #15803d; padding: 12px 20px; border-radius: 6px; margin-bottom: 25px; font-weight: bold;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 12px 20px; border-radius: 6px; margin-bottom: 25px; font-weight: bold;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Dashboard Selection Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 40px;">

            <!-- ORDERS & TICKETS CARD -->
            <div style="background: white; border-radius: 10px; border: 1px solid #e2e8f0; padding: 22px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                        <div style="background: #e0f2fe; color: #0284c7; width: 45px; height: 45px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                            📋
                        </div>
                        <?php if ($pending_orders_count > 0): ?>
                            <span style="background: #ef4444; color: white; padding: 3px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: bold;">
                                <?php echo $pending_orders_count; ?> Pending
                            </span>
                        <?php endif; ?>
                    </div>
                    <h3 style="color: #002d62; margin: 0 0 8px 0; font-size: 1.15rem;">Orders & Tickets</h3>
                    <p style="color: #64748b; font-size: 0.88rem; line-height: 1.4; margin-bottom: 15px;">
                        View and manage client orders, services, and hardware purchases.
                    </p>
                </div>
                <a href="#orders-section" style="background: #002d62; color: white; text-align: center; text-decoration: none; padding: 10px; border-radius: 6px; font-weight: bold; font-size: 0.9rem;">
                    View Orders ↓
                </a>
            </div>

            <!-- SHOP INVENTORY CARD -->
            <div style="background: white; border-radius: 10px; border: 1px solid #e2e8f0; padding: 22px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="background: #eff6ff; color: #2563eb; width: 45px; height: 45px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 15px;">
                        🛒
                    </div>
                    <h3 style="color: #002d62; margin: 0 0 8px 0; font-size: 1.15rem;">Shop Inventory</h3>
                    <p style="color: #64748b; font-size: 0.88rem; line-height: 1.4; margin-bottom: 15px;">
                        Add or remove shop hardware and consumables (<?php echo $product_count; ?> active items).
                    </p>
                </div>
                <a href="manage_products.php" style="background: #002d62; color: white; text-align: center; text-decoration: none; padding: 10px; border-radius: 6px; font-weight: bold; font-size: 0.9rem;">
                    Manage Products →
                </a>
            </div>

            <!-- BLOG CONTENT CARD -->
            <div style="background: white; border-radius: 10px; border: 1px solid #e2e8f0; padding: 22px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="background: #f0fdf4; color: #16a34a; width: 45px; height: 45px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 15px;">
                        📰
                    </div>
                    <h3 style="color: #002d62; margin: 0 0 8px 0; font-size: 1.15rem;">Blog Articles</h3>
                    <p style="color: #64748b; font-size: 0.88rem; line-height: 1.4; margin-bottom: 15px;">
                        Publish tech insights, news, and IT infrastructure guides (<?php echo $article_count; ?> posts).
                    </p>
                </div>
                <a href="manage_articles.php" style="background: #002d62; color: white; text-align: center; text-decoration: none; padding: 10px; border-radius: 6px; font-weight: bold; font-size: 0.9rem;">
                    Manage Articles →
                </a>
            </div>

        </div>

        <!-- ORDERS MANAGEMENT SECTION -->
        <div id="orders-section" style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h2 style="color: #002d62; margin: 0; font-size: 1.4rem;">Recent Orders & Inquiries</h2>
                    <p style="color: #64748b; margin: 4px 0 0 0; font-size: 0.88rem;">Track client requests and adjust order processing statuses</p>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569;">
                            <th style="padding: 12px 15px;">Reference / ID</th>
                            <th style="padding: 12px 15px;">Client Name</th>
                            <th style="padding: 12px 15px;">Contact Info</th>
                            <th style="padding: 12px 15px;">Amount</th>
                            <th style="padding: 12px 15px;">Date</th>
                            <th style="padding: 12px 15px;">Status</th>
                            <th style="padding: 12px 15px; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $o): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                                    <td style="padding: 14px 15px; font-weight: bold; color: #002d62;">
                                        #<?php echo htmlspecialchars($o['reference_code'] ?? $o['id']); ?>
                                    </td>
                                    <td style="padding: 14px 15px; font-weight: 600; color: #334155;">
                                        <?php echo htmlspecialchars($o['client_name'] ?? 'N/A'); ?>
                                    </td>
                                    <td style="padding: 14px 15px; color: #64748b;">
                                        <div><?php echo htmlspecialchars($o['email'] ?? ''); ?></div>
                                        <small style="color: #94a3b8;"><?php echo htmlspecialchars($o['phone'] ?? ''); ?></small>
                                    </td>
                                    <td style="padding: 14px 15px; font-weight: bold; color: #0f172a;">
                                        Ksh <?php echo number_format($o['total_amount'] ?? 0, 2); ?>
                                    </td>
                                    <td style="padding: 14px 15px; color: #64748b; font-size: 0.82rem;">
                                        <?php echo date('M d, Y H:i', strtotime($o['created_at'])); ?>
                                    </td>
                                    <td style="padding: 14px 15px;">
                                        <?php 
                                            $st = strtoupper($o['status'] ?? 'PENDING');
                                            $bg = ($st === 'COMPLETED') ? '#dcfce7' : (($st === 'CANCELLED') ? '#fee2e2' : '#fef3c7');
                                            $fg = ($st === 'COMPLETED') ? '#15803d' : (($st === 'CANCELLED') ? '#b91c1c' : '#d97706');
                                        ?>
                                        <span style="background: <?php echo $bg; ?>; color: <?php echo $fg; ?>; font-size: 0.72rem; font-weight: 800; padding: 4px 10px; border-radius: 12px; display: inline-block;">
                                            <?php echo $st; ?>
                                        </span>
                                    </td>
                                    <td style="padding: 14px 15px; text-align: center;">
                                        <form method="POST" action="index.php#orders-section" style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                            <select name="status" style="padding: 5px 8px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.8rem; background: white; color: #334155;">
                                                <option value="PENDING" <?php echo ($st === 'PENDING') ? 'selected' : ''; ?>>Pending</option>
                                                <option value="COMPLETED" <?php echo ($st === 'COMPLETED') ? 'selected' : ''; ?>>Completed</option>
                                                <option value="CANCELLED" <?php echo ($st === 'CANCELLED') ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" name="update_status" style="background: #002d62; color: white; border: none; padding: 5px 10px; border-radius: 6px; font-weight: bold; font-size: 0.78rem; cursor: pointer;">
                                                Save
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="padding: 30px; text-align: center; color: #94a3b8;">
                                    No orders found in the database.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

</body>
</html>