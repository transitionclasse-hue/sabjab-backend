<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "db_config.php";

// IMPORTANT: Replace this with actual authentication/session logic
$user_id = 1; 

$method = $_SERVER['REQUEST_METHOD'];

// Handle GET request (Fetch Addresses)
if ($method === 'GET') {
    $query = "SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $addresses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $addresses[] = $row;
    }
    
    echo json_encode(["status" => "success", "data" => $addresses]);

} 
// Handle POST request (Save New Address)
else if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Basic validation
    if (empty($data['full_name']) || empty($data['pincode']) || empty($data['address_line_1'])) {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit;
    }

    // Set all other addresses to non-default if this one is set as default
    if (isset($data['is_default']) && $data['is_default']) {
        $default_query = "UPDATE addresses SET is_default = 0 WHERE user_id = ?";
        $default_stmt = mysqli_prepare($conn, $default_query);
        mysqli_stmt_bind_param($default_stmt, "i", $user_id);
        mysqli_stmt_execute($default_stmt);
    }

    $query = "
        INSERT INTO addresses (user_id, full_name, phone_number, pincode, address_line_1, address_line_2, city, state, is_default) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "isssssssi", 
        $user_id, 
        $data['full_name'], 
        $data['phone_number'], 
        $data['pincode'], 
        $data['address_line_1'], 
        $data['address_line_2'], 
        $data['city'], 
        $data['state'], 
        $data['is_default']
    );
    mysqli_stmt_execute($stmt);
    
    echo json_encode(["status" => "success", "message" => "Address saved", "id" => mysqli_insert_id($conn)]);

} 
else {
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
}

mysqli_close($conn);
?>
