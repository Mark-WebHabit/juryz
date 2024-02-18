<?php

session_start();
require_once '../models/database.php';
require_once '../models/db_related_functions.php';



if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_SESSION['email']) && isset($_COOKIE['email']) && $_SESSION['email'] == $_COOKIE['email']) {
    $search = htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8');

    $result = searchUsers($search);


    header('Content-Type: application/json');
    echo json_encode(["success" => true, "data" => $result]);
    exit();
}
