<?php
include "db_config.php";
header("Content-Type: application/json; charset=UTF-8");

$sql = "SELECT id, name, image_url, category_ids, display_order
        FROM supercategories
        WHERE is_active = 1
        ORDER BY display_order ASC";

$result = $conn->query($sql);
$rows = [];

while ($row = $result->fetch_assoc()) {
    $rows[] = [
        "id" => (int)$row["id"],
        "name" => $row["name"],
        "image_url" => $row["image_url"],
        "category_ids" => $row["category_ids"] ? explode(",", $row["category_ids"]) : [],
        "display_order" => (int)$row["display_order"]
    ];
}

echo json_encode(["success"=>true,"data"=>$rows]);
?>