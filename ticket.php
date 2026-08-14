<?php
require_once 'includes/db_connect.php';

// 1. Check GET parameter first; fallback to remembered cookie reference
$ref = isset($_GET['ref']) ? trim($_GET['ref']) : ($_COOKIE['last_ticket_ref'] ?? '');

$order = null;
$items = [];
$error_message = '';

if (!empty($ref)) {
    // Fetch order details from database
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE reference_code = :ref");
    $stmt->execute([':ref' => $ref]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        // Fetch order items
        $item_stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
        $item_stmt->execute([':order_id' => $order['id']]);
        $items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Refresh/Set the guest cookie for 90 days
        setcookie('last_ticket_ref', $ref, [
            'expires'  => time() + (86400 * 90),
            'path'     => '/',
            'samesite' => 'Lax'
        ]);
    } else {
        $error_message = "No order or ticket found with reference code: <strong>" . htmlspecialchars($ref) . "</strong>";
    }
}

$page_title = $order 
    ? "Ticket " . htmlspecialchars($order['reference_code']) . " | Blue Edge Solutions" 
    : "Track Order / Service Ticket | Blue Edge Solutions";

include 'includes/header.php';
?>

<div class="container" style="max-width: 800px; margin: 40px auto; padding: 20px;">

    <?php if ($order): ?>
        <!-- ========================================== -->
        <!-- TICKET / ORDER DETAILS VIEW                -->
        <!-- ========================================== -->
        <div style="background: white; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 30px; border-top: 5px solid #002d62;">
            
            <!-- HEADER -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 25px;">
                <div>
                    <h1 style="margin: 0; color: #002d62; font-size: 1.8rem;">BLUE EDGE SOLUTIONS</h1>
                    <p style="margin: 5px 0 0 0; color: #64748b; font-size: 0.9rem;">Official Order & Service Ticket</p>
                </div>
                <div style="text-align: right;">
                    <span style="background: #e0f2fe; color: #0369a1; padding: 6px 12px; border-radius: 6px; font-weight: bold; font-size: 1rem;">
                        <?php echo htmlspecialchars($order['reference_code']); ?>
                    </span>
                </div>
            </div>

            <!-- CLIENT & ORDER INFO -->
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
                <div>
                    <small style="color: #64748b; text-transform: uppercase; font-weight: bold; font-size: 0.75rem;">Client Name</small>
                    <div style="font-weight: bold; color: #0f172a;"><?php echo htmlspecialchars($order['client_name'] ?? 'WhatsApp Customer'); ?></div>
                </div>

                <div>
                    <small style="color: #64748b; text-transform: uppercase; font-weight: bold; font-size: 0.75rem;">Phone Number</small>
                    <div style="font-weight: bold; color: #0f172a;"><?php echo htmlspecialchars($order['client_phone'] ?? 'WhatsApp Lead'); ?></div>
                </div>

                <div>
                    <small style="color: #64748b; text-transform: uppercase; font-weight: bold; font-size: 0.75rem;">Payment Method</small>
                    <div style="font-weight: bold; color: #0f172a;"><?php echo htmlspecialchars($order['payment_method'] ?? 'WhatsApp / Direct Invoice'); ?></div>
                </div>

                <div>
                    <small style="color: #64748b; text-transform: uppercase; font-weight: bold; font-size: 0.75rem;">Status</small>
                    <div>
                        <?php 
                            $status = strtoupper($order['status']);
                            $statusColor = ($status === 'COMPLETED') ? '#16a34a' : (($status === 'PROCESSING') ? '#0284c7' : '#d97706');
                        ?>
                        <span style="color: <?php echo $statusColor; ?>; font-weight: bold; text-transform: uppercase;">
                            ● <?php echo htmlspecialchars($status); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- ITEMS TABLE -->
            <h3 style="color: #002d62; margin-bottom: 15px;">Requested Items / Services</h3>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px;">
                <thead>
                    <tr style="background: #f1f5f9; text-align: left; color: #475569; font-size: 0.85rem;">
                        <th style="padding: 12px;">Item / Service</th>
                        <th style="padding: 12px; text-align: center;">Type</th>
                        <th style="padding: 12px; text-align: center;">Qty</th>
                        <th style="padding: 12px; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): 
                        $title    = htmlspecialchars($item['title'] ?? $item['item_title'] ?? 'Item');
                        $type     = strtoupper($item['item_type'] ?? 'PRODUCT');
                        $qty      = intval($item['quantity'] ?? $item['qty'] ?? 1);
                        $price    = floatval($item['price'] ?? 0);
                        $subtotal = $price * $qty;
                    ?>
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px; font-weight: bold; color: #1e293b;"><?php echo $title; ?></td>
                        <td style="padding: 12px; text-align: center;">
                            <span style="background: #e2e8f0; color: #475569; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">
                                <?php echo $type; ?>
                            </span>
                        </td>
                        <td style="padding: 12px; text-align: center;"><?php echo $qty; ?></td>
                        <td style="padding: 12px; text-align: right; font-weight: bold;">Ksh <?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- TOTAL & NOTES -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
                <div style="max-width: 50%; background: #fffbe2; border-left: 4px solid #eab308; padding: 10px 15px; border-radius: 4px; font-size: 0.85rem; color: #713f12;">
                    <strong>Notes:</strong> <?php echo htmlspecialchars($order['notes'] ?? 'Direct order via WhatsApp'); ?>
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 1.1rem; color: #64748b;">Total Amount:</span>
                    <div style="font-size: 1.8rem; font-weight: bold; color: #002d62;">
                        Ksh <?php echo number_format($order['total_amount'], 2); ?>
                    </div>
                </div>
            </div>

            <!-- ACTIONS -->
            <div style="display: flex; gap: 15px; justify-content: flex-end; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                <button onclick="window.print()" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer;">
                    🖨️ Print Receipt
                </button>
                <a href="https://wa.me/254722942293?text=<?php echo urlencode('Hello, following up on order reference: ' . $order['reference_code']); ?>" target="_blank" style="background: #25D366; color: white; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; display: inline-block;">
                    💬 Contact Support
                </a>
            </div>

        </div>

    <?php else: ?>
        <!-- ========================================== -->
        <!-- TICKET SEARCH / LOOKUP FORM                -->
        <!-- ========================================== -->
        <div style="background: white; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 40px; text-align: center; border-top: 5px solid #002d62;">
            <h2 style="color: #002d62; margin-top: 0;">Track Order or Ticket Status</h2>
            <p style="color: #64748b; margin-bottom: 25px;">Enter your ticket reference code below (e.g. <code>BES-XXXXXX</code>) to view order details and receipt status.</p>

            <?php if (!empty($error_message)): ?>
                <div style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="ticket.php" method="GET" style="display: flex; gap: 10px; max-width: 500px; margin: 0 auto;">
                <input type="text" 
                       name="ref" 
                       value="<?php echo htmlspecialchars($ref); ?>" 
                       placeholder="e.g. BES-4A8F21" 
                       required 
                       style="flex: 1; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem; font-weight: bold; color: #002d62; text-transform: uppercase;">
                <button type="submit" style="background: #002d62; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; font-size: 1rem; cursor: pointer;">
                    Track Ticket
                </button>
            </form>

            <div style="margin-top: 25px;">
                <a href="shop.php" style="color: #ff7300; text-decoration: none; font-weight: bold;">← Return to Shop</a>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>