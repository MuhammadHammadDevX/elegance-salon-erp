<?php
// Start session if not already active
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

// Database connection credentials
$host = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "";
$dbname = getenv('DB_NAME') ?: "salon";
$port = getenv('DB_PORT') ? intval(getenv('DB_PORT')) : 3306;

// Initialize MySQLi database connection
$conn = @new mysqli($host, $username, $password, $dbname, $port);

// Check database connection
if ($conn->connect_error) {
    die("<div style='font-family:sans-serif;padding:30px;background:#121212;color:#ffffff;border-radius:12px;margin:50px auto;max-width:620px;text-align:center;box-shadow:0 15px 35px rgba(0,0,0,0.6);border:1px solid #d4af37;'><h2 style='color:#d4af37;margin-top:0;letter-spacing:1px;'>✦ ELEGANCE SALON KARACHI ✦</h2><h3 style='color:#ff6b6b;font-weight:500;margin-top:5px;'>Database Connection Required</h3><p style='color:#aaa;font-size:14px;line-height:1.5;'>Could not establish a connection to MySQL database <strong>" . htmlspecialchars($dbname) . "</strong> on <strong>" . htmlspecialchars($host) . "</strong>.</p><p style='font-size:12px;color:#888;background:#1e1e1e;padding:8px;border-radius:6px;display:inline-block;'>Error: " . htmlspecialchars($conn->connect_error) . "</p><div style='background:#1a1a1a;border:1px solid #333;padding:18px;border-radius:8px;font-size:13px;text-align:left;color:#ccc;margin-top:20px;'><p style='margin:0 0 10px 0;font-weight:600;color:#d4af37;'>Quick Setup Checklist:</p><ol style='margin:0;padding-left:22px;line-height:1.8;'><li>Start <strong>Apache</strong> and <strong>MySQL</strong> in your XAMPP / Laragon control panel.</li><li>Open <a href='http://localhost/phpmyadmin' target='_blank' style='color:#d4af37;text-decoration:underline;'>phpMyAdmin</a> and create database <code>salon</code>.</li><li>Import the SQL file located at: <code>Database/salon.sql</code>.</li><li>Refresh this page or verify credentials in <code>Source Code/config.php</code>.</li></ol></div></div>");
}

// Auto-restore user session from persistent authentication cookies if active
if (empty($_SESSION['user_id']) && isset($_COOKIE['elegance_user_id']) && !empty($_COOKIE['elegance_user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['elegance_user_id'];
    $_SESSION['user_name'] = $_COOKIE['elegance_user_name'] ?? '';
    $_SESSION['user_email'] = $_COOKIE['elegance_user_email'] ?? '';
    $_SESSION['user_role'] = $_COOKIE['elegance_user_role'] ?? 'client';
}

// Set UTF-8 character encoding for proper language and symbol rendering
$conn->set_charset("utf8mb4");

// Helper to thoroughly clean any string of double/triple encoded HTML entities (e.g. &amp;)
if (!function_exists('clean_text')) {
    function clean_text($str) {
        if ($str === null) return '';
        $val = (string)$str;
        for ($i = 0; $i < 3; $i++) {
            $decoded = html_entity_decode($val, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($decoded === $val) break;
            $val = $decoded;
        }
        return $val;
    }
}

// Helper to safely output text into HTML without double-escaping entities
if (!function_exists('safe_html')) {
    function safe_html($str) {
        return htmlspecialchars(clean_text($str), ENT_QUOTES, 'UTF-8');
    }
}
?>