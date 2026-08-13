<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';

$message = '';
$message_type = '';

// Handle Status Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $request_id = intval($_POST['request_id'] ?? 0);
    $record_source = $_POST['record_source'] ?? ''; // 'service' or 'order'
    $new_status = $_POST['new_status'] ?? '';

    $allowed_statuses = ['PENDING', 'APPROVED', 'COMPLETED', 'DECLINED', 'CANCELLED'];

    if ($request_id > 0 && in_array($new_status, $allowed_statuses, true)) {
        try {
            if ($record_source === 'order') {
                $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
            } else {
                $stmt = $pdo->prepare("UPDATE service_requests SET status = :status WHERE id = :id");
            }
            $stmt->execute(['status' => $new_status, 'id' => $request_id]);
            $message = "Record status successfully updated to {$new_status}.";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Database update error: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

$all_records = [];

// 1. Safely Fetch Service & Subscription Requests
try {
    $stmt_services = $pdo->query("SELECT * FROM service_requests ORDER BY id DESC");
    $services = $stmt_services->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($services as $s) {
        $all_records[] = [
            'id'             => $s['id'],
            'reference_code' => $s['reference_code'] ?? 'N/A',
            'record_source'  => 'service',
            'item_type'      => $s['item_type'] ?? 'booking',
            'client_name'    => $s['client_name'] ?? ($s['customer_name'] ?? 'Client'),
            'client_phone'   => $s['client_phone'] ?? ($s['customer_phone'] ?? ($s['phone'] ?? '')),
            'client_email'   => $s['client_email'] ?? ($s['customer_email'] ?? ($s['email'] ?? '')),
            'item_summary'   => $s['item_title'] ?? ($s['title'] ?? 'Service Booking'),
            'notes'          => $s['notes'] ?? '',
            'status'         => $s['status'] ?? 'PENDING',
            'created_at'     => $s['created_at'] ?? date('Y-m-d H:i:s')
        ];
    }
} catch (PDOException $e) {
    // Silently handle if service_requests table isn't present
}

// 2. Safely Fetch Hardware Orders
try {
    $stmt_orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
    $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

    foreach ($orders as $o) {
        // Fetch ordered items description
        $item_descriptions = [];
        try {
            $stmt_items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
            $stmt_items->execute(['order_id' => $o['id']]);
            $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

            foreach ($items as $it) {
                $title = $it['product_title'] ?? ($it['title'] ?? 'Product');
                $qty = $it['quantity'] ?? 1;
                $item_descriptions[] = "{$title} ({$qty}x)";
            }
        } catch (PDOException $e) {
            // order_items optional
        }

        $summary = !empty($item_descriptions) ? implode(', ', $item_descriptions) : 'Hardware Purchase';
        $amount_note = isset($o['total_amount']) ? 'Total: Ksh ' . number_format($o['total_amount'], 0) : '';

        $all_records[] = [
            'id'             => $o['id'],
            'reference_code' => $o['reference_code'] ?? 'N/A',
            'record_source'  => 'order',
            'item_type'      => 'hardware',
            'client_name'    => $o['client_name'] ?? ($o['customer_name'] ?? ($o['name'] ?? 'Hardware Customer')),
            'client_phone'   => $o['client_phone'] ?? ($o['customer_phone'] ?? ($o['phone'] ?? '')),
            'client_email'   => $o['client_email'] ?? ($o['customer_email'] ?? ($o['email'] ?? '')),
            'item_summary'   => $summary,
            'notes'          => $amount_note,
            'status'         => $o['status'] ?? 'PENDING',
            'created_at'     => $o['created_at'] ?? date('Y-m-d H:i:s')
        ];
    }
} catch (PDOException $e) {
    // Silently handle if orders table isn't present
}

// Sort combined records by date (Newest First)
usort($all_records, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

$page_title = "Manage All Orders & Subscriptions | Admin";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f1f5f9; margin: 0; padding: 20px; color: #1e293b; }
        .container { max-width: 1200px; margin: 0 auto; }
        
        .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; background: #002d62; color: white; padding: 20px 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .header-bar h1 { margin: 0; font-size: 1.6rem; }

        .alert { padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; font-weight: 600; }
        .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

        .table-card { background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; min-width: 900px; }
        th { background: #f8fafc; color: #475569; padding: 14px 18px; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
        td { padding: 16px 18px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; vertical-align: middle; }
        tr:hover { background-color: #f8fafc; }

        /* Badges */
        .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .badge-sub { background: #e0f2fe; color: #0369a1; }
        .badge-bok { background: #f3e8ff; color: #6b21a8; }
        .badge-ord { background: #ffedd5; color: #c2410c; }
        
        .status-PENDING { background: #fef3c7; color: #b45309; }
        .status-APPROVED, .status-COMPLETED { background: #dcfce7; color: #15803d; }
        .status-DECLINED, .status-CANCELLED { background: #fee2e2; color: #b91c1c; }

        .wa-btn { display: inline-flex; align-items: center; gap: 6px; background: #25D366; color: white; text-decoration: none; padding: 4px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
        .ticket-btn { display: inline-block; color: #002d62; text-decoration: underline; font-size: 0.85rem; font-weight: bold; margin-top: 4px; }

        .status-select { padding: 6px 10px; border-radius: 4px; border: 1px solid #cbd5e1; font-size: 0.85rem; background: white; cursor: pointer; }
        .action-btn { background: #002d62; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">

    <div class="header-bar">
        <h1>Orders, Subscriptions & Service Bookings</h1>
        <a href="index.php" style="color: #cbd5e1; text-decoration: none; font-size: 0.95rem;">← Back to Main Dashboard</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Ref Code</th>
                    <th>Type</th>
                    <th>Client Name</th>
                    <th>Contact Info</th>
                    <th>Item / Summary</th>
                    <th>Notes / Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($all_records)): ?>
                    <?php foreach ($all_records as $req): ?>
                        <?php 
                            $phone_clean = preg_replace('/[^0-9]/', '', $req['client_phone']);
                            if (str_starts_with($phone_clean, '0')) {
                                $phone_clean = '254' . substr($phone_clean, 1);
                            }
                            $wa_link = "https://wa.me/" . $phone_clean . "?text=" . urlencode("Hello {$req['client_name']}, regarding reference {$req['reference_code']}...");
                            $ticket_url = "../ticket.php?ref=" . urlencode($req['reference_code']);
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($req['reference_code']); ?></strong>
                                <div><a href="<?php echo $ticket_url; ?>" target="_blank" class="ticket-btn">📄 View Ticket</a></div>
                            </td>
                            
                            <td>
                                <?php if ($req['item_type'] === 'subscription'): ?>
                                    <span class="badge badge-sub">Subscription</span>
                                <?php elseif ($req['item_type'] === 'hardware'): ?>
                                    <span class="badge badge-ord">Hardware</span>
                                <?php else: ?>
                                    <span class="badge badge-bok">Booking</span>
                                <?php endif; ?>
                            </td>

                            <td><strong><?php echo htmlspecialchars($req['client_name']); ?></strong></td>

                            <td>
                                <div><?php echo htmlspecialchars($req['client_phone'] ?: 'N/A'); ?></div>
                                <?php if (!empty($req['client_email'])): ?>
                                    <small style="color: #64748b; display: block;"><?php echo htmlspecialchars($req['client_email']); ?></small>
                                <?php endif; ?>
                                <?php if (!empty($phone_clean)): ?>
                                    <div style="margin-top: 4px;">
                                        <a href="<?php echo $wa_link; ?>" target="_blank" class="wa-btn">💬 Chat</a>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td><?php echo htmlspecialchars($req['item_summary']); ?></td>
                            
                            <td style="max-width: 200px; color: #475569; font-size: 0.85rem;">
                                <?php echo htmlspecialchars($req['notes'] ?: '—'); ?>
                            </td>

                            <td>
                                <span class="badge status-<?php echo $req['status']; ?>">
                                    <?php echo $req['status']; ?>
                                </span>
                            </td>

                            <td style="font-size: 0.85rem; color: #64748b;">
                                <?php echo date('M d, Y H:i', strtotime($req['created_at'])); ?>
                            </td>

                            <td>
                                <form method="POST" style="display: flex; gap: 6px; align-items: center;">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                    <input type="hidden" name="record_source" value="<?php echo $req['record_source']; ?>">
                                    
                                    <select name="new_status" class="status-select">
                                        <option value="PENDING" <?php echo $req['status'] === 'PENDING' ? 'selected' : ''; ?>>PENDING</option>
                                        <option value="APPROVED" <?php echo $req['status'] === 'APPROVED' ? 'selected' : ''; ?>>APPROVED</option>
                                        <option value="COMPLETED" <?php echo $req['status'] === 'COMPLETED' ? 'selected' : ''; ?>>COMPLETED</option>
                                        <option value="DECLINED" <?php echo $req['status'] === 'DECLINED' ? 'selected' : ''; ?>>DECLINED</option>
                                    </select>
                                    
                                    <button type="submit" class="action-btn">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: #64748b;">
                            No orders, bookings, or subscription requests found yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>