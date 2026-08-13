<?php
error_reporting(0);
header('Content-Type: application/json');

// 1. Load central configuration
require_once __DIR__ . '/../config.php';

// 2. Read user input
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = trim($input['message'] ?? '');

if (empty($userMessage)) {
    echo json_encode(['reply' => 'Please enter a valid message.']);
    exit;
}

// 3. Verify API Key
if (!defined('GEMINI_API_KEY') || GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY_HERE' || empty(GEMINI_API_KEY)) {
    echo json_encode([
        'reply' => "Hello! I am the Blue Edge Assistant. For immediate support, please contact info@blueedge-sl.com."
    ]);
    exit;
}

// Fallback message shown to live website visitors if all API attempts fail
$clientFallbackMsg = "Our AI Assistant is currently undergoing brief maintenance. " .
                     "Please reach out to our team directly at info@blueedge-sl.com or call +254 722 942 293 for immediate assistance.";

// 4. Prompt & Payload Construction
$systemPrompt = "You are the friendly AI Support Assistant for Blue Edge Solutions Limited, an enterprise IT firm in Kenya.\n" .
                "RULES FOR YOUR RESPONSES:\n" .
                "1. Keep responses concise, helpful, and pleasant.\n" .
                "2. Avoid dense walls of text. Use clear line breaks and bullet points.\n" .
                "3. Use a few subtle emojis (e.g., 🔌, 💻, 📞, ✨) to make replies engaging.\n" .
                "4. When asking questions to help a customer, list them in clear numbered points.\n" .
                "5. Contact Info: info@blueedge-sl.com | +254 722 942 293.";

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

// 5. List of model candidates to try in order
$modelsToTry = [
    'gemini-2.5-flash',
    'gemini-2.0-flash',
    'gemini-1.5-flash',
    'gemini-1.5-pro',
    'gemini-flash-latest'
];

$aiReply = null;
$lastError = '';

// 6. Multi-Model Fallback Loop
foreach ($modelsToTry as $model) {
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . GEMINI_API_KEY;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    // SSL control for Local vs HostAfrica
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
        $lastError = "Network Error: " . $curlError;
        continue; // Try next model
    }

    $data = json_decode($response, true);

    // If text was generated successfully, break out of loop!
    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        $aiReply = $data['candidates'][0]['content']['parts'][0]['text'];
        break;
    }

    // Record error for local debugging
    if (isset($data['error']['message'])) {
        $lastError = "[$model] " . $data['error']['message'];
    }
}

// 7. Send Final Output
if ($aiReply !== null) {
    echo json_encode(['reply' => $aiReply]);
} else {
    // Log real error silently on server
    error_log("Gemini API Error: " . $lastError);
    
    // Display technical details on laptop, but friendly fallback on HostAfrica
    $reply = (defined('IS_LOCAL') && IS_LOCAL) 
        ? "API Error: " . $lastError 
        : $clientFallbackMsg;

    echo json_encode(['reply' => $reply]);
}