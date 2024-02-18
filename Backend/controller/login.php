<?php
session_start();
require_once '../models/database.php';
require_once '../models/db_related_functions.php';




if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];


    if (!$email || !$password) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "All fields are requied to fill"]);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "'Invalid Email Address'"]);
        exit();
    }

    $user = getUserByEmail($email);

    if (!$user) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Account Not Found", "body" => $_POST]);
        exit();
    }

    if (!password_verify($password, $user['password'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Wrong Username or Password"]);
        exit();
    }
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['username'] = $user['fname'];
    $_SESSION['id'] = $user['id'];

    if (strtolower($user['role']) !== "client") {
        updateStatus($user['id']);
    }

    setcookie('email', $email, time() + 3600, "/"); // Adjust path and domain as needed
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => "Login successful", "role" => $user["role"]]);
    exit();
}
