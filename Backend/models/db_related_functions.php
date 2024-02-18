<?php


function getUserByEmail($email)
{
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user ? $user : false;
}

function getTransactions()
{
    global $mysqli;

    // Prepare the SQL statement
    $stmt = $mysqli->prepare("SELECT * FROM transactions ");


    // Execute the statement
    $stmt->execute();

    // Get the result set from the statement
    $result = $stmt->get_result();

    // Initialize an array to hold all transactions
    $transactions = [];

    // Fetch each row and add it to the transactions array
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }

    // Close the statement
    $stmt->close();

    // Return the transactions array, or false if it's empty
    return !empty($transactions) ? $transactions : [];
}
function getVehicleCount()
{
    global $mysqli;

    // Prepare the SQL statement
    $stmt = $mysqli->prepare("SELECT * FROM vehicle ");


    // Execute the statement
    $stmt->execute();

    // Get the result set from the statement
    $result = $stmt->get_result();

    // Initialize an array to hold all transactions
    $vehicles = [];

    // Fetch each row and add it to the transactions array
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }

    // Close the statement
    $stmt->close();

    // Return the transactions array, or false if it's empty
    return !empty($vehicles) ? $vehicles : [];
}

function getVehicles()
{
    global $mysqli;

    // Adjust the SQL statement to LEFT JOIN with the users table to get driver names
    $stmt = $mysqli->prepare("
        SELECT v.*, CONCAT(u.fname, ' ', u.lname) AS drivername
        FROM vehicle v
        LEFT JOIN users u ON v.driver = u.id
    ");

    // Execute the statement
    $stmt->execute();

    // Get the result set from the statement
    $result = $stmt->get_result();

    // Initialize an array to hold all vehicles
    $vehicles = [];

    // Fetch each row and add it to the vehicles array
    while ($row = $result->fetch_assoc()) {
        // If there's no driver assigned, you might want to set 'drivername' to 'N/A' or similar
        if ($row['driver'] === null) {
            $row['drivername'] = 'N/A';
        }

        $vehicles[] = $row;
    }

    // Close the statement
    $stmt->close();

    // Return the vehicles array
    return $vehicles;
}

function getDrivers()
{
    global $mysqli;

    // Prepare the SQL statement
    $stmt = $mysqli->prepare("SELECT * FROM users  WHERE role = ? ");

    $role = "driver";
    $stmt->bind_param("s", $role);

    // Execute the statement
    $stmt->execute();

    // Get the result set from the statement
    $result = $stmt->get_result();

    // Initialize an array to hold all transactions
    $drivers = [];

    // Fetch each row and add it to the transactions array
    while ($row = $result->fetch_assoc()) {
        $drivers[] = $row;
    }

    // Close the statement
    $stmt->close();

    // Return the transactions array, or false if it's empty
    return !empty($drivers) ? $drivers : [];
}
function getTransaction($id)
{
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM transactions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $transaction = $result->fetch_assoc();
    $stmt->close();
    return $transaction ? $transaction : false;
}

function getUsers()
{
    global $mysqli;

    // Prepare the SQL statement
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE role = ?");

    // Bind the email parameter from the session
    $role = 'client';
    $stmt->bind_param("s", $role);

    // Execute the statement
    $stmt->execute();

    // Get the result set from the statement
    $result = $stmt->get_result();

    // Initialize an array to hold all transactions
    $users = [];

    // Fetch each row and add it to the transactions array
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    // Close the statement
    $stmt->close();

    // Return the transactions array, or false if it's empty
    return !empty($users) ? $users : false;
}

function getAllTransactionsAuth($filter = null)
{
    global $mysqli;

    // Prepare the SQL statement

    if (!$filter) {
        $stmt = $mysqli->prepare("SELECT * FROM transactions WHERE sender_email = ?");

        // Bind the email parameter from the session
        $email = $_SESSION['email'];
        $stmt->bind_param("s", $email);
    } else {
        $stmt = $mysqli->prepare("SELECT * FROM transactions WHERE sender_email = ? AND  status = ?");

        // Bind the email parameter from the session
        $email = $_SESSION['email'];
        $stmt->bind_param("ss", $email, $filter);
    }



    // Execute the statement
    $stmt->execute();

    // Get the result set from the statement
    $result = $stmt->get_result();

    // Initialize an array to hold all transactions
    $transactions = [];

    // Fetch each row and add it to the transactions array
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }

    // Close the statement
    $stmt->close();

    // Return the transactions array, or false if it's empty
    return !empty($transactions) ? $transactions : [];
}


