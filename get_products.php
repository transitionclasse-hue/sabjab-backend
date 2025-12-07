<?php
include "db_config.php";
header("Content-Type: application/json; charset=UTF-8");

$cat = isset($_GET["category_id"]) ? intval($_GET["category_id"]) : 0;

$sql = "SELECT * FROM products WHERE wc_category_id = $cat ORDER BY id DESC";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) { $data[] = $row; }

echo json_encode(["success"=>true, "data"=>$data]);
?>