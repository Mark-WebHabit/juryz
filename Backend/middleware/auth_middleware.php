<?php
session_start();

function checkUserSession() {
    // Check if the session is set and a cookie with the email exists
    if (isset($_SESSION['email']) && isset($_COOKIE['email'])) {
        // Verify that the email in the session matches the email in the cookie
        if ($_SESSION['email'] === $_COOKIE['email']) {
            // The user is authenticated, and the session matches the cookie
            return true; // Proceed with the script execution
        } else {
            // Session and cookie do not match, possibly a session hijacking attempt
            destroySession(); // Destroy the session to handle unauthorized access
            sendUnauthorizedResponse();
        }
    } else {
        // No valid session or cookie found, user is not authenticated
        sendUnauthorizedResponse();
    }
}

function destroySession() {
    // Unset all of the session variables.
    $_SESSION = array();

    // If it's desired to kill the session, also delete the session cookie.
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finally, destroy the session.
    session_destroy();
}

function sendUnauthorizedResponse() {
    header('Location: ../../Frontend/views/index.html');
    exit();
}

