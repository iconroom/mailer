<?php
if (count(get_included_files()) == 1) {
    exit("Direct access not permitted.");
}

define('DEFAULT_SENDER_NAME', 'TOPSUN GLOBAL');
define('DEFAULT_SENDER_EMAIL', 'no-reply@topsunglobal.com');

/**
 * Direct Socket Mailer via Port 25
 */
function send_direct_email($to, $replyTo, $subject, $message) {
    // 1. Extract domain from recipient address
    $domain = substr(strrchr($to, "@"), 1);
    if (!$domain) {
        return ["success" => false, "error" => "Invalid recipient email address."];
    }

    // 2. Resolve target domain's MX records
    if (!getmxrr($domain, $mxHosts) || empty($mxHosts)) {
        return ["success" => false, "error" => "Could not find MX mail servers for domain: $domain"];
    }

    $targetMx = $mxHosts[0];

    // 3. Connect directly via raw TCP socket on Port 25
    $socket = @fsockopen($targetMx, 25, $errno, $errstr, 10);
    if (!$socket) {
        return ["success" => false, "error" => "Failed to connect to MX server ($targetMx:25): $errstr"];
    }

    $getResponse = function() use ($socket) {
        return fgets($socket, 512);
    };

    // Read initial greeting
    $getResponse();

    // 4. Send SMTP Handshake and Mail Commands
    fputs($socket, "HELO " . gethostname() . "\r\n");
    $getResponse();

    fputs($socket, "MAIL FROM: <" . DEFAULT_SENDER_EMAIL . ">\r\n");
    $getResponse();

    fputs($socket, "RCPT TO: <$to>\r\n");
    $getResponse();

    fputs($socket, "DATA\r\n");
    $getResponse();

    // 5. Construct Raw Headers including custom Reply-To
    $headers  = "From: " . DEFAULT_SENDER_NAME . " <" . DEFAULT_SENDER_EMAIL . ">\r\n";
    $headers .= "Reply-To: $replyTo\r\n";
    $headers .= "To: $to\r\n";
    $headers .= "Subject: $subject\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";

    $body = "
    <html>
      <body style='font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9;'>
        <div style='background: #ffffff; padding: 24px; border-radius: 8px; max-width: 500px;'>
          <h2 style='color: #111827; margin-top: 0;'>$subject</h2>
          <p style='color: #374151; line-height: 1.6;'>$message</p>
          <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;' />
          <p style='font-size: 12px; color: #6b7280;'>Configured Reply-To address: <strong>$replyTo</strong></p>
        </div>
      </body>
    </html>
    \r\n.\r\n";

    fputs($socket, $headers . $body);
    $response = $getResponse();

    fputs($socket, "QUIT\r\n");
    fclose($socket);

    if (strpos($response, '250') !== false) {
        return ["success" => true];
    }

    return ["success" => false, "error" => "MX Server Response Error: " . $response];
}
?>
