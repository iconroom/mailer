<?php
// Prevent direct access to this configuration file
if (count(get_included_files()) == 1) {
    exit("Direct access not permitted.");
}

// Global Application Constants
define('TIMEOUT_SECONDS', 10);
define('DEFAULT_SENDER_NAME', 'BuildMailer PHP');

/**
 * Sanitizes input values to prevent Email Header Injection attacks (\r or \n).
 */
function sanitize_header_value($value) {
    return preg_replace('/[\r\n]/', '', trim($value));
}

/**
 * Checks if the target domain has valid MX records and returns the primary server host.
 */
function get_primary_mx_host($email) {
    $domain = substr(strrchr($email, "@"), 1);
    $mxhosts = [];
    
    if ($domain && getmxrr($domain, $mxhosts) && !empty($mxhosts)) {
        return $mxhosts[0]; // Return top priority MX server
    }
    return false;
}
?>
