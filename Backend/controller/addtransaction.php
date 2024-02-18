<?php
session_start();
require_once '../models/database.php';
require_once '../models/db_related_functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reciever_name = htmlspecialchars($_POST['reciever_name'], ENT_QUOTES, 'UTF-8');
    $reciever_email = filter_input(INPUT_POST, 'reciever_email', FILTER_SANITIZE_EMAIL);
    $dropoff_address = htmlspecialchars($_POST['dropoff_address'], ENT_QUOTES, 'UTF-8');
    $package_weight = htmlspecialchars($_POST['package_weight'], ENT_QUOTES, 'UTF-8');
    $package_quantity = htmlspecialchars($_POST['package_quantity'], ENT_QUOTES, 'UTF-8');
    $package_size = htmlspecialchars($_POST['package_size'], ENT_QUOTES, 'UTF-8');
    $pickup_schedule = htmlspecialchars($_POST['pickup_schedule'], ENT_QUOTES, 'UTF-8');
    $item_list = htmlspecialchars($_POST['item_list'], ENT_QUOTES, 'UTF-8');
    $sender_address = htmlspecialchars($_POST['sender_address'], ENT_QUOTES, 'UTF-8');
    $contact_person = htmlspecialchars($_POST['contact_person'], ENT_QUOTES, 'UTF-8');
    $sender_note = htmlspecialchars($_POST['sender_note'], ENT_QUOTES, 'UTF-8');

    // reciever email is optional and item list also sender note


    if (!$reciever_name  || !$dropoff_address || !$package_weight || !$package_quantity || !$package_size || !$pickup_schedule || !$sender_address || !$contact_person) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Fill all the necessary informations", "body" => $_POST, "hm" => $reciever_name]);
        exit();
    }

    if ($reciever_email && !filter_var($reciever_email, FILTER_VALIDATE_EMAIL)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Invalid Email", "body" => $_POST]);
        exit();
    }

    $response = addTransaction(
        $reciever_name,
        $reciever_email,
        $dropoff_address,
        $package_weight,
        $package_quantity,
        $package_size,
        $pickup_schedule,
        $item_list,
        $sender_address,
        $contact_person,
        $sender_note
    );


    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
