<?php
require_once 'includes/db_connect.php';

$ref = trim($_GET['ref'] ?? '');

if (empty($ref)) {
    die("Invalid or missing reference code.");
}

$ticket_data = null;
$ticket_type = ''; // 'order' or 'service'
$order_items = [];

try {
    // 1. Check if it's a Hardware Cart Order
    if (str_starts_with($ref, 'ORD-')) {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE reference_code = :ref");
        $stmt->execute(['ref' => $ref]);
        $ticket_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ticket_data) {
            $ticket_type = 'order';
            $stmt_items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
            $stmt_items->execute(['order_id' => $ticket_data['id']]);
            $order_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
        }
    } 
    // 2. Check if it's a Booking or Subscription Request
    else {
        $stmt = $pdo->prepare("SELECT * FROM service_requests WHERE reference_code = :ref");
        $stmt->execute(['ref' => $ref]);
        $ticket_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ticket_data) {
            $ticket_type = 'service';
        }
    }
} catch (PDOException $e) {
    die("Error loading ticket: " . $e->getMessage());
}

if (!$ticket_data) {
    die("Ticket reference code <strong>" . htmlspecialchars($ref) . "</strong> not found.");
}

$whatsappNumber = "254722942293";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?php echo htmlspecialchars($ref); ?> | Blue Edge Solutions</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f1f5f9; margin: 0; padding: 40px 20px; color: #1e293b; }
        
        .ticket-card { max-width: 650px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #e2e8f0; }
        
        .ticket-header { background: #002d62; color: white; padding: 30px; text-align: center; position: relative; }
        .ticket-header h1 { margin: 0 0 5px 0; font-size: 1.8rem; }
        .ticket-header p { margin: 0; color: #93c5fd; font-size: 0.95rem; }

        .ticket-body { padding: 30px; }
        
        .ref-badge { display: inline-block; background: #e0f2fe; color: #0369a1; padding: 6px 14px; border-radius: 20px; font-weight: bold; font-size: 0.9rem; margin-bottom: 20px; }
        
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; float: right; }
        .status-PENDING { background: #fef3c7; color: #b45309; }
        .status-APPROVED, .status-COMPLETED { background: #dcfce7; color: #15803d; }
        .status-DECLINED { background: #fee2e2; color: #b91c1c; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; font-size: 0.95rem; }
        .info-label { color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; background: #f8fafc; padding: 10px; font-size: 0.85rem; color: #475569; border-bottom: 1px solid #e2e8f0; }
        td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }

        .total-row { font-weight: bold; font-size: 1.1rem; color: #002d62; }

        .ticket-footer { background: #f8fafc; padding: 20px 30px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 18px; border-radius: 6px; font-weight: bold; text-decoration: none; cursor: pointer; border: none; font-size: 0.9rem; }
        .btn-wa { background: #25D366; color: white; }
        .btn-print { background: #e2e8f0; color: #334155; }

        @media print {
            body { background: white; padding: 0; }
            .ticket-card { box-shadow: none; border: none; }
            .ticket-footer { display: none; }
        }
    </style>
</head>
<body>

<div class="ticket-card">
    <!-- HEADER -->
    <div class="ticket-header">
        <h1>Blue Edge Solutions</h1>
        <p>Official Service Reference Ticket</p>
    </div>

    <!-- BODY -->
    <div class="ticket-body">
        
        <div>
            <span class="ref-badge"><?php echo htmlspecialchars($ticket_data['reference_code']); ?></span>
            <span class="status-badge status-<?php echo $ticket_data['status']; ?>">
                <?php echo htmlspecialchars($ticket_data['status']); ?>
            </span>
        </div>

        <div class="info-grid">
            <div>
                <div class="info-label">Date Generated</div>
                <div><?php echo date('M d, Y - h:i A', strtotime($ticket_data['created_at'])); ?></div>
            </div>
            <div>
                <div class="info-label">Request Category</div>
                <div><?php echo ($ticket_type === 'order') ? 'Hardware Purchase' : ucfirst($ticket_data['item_type']); ?></div>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px dashed #cbd5e1; margin: 20px 0;">

        <!-- HARDWARE ORDER DETAILS -->
        <?php if ($ticket_type === 'order'): ?>
            <h3 style="margin: 0 0 10px 0; color: #002d62; font-size: 1.1rem;">Order Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Item Description</th>
                        <th>Qty</th>
                        <th style="text-align: right;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_title']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td style="text-align: right;">Ksh <?php echo number_format($item['price'] * $item['quantity'], 0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="2" style="text-align: right; padding-top: 15px;">Total:</td>
                        <td style="text-align: right; padding-top: 15px;">Ksh <?php echo number_format($ticket_data['total_amount'], 0); ?></td>
                    </tr>
                </tbody>
            </table>

        <!-- SERVICE / SUBSCRIPTION DETAILS -->
        <?php else: ?>
            <h3 style="margin: 0 0 15px 0; color: #002d62; font-size: 1.1rem;">Client & Service Request Information</h3>
            <div class="info-grid">
                <div>
                    <div class="info-label">Client Name</div>
                    <div><?php echo htmlspecialchars($ticket_data['client_name']); ?></div>
                </div>
                <div>
                    <div class="info-label">Phone / WhatsApp</div>
                    <div><?php echo htmlspecialchars($ticket_data['client_phone']); ?></div>
                </div>
                <div>
                    <div class="info-label">Item / Plan Requested</div>
                    <div><strong><?php echo htmlspecialchars($ticket_data['item_title']); ?></strong></div>
                </div>
                <div>
                    <div class="info-label">Email</div>
                    <div><?php echo htmlspecialchars($ticket_data['client_email'] ?: 'N/A'); ?></div>
                </div>
            </div>

            <?php if (!empty($ticket_data['notes'])): ?>
                <div style="margin-top: 15px; background: #f8fafc; padding: 12px; border-radius: 6px; border-left: 3px solid #002d62;">
                    <div class="info-label" style="margin-bottom: 4px;">Client Notes</div>
                    <div style="font-size: 0.9rem; color: #475569;"><?php echo nl2br(htmlspecialchars($ticket_data['notes'])); ?></div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>

    <!-- FOOTER ACTIONS -->
    <div class="ticket-footer">
        <button onclick="window.print()" class="btn btn-print">🖨️ Print Ticket</button>
        <a href="https://wa.me/<?php echo $whatsappNumber; ?>?text=<?php echo urlencode("Hello, I am inquiring about ticket " . $ref); ?>" target="_blank" class="btn btn-wa">💬 Contact Us on WhatsApp</a>
    </div>
</div>

</body>
</html>