<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

// Get JSON input from the frontend
$data = json_decode(file_get_contents('php://input'), true);
$userMessage = trim($data['message'] ?? '');

if (empty($userMessage)) {
    echo json_encode(['reply' => 'Please type a valid question.']);
    exit;
}

// System Prompt for Blue Edge Assistant
$systemPrompt = "You are the friendly AI Support Assistant for Blue Edge Solutions Limited, an enterprise IT firm in Kenya.\n" .
                "RULES FOR YOUR RESPONSES:\n" .
                "1. Keep responses concise, helpful, and pleasant.\n" .
                "2. Avoid dense walls of text. Use clear line breaks and bullet points.\n" .
                "3. Use a few subtle emojis (e.g., 🔌, 💻, 📞, ✨) to make replies engaging.\n" .
                "4. When asking questions to help a customer, list them in clear numbered points.\n" .
                "5. Contact Info: info@blueedge-sl.com | +254 722 942 293.";

$requestBody = [
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

// Priority list of models to try if one is experiencing high demand
$modelsToTry = [
    'gemini-1.5-flash',
    'gemini-2.0-flash',
    'gemini-1.5-pro',
    'gemini-flash-latest'
];

$apiKey = GEMINI_API_KEY;
$replyText = null;

// Iterate through models until one responds successfully
foreach ($modelsToTry as $model) {
    $endpointUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey;

    $ch = curl_init($endpointUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $httpCode === 200) {
        $responseData = json_decode($response, true);
        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            $replyText = $responseData['candidates'][0]['content']['parts'][0]['text'];
            break; // Success! Break out of the fallback loop
        }
    }
}

// Return successful reply or a friendly fallback message
if ($replyText) {
    echo json_encode(['reply' => $replyText]);
} else {
    echo json_encode([
        'reply' => "I am currently experiencing a high volume of requests from Google AI servers. Please try resending your message in a few seconds! ⏱️"
    ]);
}