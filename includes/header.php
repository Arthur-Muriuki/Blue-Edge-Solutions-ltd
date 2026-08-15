<?php 
// 1. Ensure PHP Session is running
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. RELIABLY INCLUDE DATABASE CONNECTION
require_once __DIR__ . '/db_connect.php';

// 3. PERSISTENT GUEST COOKIE LOGIC
if (!isset($_COOKIE['guest_device_id'])) {
    $guest_id = bin2hex(random_bytes(16));
    setcookie('guest_device_id', $guest_id, time() + (86400 * 30), "/");
}

if (empty($_SESSION['cart']) && isset($_COOKIE['saved_guest_cart'])) {
    $decoded_cart = json_decode($_COOKIE['saved_guest_cart'], true);
    if (is_array($decoded_cart)) {
        $_SESSION['cart'] = $decoded_cart;
    }
}

$base = $base_url ?? ''; 

// Auto-detect if current page is inside the /admin/ folder
$is_admin_area = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false);

// PREVENT BROWSER CACHING FOR ADMIN PAGES
if ($is_admin_area) {
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
}

$currentPage = basename($_SERVER['PHP_SELF']);
$isUsercraft = ($currentPage === 'usercraft.php' || (isset($page_title) && strpos(strtolower($page_title), 'usercraft') !== false));

// INITIAL NOTIFICATION LOAD (SERVED ON PAGE RENDER)
$recent_notifications = [];
$unread_count = 0;
$user_role = $_SESSION['admin_role'] ?? 'STAFF_ADMIN';