function registerUser($fname, $lname, $email, $hashedPass, $contact)
{
    global $mysqli;

    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    $existingUser = getUserByEmail($email);

    if ($existingUser) {
        return ['success' => false, 'message' => "Email Taken"];
    }

    // Create a formatted date string for "now" in the Asia/Manila timezone
    $now = new DateTime("now", new DateTimeZone('Asia/Manila'));
    $formattedNow = $now->format("Y-m-d H:i:s"); // Assign formatted string to $formattedNow

    $stmt = $mysqli->prepare("INSERT INTO users (fname, lname, email, password, contact, date_joined) VALUES (?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        return ['success' => false, 'message' => "Registration failed.", 'error' => $mysqli->error];
    }

    // Use $formattedNow instead of $now in bind_param
    $stmt->bind_param("ssssss", $fname, $lname, $email, $hashedPass, $contact, $formattedNow);

    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => "Registration successful."];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => "Registration failed.", 'error' => $mysqli->error];
    }
}

function addTransaction(
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
) {

    global $mysqli;

    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    $weight = intval($package_weight);
    $qty = intval($package_quantity);

    $stmt = $mysqli->prepare("INSERT INTO transactions (reciever_name, reciever_email, dropoff_address, package_weight, package_quantity, package_size, pickup_schedule, item_list, sender_address, contact_person, sender_note, sender_email) VALUES (?, ?, ?, ?, ?, ?,?,?,?,?,?,?)");
    $stmt->bind_param("sssiisssssss", $reciever_name, $reciever_email, $dropoff_address, $weight, $qty, $package_size, $pickup_schedule, $item_list, $sender_address, $contact_person, $sender_note, $_SESSION['email']);


    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => "Added Transaction."];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => "Failed To Add Transaction.", 'error' => $mysqli->error];
    }
}


function addVehicle(
    $vehicle_unit,
    $vehicle_plate
) {
    global $mysqli;

    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    $stmt = $mysqli->prepare("INSERT INTO vehicle (vehicle_unit,
vehicle_plate) VALUES (?, ?)");
    $stmt->bind_param("ss", $vehicle_unit, $vehicle_plate);


    if ($stmt->execute()) {
        $stmt->close();
        return ['success' => true, 'message' => "Added Vehicle."];
    } else {
        $stmt->close();
        return ['success' => false, 'message' => "Failed To Add Vehicle.", 'error' => $mysqli->error];
    }
}


function deleteVehicle()
{
    global $mysqli;

    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {

        $id = $_GET['id'];

        // Sanitize the input to prevent SQL Injection
        $vehicle_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

        // Prepare the SQL statement to prevent SQL injection
        $stmt = $mysqli->prepare("DELETE FROM vehicle WHERE id = ?");
        $stmt->bind_param("i", $vehicle_id);


        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => "Deleted Vehicle"];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => "Failed To Delete Vehicle.", 'error' => $mysqli->error];
        }
    } else {
        echo "Invalid item ID.";
    }
}


