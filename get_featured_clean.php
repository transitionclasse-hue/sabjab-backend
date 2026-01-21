<?php
header("Content-Type: application/json");
require_once "db_config.php";

// Example: take latest discounted products
$sql = "
SELECT id, name, price, image_url
FROM products
WHERE sale_price < regular_price
ORDER BY id DESC
LIMIT 5
";

$result = mysqli_query($conn, $sql);

$items = [];

while ($row = mysqli_fetch_assoc($result)) {
    $items[] = [
        "id" => (int)$row["id"],
        "name" => $row["name"],
        "price" => (float)$row["price"],
        "image" => $row["image_url"]
    ];
}

echo json_encode([
    "success" => true,
    "data" => $items
]);
