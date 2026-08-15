<?php
// admin/activity_logs.php - Super Admin Activity Audit Logs Dashboard
error_reporting(E_ALL);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Block unauthorized access to the logs
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'SUPER_ADMIN') {
    header("Location: index.php");
    exit();
}
// Enforce Admin Access
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// 1. Auto-detect database connection location
if (file_exists(__DIR__ . '/../db_connect.php')) {
    require_once __DIR__ . '/../db_connect.php';
} elseif (file_exists(__DIR__ . '/db_connect.php')) {
    require_once __DIR__ . '/db_connect.php';
} elseif (file_exists(__DIR__ . '/../includes/db_connect.php')) {
    require_once __DIR__ . '/../includes/db_connect.php';
} else {
    die("Database connection file not found. Please ensure db_connect.php exists.");
}

// 2. Auto-create & Auto-migrate 'activity_logs' database table
try {
    if (isset($pdo)) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS activity_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT DEFAULT NULL,
                username VARCHAR(100) DEFAULT 'System',
                user_role VARCHAR(50) DEFAULT 'Admin',
                action VARCHAR(255) NOT NULL,
                details TEXT DEFAULT NULL,
                ip_address VARCHAR(45) DEFAULT NULL,
                is_read TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // Migration: Check and add 'is_read' if missing from an existing table
        $checkCol = $pdo->query("SHOW COLUMNS FROM activity_logs LIKE 'is_read'");
        if ($checkCol && $checkCol->rowCount() === 0) {
            $pdo->exec("ALTER TABLE activity_logs ADD COLUMN is_read TINYINT(1) DEFAULT 0");
        }
    }
} catch (PDOException $e) {
    // Migration fallback error handler
}

// Automatically mark unread logs as read when Super Admin opens this page
try {
    if (isset($pdo)) {
        $pdo->exec("UPDATE activity_logs SET is_read = 1 WHERE is_read = 0 OR is_read IS NULL");
    }
} catch (Exception $e) {}

// 3. Search and Pagination Logic
$search = trim($_GET['search'] ?? '');
$page   = max(1, intval($_GET['page'] ?? 1));
$limit  = 15;
$offset = ($page - 1) * $limit;

$where_clause = "";
$params = [];

if (!empty($search)) {
    $where_clause = " WHERE username LIKE :search OR action LIKE :search OR details LIKE :search OR ip_address LIKE :search";
    $params[':search'] = "%{$search}%";
}

// Fetch total records count
$total_logs = 0;
try {
    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM activity_logs {$where_clause}");
    $count_stmt->execute($params);
    $total_logs = (int)$count_stmt->fetchColumn();
} catch (Exception $e) {
    $total_logs = 0;
}

$total_pages = max(1, ceil($total_logs / $limit));

// Fetch activity log records
$logs = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM activity_logs {$where_clause} ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $logs = [];
}

// Clear Logs Action
if (isset($_POST['clear_logs'])) {
    try {
        $pdo->exec("TRUNCATE TABLE activity_logs");
        header("Location: activity_logs.php?msg=cleared");
        exit();
    } catch (Exception $e) {
        $error = "Failed to clear logs.";
    }
}

$page_title = "Super Admin - Activity Logs";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        * { box-sizing: border-box; font-family: system-ui, -apple-system, sans-serif; }
        body { background-color: #f1f5f9; margin: 0; padding: 30px 20px; color: #1e293b; }
        .container { max-width: 1100px; margin: 0 auto; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; background: white; padding: 20px 25px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .header h1 { font-size: 1.5rem; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px; }
        
        .card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        
        .controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 15px; flex-wrap: wrap; }
        .search-box { display: flex; align-items: center; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; width: 320px; }
        .search-box input { border: none; background: transparent; outline: none; width: 100%; margin-left: 8px; font-size: 0.9rem; }
        
        .btn { padding: 9px 16px; border-radius: 6px; text-decoration: none; font-size: 0.88rem; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s; }
        .btn-danger { background: #fee2e2; color: #991b1b; }
        .btn-danger:hover { background: #fca5a5; }
        .btn-primary { background: #002d62; color: white; }
        .btn-primary:hover { background: #001a3a; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 0.9rem; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #f1f5f9; }
        th { background: #f8fafc; color: #475569; font-weight: 600; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.5px; }
        tr:hover { background: #f8fafc; }

        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .badge-admin { background: #e0f2fe; color: #0369a1; }
        .badge-system { background: #f3e8ff; color: #6b21a8; }
        .ip-code { font-family: monospace; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #475569; font-size: 0.8rem; }

        .empty-state { text-align: center; padding: 50px 20px; color: #64748b; }
        .empty-state i { font-size: 3rem; color: #cbd5e1; margin-bottom: 10px; }

        .pagination { display: flex; justify-content: flex-end; gap: 6px; margin-top: 20px; }
        .pagination a { padding: 6px 12px; background: white; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; color: #334155; font-size: 0.85rem; font-weight: 600; }
        .pagination a.active { background: #002d62; color: white; border-color: #002d62; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1><i class="ph ph-shield-check" style="color: #002d62;"></i> Activity Logs</h1>
        <a href="index.php" class="btn btn-primary"><i class="ph ph-arrow-left"></i> Back to Dashboard</a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'cleared'): ?>
        <div style="background: #dcfce7; color: #166534; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; font-size: 0.9rem;">
            ✓ All activity logs have been cleared successfully.
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="controls">
            <form method="GET" action="activity_logs.php" class="search-box">
                <i class="ph ph-magnifying-glass" style="color: #94a3b8; font-size: 1.1rem;"></i>
                <input type="text" name="search" placeholder="Search user, action, IP..." value="<?php echo htmlspecialchars($search); ?>">
            </form>

            <?php if (!empty($logs)): ?>
                <form method="POST" onsubmit="return confirm('Are you sure you want to delete ALL activity logs? This action cannot be undone.');">
                    <button type="submit" name="clear_logs" class="btn btn-danger">
                        <i class="ph ph-trash"></i> Clear All Logs
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <?php if (empty($logs)): ?>
            <div class="empty-state">
                <i class="ph ph-clock-counter-clockwise"></i>
                <h3>No Activity Logs Found</h3>
                <p>System activities, admin logins, and changes will automatically be recorded here.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Action Performed</th>
                        <th>IP Address</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td style="white-space: nowrap; color: #64748b; font-size: 0.85rem;">
                                <?php echo date('M d, Y - h:i A', strtotime($log['created_at'])); ?>
                            </td>
                            <td style="font-weight: 600; color: #0f172a;">
                                <?php echo htmlspecialchars($log['username'] ?? 'System'); ?>
                            </td>
                            <td>
                                <span class="badge <?php echo strtolower($log['user_role'] ?? '') === 'system' ? 'badge-system' : 'badge-admin'; ?>">
                                    <?php echo htmlspecialchars($log['user_role'] ?? 'Admin'); ?>
                                </span>
                            </td>
                            <td style="font-weight: 600; color: #1e293b;">
                                <?php echo htmlspecialchars($log['action']); ?>
                            </td>
                            <td>
                                <span class="ip-code"><?php echo htmlspecialchars($log['ip_address'] ?? '127.0.0.1'); ?></span>
                            </td>
                            <td style="color: #64748b; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?php echo htmlspecialchars($log['details'] ?? 'N/A'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="activity_logs.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="<?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

</body>
</html>