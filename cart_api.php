<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "db_config.php";

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input["user_id"]) || !isset($input["product_id"]) || !isset($input["qty"])) {
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
    exit;
}

$user_id = intval($input["user_id"]);
$product_id = intval($input["product_id"]);
$qty = intval($input["qty"]);

if ($user_id <= 0 || $product_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid user or product"]);
    exit;
}

if ($qty <= 0) {
    // Remove item
    $stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo json_encode(["success" => true, "message" => "Item removed"]);
    exit;
}

// Insert or Update (UPSERT)
$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO cart (user_id, product_id, qty)
     VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE qty = VALUES(qty)"
);

mysqli_stmt_bind_param($stmt, "iii", $user_id, $product_id, $qty);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($ok) {
    echo json_encode(["success" => true, "message" => "Cart updated"]);
} else {
    echo json_encode(["success" => false, "message" => "Database error"]);
}

mysqli_close($conn);
