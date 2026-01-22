<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once "db_config.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$input = json_decode(file_get_contents("php://input"), true);

// If frontend sends only user_id => CLEAR FULL CART
if (isset($input["user_id"]) && !isset($input["product_id"])) {

    $user_id = (int)$input["user_id"];

    if ($user_id <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid user"]);
        exit;
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    echo json_encode(["success" => true, "message" => "Cart cleared"]);
    exit;
}

// Normal add/update/remove logic
if (
    !isset($input["user_id"]) ||
    !isset($input["product_id"]) ||
    !isset($input["qty"])
) {
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
    exit;
}

$user_id = (int)$input["user_id"];
$product_id = (int)$input["product_id"];
$qty = (int)$input["qty"];

if ($user_id <= 0 || $product_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid user or product"]);
    exit;
}

// Remove item
if ($qty <= 0) {
    $stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);

    echo json_encode(["success" => true, "message" => "Item removed"]);
    exit;
}

// Insert or update item
$stmt = mysqli_prepare($conn, "
    INSERT INTO cart (user_id, product_id, qty)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE qty = VALUES(qty)
");

mysqli_stmt_bind_param($stmt, "iii", $user_id, $product_id, $qty);
mysqli_stmt_execute($stmt);

echo json_encode(["success" => true, "message" => "Cart updated"]);
