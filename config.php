<?php
if (count(get_included_files()) == 1) {
    exit("Direct access not permitted.");
}

// Safely retrieve the API key from environment variables
$apiKey = $_ENV['RESEND_API_KEY'] ?? $_SERVER['RESEND_API_KEY'] ?? getenv('RESEND_API_KEY');

define('RESEND_API_KEY', $apiKey);
define('DEFAULT_SENDER_NAME', 'TOPSUN GLOBAL');
define('DEFAULT_SENDER_EMAIL', 'onboarding@update.topsunglobal.com');

/**
 * Direct Email Dispatcher via HTTPS / Port 443
 */
function send_direct_email($to, $replyTo, $subject, $message) {
    if (!RESEND_API_KEY) {
        return ["success" => false, "error" => "RESEND_API_KEY environment variable is missing on Render."];
    }

    $payload = [
        "from" => DEFAULT_SENDER_NAME . " <" . DEFAULT_SENDER_EMAIL . ">",
        "to" => [$to],
        "reply_to" => $replyTo,
        "subject" => $subject,
        "html" => "
        <html>
          <body style='font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9;'>
            <div style='background: #ffffff; padding: 24px; border-radius: 8px; max-width: 500px;'>
              <h2 style='color: #111827; margin-top: 0;'>" . htmlspecialchars($subject) . "</h2>
              <p style='color: #374151; line-height: 1.6;'>" . nl2br(htmlspecialchars($message)) . "</p>
              <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;' />
              <p style='font-size: 12px; color: #6b7280;'>Configured Reply-To address: <strong>" . htmlspecialchars($replyTo) . "</strong></p>
            </div>
          </body>
        </html>"
    ];

    $ch = curl_init('https://api.resend.com:443/emails');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_PORT => 443,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . RESEND_API_KEY,
            'Content-Type: application/json'
        ]
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        return ["success" => true];
    }

    return ["success" => false, "error" => "HTTPS Error (HTTP $httpCode): " . $response];
}
?>
