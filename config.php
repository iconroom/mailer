<?php
// Prevent direct access to config.php
if (count(get_included_files()) == 1) {
    exit("Direct access not permitted.");
}

// Retrieve API Key from Render Environment Variables (checking multiple fallbacks)
$apiKey = $_ENV['BREVO_API_KEY'] ?? $_SERVER['BREVO_API_KEY'] ?? getenv('BREVO_API_KEY') ?: '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';

define('BREVO_API_KEY', $apiKey);
define('DEFAULT_SENDER_NAME', 'TOPSUN GLOBAL');
define('DEFAULT_SENDER_EMAIL', 'no-reply@topsunglobal.com');

/**
 * Sends transactional email via Brevo HTTP v3 API
 */
function send_brevo_email($to, $replyTo, $subject, $message) {
    $apiKey = BREVO_API_KEY;

    if (!$apiKey) {
        return ["success" => false, "error" => "BREVO_API_KEY is missing in Render Environment Variables."];
    }

    $payload = [
        "sender" => [
            "name" => DEFAULT_SENDER_NAME,
            "email" => DEFAULT_SENDER_EMAIL
        ],
        "to" => [
            ["email" => $to]
        ],
        "replyTo" => [
            "email" => $replyTo
        ],
        "subject" => $subject,
        "htmlContent" => "
            <html>
            <head><meta charset='utf-8'></head>
            <body style='font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9;'>
              <div style='background: #ffffff; padding: 20px; border-radius: 6px; max-width: 500px;'>
                <h2 style='color: #333;'>$subject</h2>
                <p style='color: #555; line-height: 1.5;'>$message</p>
                <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;' />
                <p style='font-size: 12px; color: #888;'>Reply-To configured address: <strong>$replyTo</strong></p>
              </div>
            </body>
            </html>
        "
    ];

    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'accept: application/json',
            'api-key: ' . $apiKey,
            'content-type: application/json'
        ]
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        return ["success" => true];
    }

    return ["success" => false, "error" => "Brevo API Error (HTTP $httpCode): " . $response];
}
?>
