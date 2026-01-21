<?php
header("Content-Type: application/json");
require_once "db_config.php";

$sql = "SELECT id, name, image_url FROM categories WHERE is_active = 1 ORDER BY display_order ASC";
$result = mysqli_query($conn, $sql);

$categories = [];

while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = [
        "id" => (int)$row["id"],
        "name" => $row["name"],
        "image" => $row["image_url"]
    ];
}

echo json_encode([
    "success" => true,
    "data" => $categories
]);
