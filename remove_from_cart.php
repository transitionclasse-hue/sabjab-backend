<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once "db_config.php";

$cart_id = $_POST['cart_id'] ?? 0;

if (!$cart_id) {
    echo json_encode(["success" => false]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM cart WHERE id=?");
$stmt->bind_param("i", $cart_id);
$stmt->execute();

echo json_encode(["success" => true]);
