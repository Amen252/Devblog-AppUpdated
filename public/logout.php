<?php
// 1. Initialize the session to access it
session_start();

// 2. Unset all session variables (clears user_id, user_name, etc.)
$_SESSION = array();

// 3. Destroy the session cookie in the browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Final destruction of the session on the server
session_destroy();

// 5. Redirect the user to the homepage
header("Location: index.php");
exit();
?>