function getAvailableDrivers()
{
    global $mysqli;

    // Prepare the SQL statement
    $stmt = $mysqli->prepare("
        SELECT users.*
        FROM users
        LEFT JOIN vehicle ON users.id = vehicle.driver
        WHERE users.role = 'driver' AND vehicle.driver IS NULL
    ");

    // Execute the statement
    $stmt->execute();

    // Get the result set from the statement
    $result = $stmt->get_result();

    // Initialize an array to hold all drivers
    $drivers = [];

    // Fetch each row and add it to the drivers array
    while ($row = $result->fetch_assoc()) {
        $drivers[] = $row;
    }

    // Close the statement
    $stmt->close();

    // Return the drivers array, or an empty array if no drivers found
    return $drivers;
}

function updateVehicle($vehicleId, $newDriverId, $newVehiclePlate, $newVehicleUnit, $newStatus)
{
    global $mysqli;

    // Prepare the SQL statement to update the vehicle details
    $stmt = $mysqli->prepare("
        UPDATE vehicle 
        SET driver = ?, vehicle_plate = ?, vehicle_unit = ?, status = ? 
        WHERE id = ?
    ");

    // Check if the statement was prepared successfully
    if (!$stmt) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        return false;
    }

    // Bind the parameters to the SQL statement
    if (!$stmt->bind_param("isssi", $newDriverId, $newVehiclePlate, $newVehicleUnit, $newStatus, $vehicleId)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        return false;
    }

    // Execute the statement
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        return false;
    }



    // Close the statement
    $stmt->close();

    return true;
}


function getAllEmployee()
{
    global $mysqli; // Ensure this is your database connection variable

    $query = "
        SELECT 
            users.*,
            vehicle.status AS vehicle_status,
            vehicle.vehicle_unit,
            vehicle.vehicle_plate
        FROM 
            users
        LEFT JOIN 
            vehicle ON users.id = vehicle.driver
        WHERE 
            users.role = 'driver';
    ";

    $result = $mysqli->query($query);

    $drivers = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $drivers[] = $row;
        }
        $result->free();
    } else {
        echo "Error: " . $mysqli->error;
        return false; // Or handle error as appropriate
    }

    return $drivers;
}

function updateStatus($id)
{
    global $mysqli;

    // Prepare the SQL statement to update the vehicle details
    $stmt = $mysqli->prepare("
        UPDATE users 
        SET status = 1 
        WHERE id = ?
    ");

    // Check if the statement was prepared successfully
    if (!$stmt) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        return false;
    }

    // Bind the parameters to the SQL statement
    if (!$stmt->bind_param("i", $id)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        return false;
    }

    // Execute the statement
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        return false;
    }



    // Close the statement
    $stmt->close();
}



function deleteEmployee()
{
    global $mysqli;

    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {

        $id = $_GET['id'];

        // Sanitize the input to prevent SQL Injection
        $emp_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

        // Prepare the SQL statement to prevent SQL injection
        $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $emp_id);


        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => "Removed Employee"];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => "Failed To Remove Employee.", 'error' => $mysqli->error];
        }
    } else {
        echo "Invalid item ID.";
    }
}

function searchUsers($searchTerm)
{
    global $mysqli; // Your mysqli database connection

    // Prepare the SQL statement with placeholders
    $query = "
    SELECT * FROM users 
    WHERE (LOWER(fname) LIKE ? OR LOWER(lname) LIKE ? OR LOWER(email) LIKE ?) AND role = 'client';
";

    // Prepare the statement
    $stmt = $mysqli->prepare($query);

    // Check if the statement was prepared successfully
    if (!$stmt) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        return false;
    }

    // The search term, with wildcards for partial matching
    $param = '%' . strtolower($searchTerm) . '%';

    // Bind parameters and execute the statement
    $stmt->bind_param("sss", $param, $param, $param);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    // Close the statement
    $stmt->close();

    return $users;
}

function updateUserRole()
{
    global $mysqli; // Your mysqli database connection
    $newRole = "driver";
    $userId = $_GET['id'];

    if (!$userId) {
        return false;
    }

    // SQL statement to update the role of a user
    $stmt = $mysqli->prepare("UPDATE users SET role = ? WHERE id = ?");

    // Check if the statement was prepared successfully
    if (!$stmt) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        return false;
    }

    // Bind the new role and user ID to the prepared statement
    if (!$stmt->bind_param("si", $newRole, $userId)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        return false;
    }

    // Execute the statement
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        return false;
    }


    // Close the statement
    $stmt->close();

    return true;
}


