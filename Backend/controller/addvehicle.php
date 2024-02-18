<?php
session_start();
require_once '../models/database.php';
require_once '../models/db_related_functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehicle_plate = htmlspecialchars($_POST['vehicle_plate'], ENT_QUOTES, 'UTF-8');
    $vehicle_unit = htmlspecialchars($_POST['vehicle_unit'], ENT_QUOTES, 'UTF-8');

    if (!$vehicle_unit || !$vehicle_plate) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Fill all the necessary informations"]);
        exit();
    }

    $response = addVehicle(
        strtoupper($vehicle_unit),
        strtoupper($vehicle_plate)
    );


    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
