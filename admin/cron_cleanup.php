<?php
// admin/cron_cleanup.php

// 1. Security Check (CLI or secret token via GET parameter)
if (php_sapi_name() !== 'cli' && ($_GET['token'] ?? '') !== 'YOUR_SECRET_CRON_TOKEN') {
    http_response_code(403);
    exit('Forbidden');
}

// 2. Include database connection from /includes/db_connect.php
require_once __DIR__ . '/../includes/db_connect.php';

try {
    $deleted = $pdo->exec("DELETE FROM orders WHERE status = 'Pending' AND created_at < NOW() - INTERVAL 3 DAY");
    echo "Successfully deleted $deleted expired pending orders.";
} catch (PDOException $e) {
    error_log("Cleanup failed: " . $e->getMessage());
}