function getPendingTransactions()
{
    global $mysqli;

    // Prepare the SQL statement
    $stmt = $mysqli->prepare("SELECT * FROM transactions WHERE status = 'pending' ");


    // Execute the statement
    $stmt->execute();

    // Get the result set from the statement
    $result = $stmt->get_result();

    // Initialize an array to hold all transactions
    $transactions = [];

    // Fetch each row and add it to the transactions array
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }


    // Close the statement
    $stmt->close();

    // Return the transactions array, or false if it's empty
    return !empty($transactions) ? $transactions : [];
}


function getTransactionsCoreData()
{
    global $mysqli;

    $query = "
        SELECT 
            t.id, 
            t.receiver_name, 
            t.status, 
            t.pickup_schedule, 
            t.package_size, 
            t.sender_email, 
            v.vehicle_unit,
            CONCAT(u.fname, ' ', u.lname) AS driver_name
        FROM transactions t
        LEFT JOIN vehicles v ON t.vehicle = v.id
        LEFT JOIN users u ON v.driver = u.id
        WHERE u.role = 'driver' OR u.role IS NULL
    ";

    $result = $mysqli->query($query);
    if (!$result) {
        echo "Error: " . $mysqli->error;
        return [];
    }

    $transactions = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();

    return $transactions;
}

function getTransactionsCoreDataWithStatusPending()
{
    global $mysqli;

    // Add a condition in the WHERE clause for transactions with a 'pending' status
    $query = "
        SELECT 
            t.id, 
            t.reciever_name, 
            t.status, 
            t.pickup_schedule, 
            t.package_size, 
            t.sender_email, 
            v.vehicle_unit,
            CONCAT(u.fname, ' ', u.lname) AS driver_name
        FROM transactions t
        LEFT JOIN vehicle v ON t.vehicle = v.id
        LEFT JOIN users u ON v.driver = u.id
        WHERE t.status = 'pending' AND (u.role = 'driver' OR u.role IS NULL)
    ";

    $result = $mysqli->query($query);
    if (!$result) {
        echo "Error: " . $mysqli->error;
        return [];
    }

    $transactions = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();

    return $transactions;
}

function getTransactionsCoreDataWithStatusCompleted()
{
    global $mysqli;

    // Add a condition in the WHERE clause for transactions with a 'pending' status
    $query = "
        SELECT 
            t.id, 
            t.reciever_name, 
            t.status, 
            t.pickup_schedule, 
            t.package_size, 
            t.sender_email, 
            v.vehicle_unit,
            CONCAT(u.fname, ' ', u.lname) AS driver_name
        FROM transactions t
        LEFT JOIN vehicle v ON t.vehicle = v.id
        LEFT JOIN users u ON v.driver = u.id
        WHERE t.status = 'completed' AND (u.role = 'driver' OR u.role IS NULL)
    ";

    $result = $mysqli->query($query);
    if (!$result) {
        echo "Error: " . $mysqli->error;
        return [];
    }

    $transactions = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();

    return $transactions;
}

function getTransactionsCoreDataWithStatusCurrent()
{
    global $mysqli;

    // Add a condition in the WHERE clause for transactions with a 'pending' status
    $query = "
        SELECT 
            t.id, 
            t.reciever_name, 
            t.status, 
            t.pickup_schedule, 
            t.package_size, 
            t.sender_email, 
            v.vehicle_unit,
            CONCAT(u.fname, ' ', u.lname) AS driver_name
        FROM transactions t
        LEFT JOIN vehicle v ON t.vehicle = v.id
        LEFT JOIN users u ON v.driver = u.id
        WHERE t.status = 'journey' AND (u.role = 'driver' OR u.role IS NULL)
    ";

    $result = $mysqli->query($query);
    if (!$result) {
        echo "Error: " . $mysqli->error;
        return [];
    }

    $transactions = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();

    return $transactions;
}


function getTransactionsCoreDataWithStatusCancelled()
{
    global $mysqli;

    // Add a condition in the WHERE clause for transactions with a 'pending' status
    $query = "
        SELECT 
            t.id, 
            t.reciever_name, 
            t.status, 
            t.pickup_schedule, 
            t.package_size, 
            t.sender_email, 
            v.vehicle_unit,
            CONCAT(u.fname, ' ', u.lname) AS driver_name
        FROM transactions t
        LEFT JOIN vehicle v ON t.vehicle = v.id
        LEFT JOIN users u ON v.driver = u.id
        WHERE t.status = 'cancelled' AND (u.role = 'driver' OR u.role IS NULL)
    ";

    $result = $mysqli->query($query);
    if (!$result) {
        echo "Error: " . $mysqli->error;
        return [];
    }

    $transactions = $result->fetch_all(MYSQLI_ASSOC);
    $result->free();

    return $transactions;
}

