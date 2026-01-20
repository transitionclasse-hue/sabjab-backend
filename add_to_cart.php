<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once "db_config.php";

$user_id = $_POST['user_id'] ?? 0;
$product_id = $_POST['product_id'] ?? 0;
$qty = $_POST['qty'] ?? 1;

if (!$user_id || !$product_id) {
    echo json_encode(["success" => false]);
    exit;
}

// Check if item already in cart
$stmt = $conn->prepare("SELECT id, qty FROM cart WHERE user_id=? AND product_id=?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    // Update qty
    $newQty = $row['qty'] + $qty;
    $stmt = $conn->prepare("UPDATE cart SET qty=? WHERE id=?");
    $stmt->bind_param("ii", $newQty, $row['id']);
    $stmt->execute();
} else {
    // Insert new
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, qty) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $product_id, $qty);
    $stmt->execute();
}

echo json_encode(["success" => true]);
