<?php

session_start();
require_once '../models/database.php';
require_once '../models/db_related_functions.php';



if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_SESSION['email']) && isset($_COOKIE['email']) && $_SESSION['email'] == $_COOKIE['email']) {

    $transaction = getTransactionDetailsById($_GET['id']);

    header('Content-Type: application/json');
    echo json_encode(["success" => true, "data" => $transaction]);
    exit();
}