// if issue persists diako sure sa pinalitan ko dto
function getTransactionDetailsById($transactionId)
{
    global $mysqli;

    $query = "
        SELECT 
            t.id, 
            t.reciever_name, 
            t.reciever_email, 
            t.dropoff_address, 
            t.package_weight, 
            t.package_quantity, 
            t.package_size, 
            t.pickup_schedule, 
            t.item_list, 
            t.sender_address, 
            t.contact_person, 
            t.sender_note, 
            t.status, 
            t.sender_email, 
            t.shipped_date, 
            t.delivery_proof, 
            v.id AS vehicle, 
            u.id AS driver, 
            CONCAT(u.fname, ' ', u.lname) AS driver_name, 
            u.email AS driver_email, 
            u.contact AS driver_contact, 
            v.vehicle_unit, 
            v.vehicle_plate,
            CASE 
                WHEN t.status = 'journey' THEN 'journey' 
                ELSE 'standby' 
            END AS vehicle_status
        FROM transactions t
        LEFT JOIN vehicle v ON t.vehicle = v.id
        LEFT JOIN users u ON v.driver = u.id
        WHERE t.id = ? AND t.status = ? AND (u.role = 'driver' OR u.role IS NULL)
    ";

    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        echo "Error preparing statement: " . $mysqli->error;
        return null;
    }

    $filter = $_GET['filter'];

    $stmt->bind_param("ii", $transactionId, $filter);
    if (!$stmt->execute()) {
        echo "Error executing statement: " . $stmt->error;
        return null;
    }

    $result = $stmt->get_result();
    $details = $result->fetch_assoc();
    $stmt->close();


    return $details ? $details : null;
}

function getStandbyVehiclesWithDrivers()
{
    global $mysqli;

    $query = "
        SELECT 
            v.id AS vehicle_id,
            v.vehicle_unit,
            v.vehicle_plate,
            v.status AS vehicle_status,
            u.id AS driver_id,
            CONCAT(u.fname, ' ', u.lname) AS driver_name,
            u.email AS driver_email,
            u.contact AS driver_contact
        FROM vehicle v
        JOIN users u ON v.driver = u.id
        WHERE v.status = 'standby'
    ";

    $vehicles = [];
    if ($result = $mysqli->query($query)) {
        while ($row = $result->fetch_assoc()) {
            $vehicles[] = $row;
        }
        $result->free();
    } else {
        echo "Error: " . $mysqli->error;
    }

    return $vehicles;
}


