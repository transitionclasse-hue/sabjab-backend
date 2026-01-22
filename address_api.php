<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "db_config.php";

$method = $_SERVER['REQUEST_METHOD'];

// ----------------------------------------------------
// 🔒 GET: Fetch addresses for logged-in user
// ----------------------------------------------------
if ($method === 'GET') {

    if (!isset($_GET['user_id'])) {
        echo json_encode(["status" => "error", "message" => "Missing user_id"]);
        exit;
    }

    $user_id = intval($_GET['user_id']);

    if ($user_id <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid user_id"]);
        exit;
    }

    $query = "SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $addresses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $addresses[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "data" => $addresses
    ]);

    exit;
}

// ----------------------------------------------------
// 🔒 POST: Save new address for logged-in user
// ----------------------------------------------------
if ($method === 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || !isset($data['user_id'])) {
        echo json_encode(["status" => "error", "message" => "Missing user_id"]);
        exit;
    }

    $user_id = intval($data['user_id']);

    if ($user_id <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid user_id"]);
        exit;
    }

    // Basic validation
    if (
        empty($data['full_name']) ||
        empty($data['pincode']) ||
        empty($data['address_line_1'])
    ) {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit;
    }

    // If default address, clear previous default
    if (!empty($data['is_default'])) {
        $default_query = "UPDATE addresses SET is_default = 0 WHERE user_id = ?";
        $default_stmt = mysqli_prepare($conn, $default_query);
        mysqli_stmt_bind_param($default_stmt, "i", $user_id);
        mysqli_stmt_execute($default_stmt);
    }

    $query = "
        INSERT INTO addresses 
        (user_id, full_name, phone_number, pincode, address_line_1, address_line_2, city, state, is_default) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = mysqli_prepare($conn, $query);

    mysqli_stmt_bind_param(
        $stmt,
        "isssssssi",
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

    echo json_encode([
        "status" => "success",
        "message" => "Address saved",
        "id" => mysqli_insert_id($conn)
    ]);

    exit;
}

// ----------------------------------------------------
// ❌ INVALID METHOD
// ----------------------------------------------------
http_response_code(405);
echo json_encode(["status" => "error", "message" => "Method not allowed"]);

mysqli_close($conn);
?>
