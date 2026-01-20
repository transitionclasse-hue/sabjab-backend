<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once "db_config.php";

$user_id = $_GET['user_id'] ?? 0;

if (!$user_id) {
    echo json_encode(["success" => false, "items" => []]);
    exit;
}

// Join with your products table (adjust table name if needed)
$sql = "
SELECT 
  c.id as cart_id,
  c.product_id,
  c.qty,
  p.name,
  p.price,
  p.image
FROM cart c
JOIN products p ON p.id = c.product_id
WHERE c.user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$items = [];
$total = 0;

while ($row = $res->fetch_assoc()) {
    $row['subtotal'] = $row['price'] * $row['qty'];
    $total += $row['subtotal'];
    $items[] = $row;
}

echo json_encode([
  "success" => true,
  "items" => $items,
  "total" => $total
]);
