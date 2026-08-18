<?php
$status = '';
$statusType = '';

// Retrieve API key from Render Environment Variables
$apiKey = getenv('xsmtpsib-f8bd5b7f232a2d9a15ef7731515ca6ac647277988081d0e5bb7903481d4d070c-GM5iwWthWoy8QchE/');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = filter_var($_POST['to'], FILTER_VALIDATE_EMAIL);
    $replyTo = filter_var($_POST['replyTo'], FILTER_VALIDATE_EMAIL);
    $subject = htmlspecialchars($_POST['subject'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');

    if (!$apiKey) {
        $status = "Error: BREVO_API_KEY environment variable is not set in Render.";
        $statusType = "error";
    } elseif ($to && $replyTo && $subject && $message) {
        
        // Construct Brevo v3 API JSON payload
        $data = [
            "sender" => [
                "name" => "TOPSUN GLOBAL",
                "email" => "no-reply@topsunglobal.com"
            ],
            "to" => [
                ["email" => $to]
            ],
            "replyTo" => [
                "email" => $replyTo
            ],
            "subject" => $subject,
            "htmlContent" => "<html><body><h3>$subject</h3><p>$message</p><hr/><p><small>Reply-To: $replyTo</small></p></body></html>"
        ];

        // Send payload over HTTP POST via cURL
        $ch = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
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
            $status = "Email successfully dispatched to $to!";
            $statusType = "success";
        } else {
            $status = "Delivery failed (HTTP $httpCode): " . $response;
            $statusType = "error";
        }
    } else {
        $status = "Please provide valid email addresses and fill all fields.";
        $statusType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Render PHP Mailer</title>
  <style>
    body { font-family: -apple-system, sans-serif; background: #f4f5f7; display: flex; justify-content: center; padding-top: 50px; }
    .card { background: white; padding: 30px; border-radius: 8px; width: 400px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    input, textarea, button { width: 100%; margin-bottom: 12px; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
    button { background: #4f46e5; color: white; font-weight: bold; cursor: pointer; border: none; }
    .status { padding: 10px; border-radius: 4px; margin-bottom: 12px; font-size: 14px; }
    .success { background: #d1fae5; color: #065f46; }
    .error { background: #fee2e2; color: #991b1b; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Render PHP Mailer</h2>
    <?php if ($status): ?>
      <div class="status <?= $statusType ?>"><?= $status ?></div>
    <?php endif; ?>
    <form method="POST">
      <label>Recipient Email:</label>
      <input type="email" name="to" required placeholder="recipient@example.com">
      
      <label>Reply-To Email:</label>
      <input type="email" name="replyTo" required placeholder="your-email@example.com">
      
      <label>Subject:</label>
      <input type="text" name="subject" required placeholder="Test Subject">
      
      <label>Message:</label>
      <textarea name="message" rows="4" required placeholder="Write your message..."></textarea>
      
      <button type="submit">Send via Render</button>
    </form>
  </div>
</body>
</html>