function assignVehicleToTransaction($vehicleId, $transactionId)
{
    global $mysqli;

    // Begin transaction
    $mysqli->begin_transaction();

    try {
        // Verify vehicle is on standby and has an assigned driver
        $vehicleQuery = "
            SELECT v.driver
            FROM vehicle v
            WHERE v.id = ? AND v.status = 'standby' AND v.driver IS NOT NULL";
        $vehicleStmt = $mysqli->prepare($vehicleQuery);
        $vehicleStmt->bind_param('i', $vehicleId);
        $vehicleStmt->execute();
        $vehicleResult = $vehicleStmt->get_result();
        if ($vehicleResult->num_rows == 0) {
            throw new Exception("Vehicle not available or does not have an assigned driver.");
        }
        $vehicle = $vehicleResult->fetch_assoc();
        $driverId = $vehicle['driver'];
        $vehicleStmt->close();

        // Verify transaction is pending
        $transactionQuery = "
            SELECT id
            FROM transactions
            WHERE id = ? AND status = 'pending'";
        $transactionStmt = $mysqli->prepare($transactionQuery);
        $transactionStmt->bind_param('i', $transactionId);
        $transactionStmt->execute();
        $transactionResult = $transactionStmt->get_result();
        if ($transactionResult->num_rows == 0) {
            throw new Exception("Transaction is not pending or does not exist.");
        }
        $transactionStmt->close();

        // Update vehicle status to 'journey'
        $updateVehicleQuery = "
            UPDATE vehicle
            SET status = 'journey'
            WHERE id = ?";
        $updateVehicleStmt = $mysqli->prepare($updateVehicleQuery);
        $updateVehicleStmt->bind_param('i', $vehicleId);
        $updateVehicleStmt->execute();
        if ($updateVehicleStmt->affected_rows == 0) {
            throw new Exception("Failed to update vehicle status.");
        }
        $updateVehicleStmt->close();

        // Update transaction with vehicle, driver ID, and set status to 'current'
        $updateTransactionQuery = "
            UPDATE transactions
            SET vehicle = ?, driver = ?, status = 'journey'
            WHERE id = ?";
        $updateTransactionStmt = $mysqli->prepare($updateTransactionQuery);
        $updateTransactionStmt->bind_param('iii', $vehicleId, $driverId, $transactionId);
        $updateTransactionStmt->execute();
        if ($updateTransactionStmt->affected_rows == 0) {
            throw new Exception("Failed to update transaction.");
        }
        $updateTransactionStmt->close();

        // Commit transaction
        $mysqli->commit();

        return ['success' => true, 'message' => "Vehicle and driver successfully assigned to transaction and status updated to current."];
    } catch (Exception $e) {
        // Rollback transaction on error
        $mysqli->rollback();

        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getJourneyTransactionWithVehicleAndDriver()
{
    global $mysqli;
    $driverId = $_SESSION['id'];

    if (!$driverId) {
        return null;
    }

    $query = "
        SELECT 
            t.*,
            v.vehicle_unit,
            v.vehicle_plate,
            v.status AS vehicle_status,
            u.fname AS driver_fname,
            u.lname AS driver_lname,
            u.email AS driver_email,
            u.contact AS driver_contact
        FROM transactions t
        JOIN vehicle v ON t.vehicle = v.id
        JOIN users u ON v.driver = u.id
        WHERE t.status = 'journey' AND u.id = ?
        LIMIT 1
    ";

    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        echo "Error preparing statement: " . $mysqli->error;
        return [];
    }

    $stmt->bind_param("i", $driverId);
    if (!$stmt->execute()) {
        echo "Error executing statement: " . $stmt->error;
        return [];
    }

    $result = $stmt->get_result();
    $transaction = $result->fetch_assoc();

    $stmt->close();

    return $transaction ? $transaction : null;
}

function completeTransactionAndUpdateVehicle($transactionId, $filePath, $file)
{
    global $mysqli;

    // Define the target directory for file uploads
    $fileName = basename($file['name']);
    $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/Client/Frontend/assets/uploads/";
    $targetFile = $targetDir . basename($_FILES["file"]["name"]);
    $uploadOk = 1;

    var_dump($targetFile);

    // Check if file already exists
    if (file_exists($targetFile)) {
        return ['success' => false, 'message' => "Sorry, file already exists."];
    }

    // Attempt to move the uploaded file to the target directory
    if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
        return ['success' => false, 'message' => "Sorry, there was an error uploading your file."];
    }

    // Begin transaction
    $mysqli->begin_transaction();

    try {
        // Fetch vehicle and driver ID from the transaction
        $fetchVehicleAndDriverQuery = "SELECT vehicle, driver FROM transactions WHERE id = ? AND status = 'journey'";
        $fetchStmt = $mysqli->prepare($fetchVehicleAndDriverQuery);
        $fetchStmt->bind_param('i', $transactionId);
        $fetchStmt->execute();
        $result = $fetchStmt->get_result();
        if ($result->num_rows == 0) {
            throw new Exception("Transaction not pending or does not exist.");
        }
        $transactionData = $result->fetch_assoc();
        $vehicleId = $transactionData['vehicle'];
        $driverId = $transactionData['driver'];
        $fetchStmt->close();

        // Set current date and time
        $currentDateTime = date("Y-m-d H:i:s");

        // Update transaction to 'completed', set shipped_date and delivery_proof
        $updateTransactionQuery = "UPDATE transactions SET status = 'completed', shipped_date = ?, delivery_proof = ? WHERE id = ?";
        $updateTransactionStmt = $mysqli->prepare($updateTransactionQuery);
        $updateTransactionStmt->bind_param('ssi', $currentDateTime, $targetFile, $transactionId);
        $updateTransactionStmt->execute();
        if ($updateTransactionStmt->affected_rows == 0) {
            throw new Exception("Failed to update transaction.");
        }
        $updateTransactionStmt->close();

        // Update vehicle status to 'standby'
        $updateVehicleQuery = "UPDATE vehicle SET status = 'standby' WHERE id = ?";
        $updateVehicleStmt = $mysqli->prepare($updateVehicleQuery);
        $updateVehicleStmt->bind_param('i', $vehicleId);
        $updateVehicleStmt->execute();
        if ($updateVehicleStmt->affected_rows == 0) {
            throw new Exception("Failed to update vehicle status.");
        }
        $updateVehicleStmt->close();

        // Commit transaction
        $mysqli->commit();

        return ['success' => true, 'message' => "Transaction completed and vehicle status updated successfully, file uploaded."];
    } catch (Exception $e) {
        // Rollback transaction on error
        $mysqli->rollback();
        // Attempt to delete the uploaded file if the transaction fails
        @unlink($targetFile);
        return ['success' => false, 'message' => "Error: " . $e->getMessage()];
    }
}


