<?php
header('Content-Type: application/json');
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Invalid request method']));
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (empty($data['item_id']) || empty($data['client_name']) || empty($data['client_phone'])) {
    http_response_code(400);
    exit(json_encode(['error' => 'Missing required fields']));
}

// Generate unique reference (SUB-XXXXXX or BOK-XXXXXX)
$prefix = ($data['item_type'] === 'subscription') ? 'SUB-' : 'BOK-';
$ref_code = $prefix . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

try {
    $stmt = $pdo->prepare("
        INSERT INTO service_requests 
        (reference_code, item_id, item_title, item_type, client_name, client_phone, client_email, notes, status) 
        VALUES (:ref, :item_id, :title, :type, :name, :phone, :email, :notes, 'PENDING')
    ");

    $stmt->execute([
        'ref'      => $ref_code,
        'item_id'  => intval($data['item_id']),
        'title'    => $data['item_title'],
        'type'     => $data['item_type'],
        'name'     => $data['client_name'],
        'phone'    => $data['client_phone'],
        'email'    => $data['client_email'] ?? '',
        'notes'    => $data['notes'] ?? ''
    ]);

    // Build URL protocol safely
    $is_https = false;
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $is_https = true;
    } elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
        $is_https = true;
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $is_https = true;
    }

    $protocol  = $is_https ? "https://" : "http://";
    $domain    = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base_dir  = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    $ticket_url = $protocol . $domain . $base_dir . "/ticket.php?ref=" . $ref_code;

    echo json_encode([
        'success'    => true,
        'reference'  => $ref_code,
        'ticket_url' => $ticket_url
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit;
}
?>