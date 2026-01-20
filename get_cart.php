<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once "db_config.php";

$user_id = $_GET['user_id'] ?? 0;

if (!$user_id) {
    echo json_encode(["success" => false, "items" => []]);
    exit;
}

// Fetch only cart rows (NO JOIN)
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$items = [];

while ($row = $res->fetch_assoc()) {
    // TEMP fake product data (so app UI works)
    $row["name"] = "Product #" . $row["product_id"];
    $row["price"] = 100;
    $row["image"] = "https://via.placeholder.com/100";
    $row["subtotal"] = $row["price"] * $row["qty"];
    $items[] = $row;
}

echo json_encode([
  "success" => true,
  "items" => $items
]);
