<?php
include "db_config.php";
header("Content-Type: application/json; charset=UTF-8");

$sql = "SELECT id, wc_category_id, name, image_url FROM categories ORDER BY id ASC";
$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(["success"=>true, "data"=>$data]);
?>