if ($is_admin_area && !empty($_SESSION['admin_logged_in']) && isset($pdo)) {
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

        // 2. Contact Inquiries
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

        $unread_count = $pending_count + $inquiry_count + $stock_count;

    } catch (PDOException $e) {
        $recent_notifications = [];
        $unread_count = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title><?php echo $page_title ?? 'Blue Edge Solutions Limited | IT Infrastructure & Support'; ?></title>
    <meta name="description" content="Professional IT support, server infrastructure, cloud management, and cybersecurity solutions.">
    
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

<?php if ($is_admin_area): ?>
    <!-- ==================================================== -->
    <!-- UNIFIED ADMIN HEADER                                 -->
    <!-- ==================================================== -->
    <header style="background: #002d62; color: white; padding: 10px 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000;">
        <div style="max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: nowrap; gap: 15px;">
            
            <!-- Left Side: Branding & Welcome -->
            <div style="display: flex; align-items: center; gap: 12px; flex-shrink: 0;">
                <a href="index.php" style="color: white; font-weight: 800; font-size: 1.15rem; text-decoration: none; display: flex; align-items: center; gap: 6px; white-space: nowrap;">
                    Blue Edge<span style="color: #ff7300;">.</span> <span style="background: rgba(255,255,255,0.15); font-size: 0.7rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Admin</span>
                </a>

                <?php if (!empty($_SESSION['admin_logged_in'])): ?>
                    <div style="display: flex; align-items: center; gap: 6px; border-left: 1px solid rgba(255,255,255,0.2); padding-left: 12px; font-size: 0.82rem; color: #cbd5e1; white-space: nowrap;">
                        <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_fullname'] ?? $_SESSION['admin_username']); ?></strong></span>
                        <span style="background: #ff7300; color: white; padding: 2px 7px; border-radius: 12px; font-size: 0.65rem; font-weight: 800;">
                            <?php echo htmlspecialchars($user_role); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Side: Navigation & Role-Aware Bell -->
            <?php if (!empty($_SESSION['admin_logged_in'])): ?>
            <nav style="display: flex; align-items: center; gap: 14px; font-size: 0.88rem; font-weight: 600; flex-shrink: 0;">
                <a href="index.php#orders-section" style="color: white; text-decoration: none; white-space: nowrap;">Orders</a>
                <a href="manage_products.php" style="color: white; text-decoration: none; white-space: nowrap;">Products</a>
                <a href="manage_articles.php" style="color: white; text-decoration: none; white-space: nowrap;">Blog Posts</a>
                
                <?php if ($user_role === 'SUPER_ADMIN'): ?>
                    <a href="manage-staff.php" style="color: #38bdf8; text-decoration: none; font-weight: 700; white-space: nowrap;">👥 Staff</a>
                    <a href="activity_logs.php" style="color: #cbd5e1; text-decoration: none; white-space: nowrap;">📋 Logs</a>
                <?php endif; ?>

                <!-- ACTIONABLE NOTIFICATION BELL UI -->
                <div style="position: relative;">
                    <button id="notifBellBtn" onclick="toggleNotifDropdown()" style="background: rgba(255,255,255,0.1); border: none; color: white; padding: 6px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; position: relative; width: 34px; height: 34px; transition: background 0.2s;">
                        <i class="ph ph-bell" style="font-size: 1.15rem;"></i>
                        <span id="notifBadge" style="position: absolute; top: -2px; right: -2px; background: #ef4444; color: white; font-size: 0.6rem; font-weight: bold; width: 16px; height: 16px; border-radius: 50%; display: <?php echo $unread_count > 0 ? 'flex' : 'none'; ?>; align-items: center; justify-content: center; border: 2px solid #002d62;">
                            <?php echo $unread_count > 9 ? '9+' : $unread_count; ?>
                        </span>
                    </button>

                    <!-- DROPDOWN CONTAINER -->
                    <div id="notifDropdown" style="display: none; position: absolute; right: 0; top: 42px; width: 340px; background: white; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); border: 1px solid #e2e8f0; color: #1e293b; z-index: 1050; overflow: hidden;">
                        <div style="padding: 10px 14px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                            <strong style="color: #002d62; font-size: 0.85rem;">Action Required</strong>
                            <button onclick="markAllNotificationsRead()" style="background: none; border: none; color: #0284c7; font-size: 0.72rem; font-weight: 700; cursor: pointer;">Mark all as read</button>
                        </div>

                        <div id="notifListContainer" style="max-height: 280px; overflow-y: auto;">
                            <?php if (!empty($recent_notifications)): ?>
                                <?php foreach ($recent_notifications as $notif): ?>
                                    <div id="notif-item-<?php echo $notif['notif_type'] . '-' . $notif['id']; ?>" 
                                         onclick="handleNotifClick(event, '<?php echo $notif['id']; ?>', '<?php echo $notif['notif_type']; ?>', '<?php echo $notif['link']; ?>')"
                                         style="padding: 10px 14px; border-bottom: 1px solid #f1f5f9; font-size: 0.8rem; display: flex; align-items: flex-start; gap: 10px; cursor: pointer; transition: background 0.15s;"
                                         onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                                        
                                        <span style="margin-top: 2px; padding: 4px; border-radius: 6px; background: <?php echo $notif['notif_type'] === 'order' ? '#e0f2fe' : ($notif['notif_type'] === 'inquiry' ? '#f0fdf4' : '#fef3c7'); ?>; color: <?php echo $notif['notif_type'] === 'order' ? '#0284c7' : ($notif['notif_type'] === 'inquiry' ? '#16a34a' : '#d97706'); ?>; font-size: 0.9rem;">
                                            <i class="ph <?php echo $notif['notif_type'] === 'order' ? 'ph-shopping-cart-simple' : ($notif['notif_type'] === 'inquiry' ? 'ph-chat-circle-dots' : 'ph-warning-circle'); ?>"></i>
                                        </span>
                                        
                                        <div style="flex: 1;">
                                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                                <span style="font-weight: bold; color: #002d62;"><?php echo htmlspecialchars($notif['title']); ?></span>
                                                <small style="color: #94a3b8; font-size: 0.7rem;"><?php echo date('M d, H:i', strtotime($notif['created_at'])); ?></small>
                                            </div>
                                            <p style="margin: 2px 0 0 0; color: #475569; line-height: 1.3; font-size: 0.78rem;"><?php echo htmlspecialchars($notif['description']); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div id="noNotifMsg" style="padding: 25px; text-align: center; color: #94a3b8; font-size: 0.8rem;">
                                    🎉 All caught up! No pending alerts.
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="display: flex; background: #f1f5f9; border-top: 1px solid #e2e8f0;">
                            <a href="index.php#orders-section" style="flex: 1; padding: 9px; text-align: center; color: #002d62; font-weight: bold; font-size: 0.78rem; text-decoration: none; border-right: 1px solid #e2e8f0;">
                                Orders →
                            </a>
                            <a href="manage_products.php" style="flex: 1; padding: 9px; text-align: center; color: #002d62; font-weight: bold; font-size: 0.78rem; text-decoration: none;">
                                Inventory →
                            </a>
                        </div>
                    </div>
                </div>

                <a href="profile.php" style="color: white; text-decoration: none; white-space: nowrap;">My Profile</a>
                <a href="../index.php" target="_blank" style="color: #94a3b8; text-decoration: none; font-size: 0.8rem; display: flex; align-items: center; gap: 4px; border: 1px solid #334155; padding: 4px 8px; border-radius: 6px; white-space: nowrap;">
                     View Site
                </a>
                <a href="logout.php" style="color: #f87171; text-decoration: none; font-weight: 700; background: rgba(239, 68, 68, 0.1); padding: 4px 10px; border-radius: 6px; white-space: nowrap;">
                    Logout
                </a>
            </nav>
            <?php endif; ?>

        </div>
    </header>

    <script>
    function toggleNotifDropdown() {
        const dropdown = document.getElementById('notifDropdown');
        if (dropdown) {
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
    }

    // Dismiss single notification on click & navigate to section
    function handleNotifClick(event, id, type, redirectUrl) {
        event.stopPropagation();
        
        // Remove item smoothly from UI
        const elem = document.getElementById('notif-item-' + type + '-' + id);
        if (elem) {
            elem.style.opacity = '0.4';
            elem.style.pointerEvents = 'none';
        }

        // Send AJAX request to mark as read
        fetch('mark_notification_read.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id, type: type })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateBadgeCount(data.unread_count);
                if (elem) elem.remove();
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                }
            }
        })
        .catch(err => console.error('Error marking notification read:', err));
    }

    // Mark all notifications as read
    function markAllNotificationsRead() {
        fetch('mark_notification_read.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'mark_all' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateBadgeCount(0);
                const container = document.getElementById('notifListContainer');
                if (container) {
                    container.innerHTML = '<div style="padding: 25px; text-align: center; color: #94a3b8; font-size: 0.8rem;">🎉 All caught up! No pending alerts.</div>';
                }
            }
        });
    }

    // Update Badge UI helper
    function updateBadgeCount(count) {
        const badge = document.getElementById('notifBadge');
        if (badge) {
            if (count > 0) {
                badge.innerText = count > 9 ? '9+' : count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    // 30-Second Live Polling for New Alerts
    function pollNewNotifications() {
        fetch('fetch_notifications.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateBadgeCount(data.unread_count);
            }
        })
        .catch(err => console.error('Polling error:', err));
    }

    // Start timer on load (runs every 30 seconds)
    setInterval(pollNewNotifications, 30000);

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const bellBtn = document.getElementById('notifBellBtn');
        const dropdown = document.getElementById('notifDropdown');
        if (bellBtn && dropdown && !bellBtn.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
    </script>

<?php else: ?>
    <!-- ==================================================== -->
    <!-- STANDARD PUBLIC CLIENT HEADER                       -->
    <!-- ==================================================== -->
    <header class="main-header">
        <div class="container nav-container">
            <a href="<?php echo $base; ?>index.php" class="brand-logo">
                <?php if ($isUsercraft): ?>
                    UserCraft<span>.</span>
                <?php else: ?>
                    Blue Edge<span>.</span>
                <?php endif; ?>
            </a>

            <nav class="desktop-nav">
                <ul class="nav-links">
                    <li><a href="<?php echo $base; ?>index.php">Home</a></li>
                    <li><a href="<?php echo $base; ?>about.php">About Us</a></li>
                    <li><a href="<?php echo $base; ?>services.php">Services</a></li>
                    <li><a href="<?php echo $base; ?>shop.php">Shop</a></li>
                    <li><a href="<?php echo $base; ?>contact.php">Contact</a></li>
                    <li><a href="<?php echo $base; ?>blog.php">Blog</a></li>
                    <li><a href="<?php echo $base; ?>usercraft.php">UserCraft</a></li>
                    <li><a href="<?php echo $base; ?>help.php">Help Center</a></li>
                </ul>
            </nav>

            <div class="nav-actions" style="display: flex; align-items: center; gap: 15px;">
                <button onclick="toggleCart()" style="background: none; border: none; color: #002d62; font-weight: bold; font-size: 1rem; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: color 0.2s; padding: 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16"><path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>
                    (<span id="cartCount"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>)
                </button>

                <a href="<?php echo $base; ?>contact.php" class="btn btn-primary">Get in Touch</a>
                
                <button class="mobile-menu-btn" aria-label="Open Menu">
                    <i class="ph ph-list"></i>
                </button>
            </div>
        </div> 
        
        <nav class="mobile-menu">
            <ul class="mobile-nav-links">
                <li><a href="<?php echo $base; ?>index.php">Home</a></li>
                <li><a href="<?php echo $base; ?>about.php">About Us</a></li>
                <li><a href="<?php echo $base; ?>services.php">Services</a></li>
                <li><a href="<?php echo $base; ?>shop.php">Shop</a></li>
                <li><a href="<?php echo $base; ?>blog.php">Blog</a></li>
                <li><a href="<?php echo $base; ?>usercraft.php">UserCraft</a></li>
                <li><a href="#" onclick="toggleCart(); return false;" class="mobile-contact-link" style="background: #e2e8f0; color: #002d62;">View Cart (<span id="mobileCartCount"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>)</a></li>
                <li><a href="<?php echo $base; ?>contact.php" class="mobile-contact-link">Get in Touch</a></li>
            </ul>
        </nav>
    </header>
<?php endif; ?>