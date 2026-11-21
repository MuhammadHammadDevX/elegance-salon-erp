<?php
// Initialize session
session_start();

// Reset all session variables
$_SESSION = array();

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy active session
session_destroy();

// Clear authentication cookies
setcookie('elegance_user_id', '', time() - 3600, "/");
setcookie('elegance_user_name', '', time() - 3600, "/");
setcookie('elegance_user_email', '', time() - 3600, "/");
setcookie('elegance_user_role', '', time() - 3600, "/");

// Redirect to homepage
header("Location: index.php");
exit();
?>
