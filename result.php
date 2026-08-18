<?php
$status = $_GET['status'] ?? 'error';
$to = $_GET['to'] ?? '';
$replyTo = $_GET['replyTo'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Status - Iconroom Mailer</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #f4f5f7; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
    .card { background: #ffffff; padding: 32px; border-radius: 8px; width: 100%; max-width: 420px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); text-align: center; }
    .success-icon { color: #059669; font-size: 48px; margin-bottom: 12px; }
    .error-icon { color: #dc2626; font-size: 48px; margin-bottom: 12px; }
    h2 { margin: 0 0 12px 0; color: #111827; }
    p { color: #4b5563; font-size: 14px; margin: 6px 0; }
    a { display: inline-block; margin-top: 20px; color: #4f46e5; text-decoration: none; font-weight: 600; font-size: 14px; }
    a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="card">
    <?php if ($status === 'success'): ?>
      <div class="success-icon">✓</div>
      <h2>Message Dispatched!</h2>
      <p>Sent to: <strong><?= htmlspecialchars($to) ?></strong></p>
      <p>Reply-To: <strong><?= htmlspecialchars($replyTo) ?></strong></p>
    <?php else: ?>
      <div class="error-icon">✕</div>
      <h2>Delivery Failed</h2>
      <p>Unable to send message via Brevo API.</p>
    <?php endif; ?>
    
    <a href="index.php">← Send Another Email</a>
  </div>
</body>
</html>
