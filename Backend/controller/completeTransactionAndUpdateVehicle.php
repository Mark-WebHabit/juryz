<?php

session_start();
require_once '../models/database.php';
require_once '../models/db_related_functions.php';



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['email']) && isset($_COOKIE['email']) && $_SESSION['email'] == $_COOKIE['email']) {

    $transactionId = $_POST['transactionId'];
    $filePath = $_FILES['file']['name']; // The path (or name) of the file.
    $file = $_FILES['file']; // The file itself.

    $response = completeTransactionAndUpdateVehicle($transactionId, $filePath, $file);

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
