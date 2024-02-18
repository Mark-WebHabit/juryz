<?php

require_once '../models/database.php';
require_once '../models/db_related_functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $fname = htmlspecialchars($_POST['fname'], ENT_QUOTES, 'UTF-8');
    $lname = htmlspecialchars($_POST['lname'], ENT_QUOTES, 'UTF-8');
    $contact = htmlspecialchars($_POST['contact'], ENT_QUOTES, 'UTF-8');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $cpass = $_POST['cpass'];


    if (!$fname || !$lname  || !$email || !$password || !$cpass || !$contact) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "All fields are requied to fill"]);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "'Invalid Email Address'"]);
        exit();
    }

    if ($password !== $cpass) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Password don't matched"]);
        exit();
    }

    $hashedPass = password_hash($password, PASSWORD_DEFAULT);


    $response = registerUser($fname, $lname, $email,  $hashedPass, $contact);
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
