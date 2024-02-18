<?php

session_start();
require_once '../models/database.php';
require_once '../models/db_related_functions.php';



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['email']) && isset($_COOKIE['email']) && $_SESSION['email'] == $_COOKIE['email']) {

    $vehicle_plate = htmlspecialchars($_POST['vehicle_plate'], ENT_QUOTES, 'UTF-8');
    $vehicle_unit = htmlspecialchars($_POST['vehicle_unit'], ENT_QUOTES, 'UTF-8');
    $driverId = $_POST['driver'];
    $status;
    $id = $_POST['id'];

    if (!$vehicle_plate || !$vehicle_unit) {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "All fields required"]);
        exit();
    }

    if ($driverId) {
        $status = "standby";
    } else {
        $status = "unassigned";
    }

    $vehicle = updateVehicle($id, $driverId, $vehicle_plate, $vehicle_unit, $status);


    header('Content-Type: application/json');
    echo json_encode(["success" => true, "data" => $vehicle]);
    exit();
}
