<?php
header("Content-Type: application/json");
require_once "db_config.php";

$sql = "SELECT id, name, image FROM categories ORDER BY id ASC";
$result = mysqli_query($conn, $sql);

$categories = [];

while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = [
        "id" => (int)$row["id"],
        "name" => $row["name"],
        "image" => $row["image"]
    ];
}

echo json_encode([
    "success" => true,
    "data" => $categories
]);
