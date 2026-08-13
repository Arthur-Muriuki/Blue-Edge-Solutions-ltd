<?php
error_reporting(0);
header('Content-Type: application/json');

// Load central configuration
require_once __DIR__ . '/../config.php';

// Get POST input
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = trim($input['message'] ?? '');

if (empty($userMessage)) {
    echo json_encode(['reply' => 'Please enter a valid message.']);
    exit;
}

// Check if API key is configured
if (!defined('GEMINI_API_KEY') || GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY_HERE' || empty(GEMINI_API_KEY)) {
    echo json_encode([
        'reply' => "Hello! I am the Blue Edge Assistant. (API Key not configured in config.php)."
    ]);
    exit;
}

// Payload construction
$systemPrompt = "You are the AI Help Desk Assistant for Blue Edge Solutions Limited, a Kenyan enterprise IT firm. " .
                "You assist clients with questions about IT infrastructure, cybersecurity, cloud management, and hardware. " .
                "Be polite, professional, and concise. Contact: info@blueedge-sl.com | +254 722 942 293.";

$payload = [
    'system_instruction' => [
        'parts' => [
            ['text' => $systemPrompt]
        ]
    ],
    'contents' => [
        [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage]
            ]
        ]
    ]
];

$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . GEMINI_API_KEY;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

// Automatically handle SSL bypass based on IS_LOCAL constant
if (defined('IS_LOCAL') && IS_LOCAL === true) {
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
} else {
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
}

$response = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    echo json_encode(['reply' => "Network Error: " . $curlError]);
    exit;
}

$data = json_decode($response, true);

if (isset($data['error'])) {
    echo json_encode(['reply' => "API Error: " . ($data['error']['message'] ?? 'Unable to connect to AI.')]);
    exit;
}

$aiReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, I could not process your request.';

echo json_encode(['reply' => $aiReply]);