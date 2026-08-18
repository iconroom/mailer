<?php
require_once __DIR__ . '/config.php';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = filter_var($_POST['to'], FILTER_VALIDATE_EMAIL);
    $replyTo = filter_var($_POST['replyTo'], FILTER_VALIDATE_EMAIL);
    $subject = htmlspecialchars($_POST['subject'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');

    if ($to && $replyTo && !empty($subject) && !empty($message)) {
        $result = send_direct_email($to, $replyTo, $subject, $message);

        if ($result['success']) {
            header("Location: result.php?status=success&to=" . urlencode($to) . "&replyTo=" . urlencode($replyTo));
            exit;
        } else {
            $errorMessage = $result['error'];
        }
    } else {
        $errorMessage = "Please enter valid email addresses and complete all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Direct Custom Mailer</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #f4f5f7; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
    .card { background: #ffffff; padding: 32px; border-radius: 8px; width: 100%; max-width: 420px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    h2 { margin-top: 0; color: #111827; }
    label { font-size: 14px; font-weight: 600; color: #374151; display: block; margin-bottom: 4px; }
    input, textarea { width: 100%; padding: 10px; margin-bottom: 16px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
    button { width: 100%; background-color: #1e3a8a; color: white; padding: 12px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 15px; }
    button:hover { background-color: #1e40af; }
    .alert { background-color: #fee2e2; border: 1px solid #f87171; color: #991b1b; padding: 12px; border-radius: 6px; font-size: 13px; margin-bottom: 16px; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Direct Socket Mailer</h2>
    
    <?php if ($errorMessage): ?>
      <div class="alert"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php">
      <label for="to">Recipient Email:</label>
      <input type="email" id="to" name="to" required placeholder="recipient@example.com">

      <label for="replyTo">Reply-To Address:</label>
      <input type="email" id="replyTo" name="replyTo" required placeholder="your-email@example.com">

      <label for="subject">Subject:</label>
      <input type="text" id="subject" name="subject" required placeholder="Subject text">

      <label for="message">Message Body:</label>
      <textarea id="message" name="message" rows="4" required placeholder="Write your message here..."></textarea>

      <button type="submit">Send Message Direct</button>
    </form>
  </div>
</body>
</html>
