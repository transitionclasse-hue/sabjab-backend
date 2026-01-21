<?php
header("Content-Type: application/json");
require_once "db_config.php";

// Fetch only what app needs
$sql = "SELECT id, name, price, image_url FROM products ORDER BY id DESC LIMIT 50";
$result = mysqli_query($conn, $sql);

$products = [];

while ($row = mysqli_fetch_assoc($result)) {
    $products[] = [
        "id" => (int)$row["id"],
        "name" => $row["name"],
        "price" => (float)$row["price"],
        "image" => $row["image_url"]
    ];
}

echo json_encode([
    "success" => true,
    "data" => $products
]);
