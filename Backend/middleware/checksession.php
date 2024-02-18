<?php
session_start();

// get the session
if (isset($_SESSION['email']) && isset($_COOKIE['email'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => "Session Valid", "role" => $_SESSION['role']]);
    exit();
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => "No Valid Session"]);
    exit();
}
