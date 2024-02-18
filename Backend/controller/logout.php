<?php
// Initialize the session.
session_start();

require_once '../models/database.php';
require_once '../models/db_related_functions.php';

logout();

// Unset the session variable.
if (isset($_SESSION['email'])) {
    unset($_SESSION['email']);
}

// Check if the email cookie exists and delete it.
if (isset($_COOKIE['email'])) {
    // Setting a cookie with a past expiration date removes it.
    setcookie('email', '', time() - 3600, '/'); // Adjust the path as needed.
}

// Destroy the session.
session_destroy();

// Redirect to the login page or homepage after logging out.
exit;
