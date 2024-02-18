<?php

session_start();
require_once '../models/database.php';
require_once '../models/db_related_functions.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_SESSION['email']) && isset($_COOKIE['email']) && $_SESSION['email'] == $_COOKIE['email']) {
    // Use the null coalescing operator to provide a default value of null
    $filter = $_GET['filter'] ?? null;
    if ($filter) {
        $transactions = getAllTransactionsAuth($filter);
    } else {
        $transactions = getAllTransactionsAuth();
    }

    header('Content-Type: application/json');
    echo json_encode(["success" => true, "data" => $transactions]);
    exit();
}
