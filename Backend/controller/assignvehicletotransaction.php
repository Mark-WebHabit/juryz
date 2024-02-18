<?php

session_start();
require_once '../models/database.php';
require_once '../models/db_related_functions.php';

// Check if the request method is POST and if the session and cookie are correctly set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['email']) && isset($_COOKIE['email']) && $_SESSION['email'] == $_COOKIE['email']) {
    // Get the raw POST data from the input stream
    $input = file_get_contents('php://input');
    // Decode the JSON data into a PHP array
    $data = json_decode($input, true);

    if ($data) {
        // Assuming your JSON contains 'vehicle' and 'transaction' keys
        $vehicleId = $data['vehicle'];
        $transactionId = $data['transaction'];

        // Proceed with your function to assign a vehicle to a transaction
        $response = assignVehicleToTransaction($vehicleId, $transactionId);

        // Send back a JSON response
        header('Content-Type: application/json');
        echo json_encode(["success" => true, "data" => $response]);
    } else {
        // Handle error in case JSON data is not received or cannot be decoded
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "Invalid or missing data"]);
    }
    exit();
}
