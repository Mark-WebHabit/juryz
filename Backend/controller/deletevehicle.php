<?php
session_start();
require_once '../models/database.php';
require_once '../models/db_related_functions.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];

    if (!$id) {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "Id not found"]);
        exit();
    }
    $result = deleteVehicle();


    header('Content-Type: application/json');
    echo json_encode(["success" => true, "data" => $result]);
    exit();
}
