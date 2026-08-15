<?php 
// 1. Ensure PHP Session is running
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Generate CSRF Token for Secure AJAX Requests
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 3. RELIABLY INCLUDE DATABASE CONNECTION
if (file_exists(__DIR__ . '/db_connect.php')) {
    require_once __DIR__ . '/db_connect.php';
} elseif (file_exists(__DIR__ . '/includes/db_connect.php')) {
    require_once __DIR__ . '/includes/db_connect.php';
}

// 4. PERSISTENT SECURE GUEST COOKIE LOGIC
if (!isset($_COOKIE['guest_device_id'])) {
    $guest_id = bin2hex(random_bytes(16));
    setcookie('guest_device_id', $guest_id, [
        'expires'  => time() + (86400 * 30),
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

if (empty($_SESSION['cart']) && isset($_COOKIE['saved_guest_cart'])) {
    $decoded_cart = json_decode($_COOKIE['saved_guest_cart'], true);
    if (is_array($decoded_cart)) {
        $_SESSION['cart'] = $decoded_cart;
    }
}

// Calculate initial total cart quantity
$initial_cart_count = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $c_item) {
        $initial_cart_count += (int)($c_item['qty'] ?? 1);
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

// INITIAL NOTIFICATION LOAD (OPTIMIZED & SECURED)
$recent_notifications = [];
$unread_count = 0;
$user_role = $_SESSION['admin_role'] ?? 'STAFF_ADMIN';

if ($is_admin_area && !empty($_SESSION['admin_logged_in']) && isset($pdo)) {
    $pending_orders = [];
    $inquiries = [];
    $low_stock = [];
    $staff_actions = [];
    
    $pending_count = 0;
    $inquiry_count = 0;
    $stock_count = 0;
    $staff_action_count = 0;

    // 1. FETCH PENDING ORDERS
    try {
        $orderStmt = $pdo->prepare("
            SELECT id, 'order' AS notif_type, 
                   COALESCE(client_name, reference_code, CONCAT('Order #', id)) AS title, 
                   CONCAT('New order #', reference_code, ' (Ksh ', FORMAT(total_amount, 0), ')') AS description, 
                   created_at, 'index.php#orders-section' AS link
            FROM orders 
            WHERE status = 'Pending' AND (is_read = 0 OR is_read IS NULL)
            ORDER BY created_at DESC LIMIT 5
        ");
        $orderStmt->execute();
        $pending_orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

        $pending_count = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending' AND (is_read = 0 OR is_read IS NULL)")->fetchColumn();
    } catch (PDOException $e) {}

    // 2. FETCH CONTACT INQUIRIES
    try {
        $inqStmt = $pdo->prepare("
            SELECT id, 'inquiry' AS notif_type, name AS title, 
                   CONCAT('Inquiry: ', service_type) AS description, 
                   created_at, 'index.php#inquiries-section' AS link
            FROM contact_inquiries WHERE (is_read = 0 OR is_read IS NULL)
            ORDER BY created_at DESC LIMIT 4
        ");
        $inqStmt->execute();
        $inquiries = $inqStmt->fetchAll(PDO::FETCH_ASSOC);

        $inquiry_count = (int)$pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE (is_read = 0 OR is_read IS NULL)")->fetchColumn();
    } catch (PDOException $e) {}

    // 3. FETCH LOW STOCK ALERTS
    try {
        if (empty($_SESSION['dismiss_all_stock'])) {
            $dismissed = array_map('intval', $_SESSION['dismissed_stock_ids'] ?? []);
            $stock_query = "SELECT id, 'stock' AS notif_type, name AS title, CONCAT('Low Stock Alert: Only ', stock, ' left!') AS description, IFNULL(updated_at, NOW()) AS created_at, 'manage_products.php' AS link FROM products WHERE stock <= 5";
            
            if (!empty($dismissed)) {
                $in_clause = implode(',', $dismissed);
                $stock_query .= " AND id NOT IN ($in_clause)";
            }
            $stock_query .= " ORDER BY stock ASC LIMIT 4";
            
            $stockStmt = $pdo->prepare($stock_query);
            $stockStmt->execute();
            $low_stock = $stockStmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($dismissed)) {
                $in_clause = implode(',', $dismissed);
                $stock_count = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE stock <= 5 AND id NOT IN ($in_clause)")->fetchColumn();
            } else {
                $stock_count = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE stock <= 5")->fetchColumn();
            }
        }
    } catch (PDOException $e) {}

  // 4. FETCH STAFF ACTIVITY LOGS (Only for Super Admins, and excluding their own actions)
    if ($user_role === 'SUPER_ADMIN') {
        try {
            $actStmt = $pdo->prepare("
                SELECT id, 'staff_action' AS notif_type, 
                       username AS title, 
                       CONCAT(action, IF(details IS NOT NULL AND details != '', CONCAT(': ', details), '')) AS description, 
                       created_at, 'activity_logs.php' AS link
                FROM activity_logs 
                WHERE (is_read = 0 OR is_read IS NULL) AND user_role != 'SUPER_ADMIN'
                ORDER BY created_at DESC LIMIT 5
            ");
            $actStmt->execute();
            $staff_actions = $actStmt->fetchAll(PDO::FETCH_ASSOC);

            $staff_action_count = (int)$pdo->query("SELECT COUNT(*) FROM activity_logs WHERE (is_read = 0 OR is_read IS NULL) AND user_role != 'SUPER_ADMIN'")->fetchColumn();
        } catch (PDOException $e) {}
    }

    // Combine & Sort Notifications
    $recent_notifications = array_merge($pending_orders, $inquiries, $low_stock, $staff_actions);
    usort($recent_notifications, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    $recent_notifications = array_slice($recent_notifications, 0, 8);

    $unread_count = $pending_count + $inquiry_count + $stock_count + $staff_action_count;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    
    <title><?php echo htmlspecialchars($page_title ?? 'Blue Edge Solutions Limited | IT Infrastructure & Support'); ?></title>
    <meta name="description" content="Professional IT support, server infrastructure, cloud management, and cybersecurity solutions.">
    
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="<?php echo $base; ?>assets/js/carts.js"></script>
</head>
<body>

<?php if ($is_admin_area): ?>
    <header style="background: #002d62; color: white; padding: 10px 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000;">
        <div style="max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: nowrap; gap: 15px;">
            
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

            <?php if (!empty($_SESSION['admin_logged_in'])): ?>
            <nav style="display: flex; align-items: center; gap: 14px; font-size: 0.88rem; font-weight: 600; flex-shrink: 0;">
                <a href="index.php#orders-section" style="color: white; text-decoration: none; white-space: nowrap;">Orders</a>
                <a href="manage_products.php" style="color: white; text-decoration: none; white-space: nowrap;">Products</a>
                <a href="manage_articles.php" style="color: white; text-decoration: none; white-space: nowrap;">Blog Posts</a>
                
                <?php if ($user_role === 'SUPER_ADMIN'): ?>
                    <a href="manage-staff.php" style="color: #38bdf8; text-decoration: none; font-weight: 700; white-space: nowrap;">👥 Staff</a>
                    <a href="activity_logs.php" style="color: #cbd5e1; text-decoration: none; white-space: nowrap;">📋 Logs</a>
                <?php endif; ?>

                <div style="position: relative;">
                    <button id="notifBellBtn" onclick="toggleNotifDropdown()" style="background: rgba(255,255,255,0.1); border: none; color: white; padding: 6px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; position: relative; width: 34px; height: 34px; transition: background 0.2s;">
                        <i class="ph ph-bell" style="font-size: 1.15rem;"></i>
                        <span id="notifBadge" style="position: absolute; top: -2px; right: -2px; background: #ef4444; color: white; font-size: 0.6rem; font-weight: bold; width: 16px; height: 16px; border-radius: 50%; display: <?php echo $unread_count > 0 ? 'flex' : 'none'; ?>; align-items: center; justify-content: center; border: 2px solid #002d62;">
                            <?php echo $unread_count > 9 ? '9+' : $unread_count; ?>
                        </span>
                    </button>

                    <div id="notifDropdown" style="display: none; position: absolute; right: 0; top: 42px; width: 340px; background: white; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); border: 1px solid #e2e8f0; color: #1e293b; z-index: 1050; overflow: hidden;">
                        <div style="padding: 10px 14px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                            <strong style="color: #002d62; font-size: 0.85rem;">Action Required & Activity</strong>
                            <button onclick="markAllNotificationsRead()" style="background: none; border: none; color: #0284c7; font-size: 0.72rem; font-weight: 700; cursor: pointer;">Mark all as read</button>
                        </div>

                        <div id="notifListContainer" style="max-height: 280px; overflow-y: auto;">
                            <?php if (!empty($recent_notifications)): ?>
                                <?php foreach ($recent_notifications as $notif): ?>
                                    <div id="notif-item-<?php echo $notif['notif_type'] . '-' . $notif['id']; ?>" 
                                         onclick="handleNotifClick(event, '<?php echo $notif['id']; ?>', '<?php echo $notif['notif_type']; ?>', '<?php echo $notif['link']; ?>')"
                                         style="padding: 10px 14px; border-bottom: 1px solid #f1f5f9; font-size: 0.8rem; display: flex; align-items: flex-start; gap: 10px; cursor: pointer; transition: background 0.15s;"
                                         onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                                        
                                        <span style="margin-top: 2px; padding: 4px; border-radius: 6px; background: <?php echo $notif['notif_type'] === 'order' ? '#e0f2fe' : ($notif['notif_type'] === 'inquiry' ? '#f0fdf4' : ($notif['notif_type'] === 'stock' ? '#fef3c7' : '#f1f5f9')); ?>; color: <?php echo $notif['notif_type'] === 'order' ? '#0284c7' : ($notif['notif_type'] === 'inquiry' ? '#16a34a' : ($notif['notif_type'] === 'stock' ? '#d97706' : '#64748b')); ?>; font-size: 0.9rem;">
                                            <i class="ph <?php echo $notif['notif_type'] === 'order' ? 'ph-shopping-cart-simple' : ($notif['notif_type'] === 'inquiry' ? 'ph-chat-circle-dots' : ($notif['notif_type'] === 'stock' ? 'ph-warning-circle' : 'ph-user-gear')); ?>"></i>
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
                            <?php if ($user_role === 'SUPER_ADMIN'): ?>
                            <a href="activity_logs.php" style="flex: 1; padding: 9px; text-align: center; color: #002d62; font-weight: bold; font-size: 0.78rem; text-decoration: none;">
                                Activity Logs →
                            </a>
                            <?php endif; ?>
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
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function toggleNotifDropdown() {
        const dropdown = document.getElementById('notifDropdown');
        if (dropdown) {
            const isOpening = dropdown.style.display !== 'block';
            dropdown.style.display = isOpening ? 'block' : 'none';
            if (isOpening) {
                pollNewNotifications();
            }
        }
    }

    function renderNotificationsHTML(notifications) {
        const container = document.getElementById('notifListContainer');
        if (!container) return;

        if (!notifications || notifications.length === 0) {
            container.innerHTML = '<div style="padding: 25px; text-align: center; color: #94a3b8; font-size: 0.8rem;">🎉 All caught up! No pending alerts.</div>';
            return;
        }

        let html = '';
        notifications.forEach(notif => {
            const icon = notif.notif_type === 'order' ? 'ph-shopping-cart-simple' : (notif.notif_type === 'inquiry' ? 'ph-chat-circle-dots' : (notif.notif_type === 'stock' ? 'ph-warning-circle' : 'ph-user-gear'));
            const bg = notif.notif_type === 'order' ? '#e0f2fe' : (notif.notif_type === 'inquiry' ? '#f0fdf4' : (notif.notif_type === 'stock' ? '#fef3c7' : '#f1f5f9'));
            const color = notif.notif_type === 'order' ? '#0284c7' : (notif.notif_type === 'inquiry' ? '#16a34a' : (notif.notif_type === 'stock' ? '#d97706' : '#64748b'));

            html += `
                <div id="notif-item-${notif.notif_type}-${notif.id}" 
                     onclick="handleNotifClick(event, '${notif.id}', '${notif.notif_type}', '${notif.link}')"
                     style="padding: 10px 14px; border-bottom: 1px solid #f1f5f9; font-size: 0.8rem; display: flex; align-items: flex-start; gap: 10px; cursor: pointer; transition: background 0.15s;"
                     onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                    
                    <span style="margin-top: 2px; padding: 4px; border-radius: 6px; background: ${bg}; color: ${color}; font-size: 0.9rem;">
                        <i class="ph ${icon}"></i>
                    </span>
                    
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: bold; color: #002d62;">${escapeHtml(notif.title)}</span>
                            <small style="color: #94a3b8; font-size: 0.7rem;">${notif.created_at}</small>
                        </div>
                        <p style="margin: 2px 0 0 0; color: #475569; line-height: 1.3; font-size: 0.78rem;">${escapeHtml(notif.description)}</p>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function handleNotifClick(event, id, type, redirectUrl) {
        event.stopPropagation();
        
        const elem = document.getElementById('notif-item-' + type + '-' + id);
        if (elem) {
            elem.style.opacity = '0.4';
            elem.style.pointerEvents = 'none';
        }

        fetch('mark_notification_read.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
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

    function markAllNotificationsRead() {
        fetch('mark_notification_read.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
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

    function pollNewNotifications() {
        fetch('fetch_notifications.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateBadgeCount(data.unread_count);
                if (data.notifications) {
                    renderNotificationsHTML(data.notifications);
                }
            }
        })
        .catch(err => console.error('Polling error:', err));
    }

    setInterval(pollNewNotifications, 30000);

    document.addEventListener('click', function(event) {
        const bellBtn = document.getElementById('notifBellBtn');
        const dropdown = document.getElementById('notifDropdown');
        if (bellBtn && dropdown && !bellBtn.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
    </script>

<?php else: ?>
    <!-- CLIENT HEADER -->
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
                <button type="button" onclick="toggleCart()" style="background: none; border: none; color: #002d62; font-weight: bold; font-size: 1rem; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: color 0.2s; padding: 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16"><path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>
                    ( <span id="cartCount"><?php echo $initial_cart_count; ?></span> )
                </button>

                <a href="<?php echo $base; ?>contact.php" class="btn btn-primary">Get in Touch</a>
                
                <button class="mobile-menu-btn" aria-label="Open Menu">
                    <i class="ph ph-list"></i>
                </button>
            </div>
        </div> 
    </header>
<?php endif; ?>