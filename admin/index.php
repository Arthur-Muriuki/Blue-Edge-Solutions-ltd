<?php
// 1. Session check to protect the page
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';

$message = '';
$error = '';

// 2. Handle Status Updates (PENDING, COMPLETED, CANCELLED) + Automated Inventory Adjustment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id   = intval($_POST['order_id']);
    $new_status = trim($_POST['status']);
    
    // Fetch previous order status
    $stmtPrev = $pdo->prepare("SELECT status FROM orders WHERE id = :id");
    $stmtPrev->execute(['id' => $order_id]);
    $old_status = $stmtPrev->fetchColumn();

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
        } else {
            $error = "Failed to update order status.";
        }
    }
}

// 3. Quick metrics overview
$product_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

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
        <div style="background: linear-gradient(135deg, #002d62 0%, #001f42 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <h1 style="margin: 0 0 8px 0; color: #cbd5e1; font-size: 1.8rem;">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>!</h1>
                <p style="margin: 0; color: #cbd5e1; font-size: 1rem;">Manage client tickets, hardware shop inventory, or blog content below.</p>
            </div>
            <a href="logout.php" style="background: rgba(255,255,255,0.15); color: white; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-weight: bold; border: 1px solid rgba(255,255,255,0.3);">
                Log Out
            </a>
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
                <a href="manage_products.php" style="background: #ff7300; color: white; text-align: center; text-decoration: none; padding: 10px; border-radius: 6px; font-weight: bold; font-size: 0.9rem;">
                    Manage Shop &rarr;
                </a>
            </div>

            <!-- BLOG & ARTICLES CARD -->
            <div style="background: white; border-radius: 10px; border: 1px solid #e2e8f0; padding: 22px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="background: #f0fdf4; color: #16a34a; width: 45px; height: 45px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 15px;">
                        📝
                    </div>
                    <h3 style="color: #002d62; margin: 0 0 8px 0; font-size: 1.15rem;">Blog Articles</h3>
                    <p style="color: #64748b; font-size: 0.88rem; line-height: 1.4; margin-bottom: 15px;">
                        Publish and edit technical blog posts and updates for clients.
                    </p>
                </div>
                <a href="manage_articles.php" style="background: #002d62; color: white; text-align: center; text-decoration: none; padding: 10px; border-radius: 6px; font-weight: bold; font-size: 0.9rem;">
                    Manage Articles &rarr;
                </a>
            </div>

            <!-- LIVE SITE PREVIEW CARD -->
            <div style="background: white; border-radius: 10px; border: 1px solid #e2e8f0; padding: 22px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="background: #fefce8; color: #ca8a04; width: 45px; height: 45px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 15px;">
                        🌐
                    </div>
                    <h3 style="color: #002d62; margin: 0 0 8px 0; font-size: 1.15rem;">Public Website</h3>
                    <p style="color: #64748b; font-size: 0.88rem; line-height: 1.4; margin-bottom: 15px;">
                        Open the public homepage to view the website as a client.
                    </p>
                </div>
                <a href="../index.php" target="_blank" style="background: #e2e8f0; color: #002d62; text-align: center; text-decoration: none; padding: 10px; border-radius: 6px; font-weight: bold; font-size: 0.9rem;">
                    Open Website &rarr;
                </a>
            </div>

        </div>

        <!-- RECENT ORDERS TABLE -->
        <div id="orders-section" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; overflow-x: auto;">
            <h3 style="color: #002d62; margin-top: 0; margin-bottom: 20px; font-size: 1.25rem;">Client Orders & Service Tickets (<?php echo count($orders); ?>)</h3>

            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0; color: #64748b;">
                        <th style="padding: 10px;">Reference</th>
                        <th style="padding: 10px;">Client</th>
                        <th style="padding: 10px;">Phone</th>
                        <th style="padding: 10px;">Items</th>
                        <th style="padding: 10px;">Total</th>
                        <th style="padding: 10px;">Status</th>
                        <th style="padding: 10px; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $o): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 10px;">
                                    <a href="../ticket.php?ref=<?php echo htmlspecialchars($o['reference_code']); ?>" target="_blank" style="font-weight: bold; color: #0284c7; text-decoration: none;">
                                        <?php echo htmlspecialchars($o['reference_code']); ?> ↗
                                    </a>
                                </td>
                                <td style="padding: 10px; font-weight: bold; color: #1e293b;">
                                    <?php echo htmlspecialchars($o['client_name']); ?>
                                </td>
                                <td style="padding: 10px; color: #475569;">
                                    <?php echo htmlspecialchars($o['client_phone']); ?>
                                </td>
                                <td style="padding: 10px; color: #475569;">
                                    <?php echo intval($o['total_items']); ?> item(s)
                                </td>
                                <td style="padding: 10px; font-weight: bold; color: #166534;">
                                    Ksh <?php echo number_format($o['total_amount'], 2); ?>
                                </td>
                                <td style="padding: 10px;">
                                    <form method="POST" action="index.php" style="margin: 0;">
                                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                        <select name="status" onchange="this.form.submit()" style="padding: 4px 8px; border-radius: 4px; border: 1px solid #cbd5e1; font-weight: bold; font-size: 0.85rem; color: <?php echo $o['status'] === 'COMPLETED' ? '#166534' : ($o['status'] === 'CANCELLED' ? '#dc2626' : '#d97706'); ?>;">
                                            <option value="PENDING" <?php echo $o['status'] === 'PENDING' ? 'selected' : ''; ?>>PENDING</option>
                                            <option value="COMPLETED" <?php echo $o['status'] === 'COMPLETED' ? 'selected' : ''; ?>>COMPLETED</option>
                                            <option value="CANCELLED" <?php echo $o['status'] === 'CANCELLED' ? 'selected' : ''; ?>>CANCELLED</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td style="padding: 10px; text-align: right;">
                                    <a href="../ticket.php?ref=<?php echo htmlspecialchars($o['reference_code']); ?>" target="_blank" style="background: #f1f5f9; color: #002d62; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; font-weight: bold;">
                                        View Ticket
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="padding: 20px; text-align: center; color: #64748b;">No orders or tickets found yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

<?php include_once '../includes/footer.php'; ?>