function cancelTransaction($transactionId)
{
    global $mysqli;

    // Prepare the SQL statement to update the transaction status
    $query = "UPDATE transactions SET status = 'cancelled' WHERE id = ?";

    // Prepare the statement
    $stmt = $mysqli->prepare($query);

    // Check if the statement was prepared successfully
    if (!$stmt) {
        return ['success' => false, 'message' => "Error preparing statement: " . $mysqli->error];
    }

    // Bind the transaction ID to the parameter
    $stmt->bind_param("i", $transactionId);

    // Execute the statement
    if (!$stmt->execute()) {
        // Close the statement before returning
        $stmt->close();
        return ['success' => false, 'message' => "Error executing statement: " . $stmt->error];
    }

    // Check if any rows were updated
    if ($stmt->affected_rows > 0) {
        // Close the statement before returning
        $stmt->close();
        return ['success' => true, 'message' => "Transaction cancelled successfully."];
    } else {
        // Close the statement before returning
        $stmt->close();
        return ['success' => false, 'message' => "No transaction was updated. Please check the transaction ID."];
    }
}


function logout()
{

    global $mysqli;

    // Prepare the SQL statement to update the transaction status
    $query = "UPDATE users SET status = 0 WHERE id = ?";

    // Prepare the statement
    $stmt = $mysqli->prepare($query);
    $id = $_SESSION['id'];

    // Check if the statement was prepared successfully
    if (!$stmt) {
        return ['success' => false, 'message' => "Error preparing statement: " . $mysqli->error];
    }

    // Bind the transaction ID to the parameter
    $stmt->bind_param("i", $id);

    // Execute the statement
    if (!$stmt->execute()) {
        // Close the statement before returning
        $stmt->close();
        return ['success' => false, 'message' => "Error executing statement: " . $stmt->error];
    }

    // Check if any rows were updated
    if ($stmt->affected_rows > 0) {
        // Close the statement before returning
        $stmt->close();
        return ['success' => true, 'message' => "Logged out successfully."];
    } else {
        // Close the statement before returning
        $stmt->close();
        return ['success' => false, 'message' => "Unable to peform logout operation"];
    }
}

function getCompletedTransactionsByDriver()
{
    global $mysqli;
    $id = $_SESSION["id"];

    $query = "
SELECT * FROM transactions
WHERE status = 'completed' AND driver = ?
";

    $transactions = [];

    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }

        $stmt->close();

        return ['success' => true, 'data' => $transactions];
    } else {
        return ['success' => false, 'message' => "Error preparing query: " . $mysqli->error];
    }
}
