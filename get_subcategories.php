<?php
include 'db_config.php';

$category_id = $_GET['category_id'] ?? 0;

$response = ["status" => false, "data" => []];

if ($category_id == 0) {
    echo json_encode($response);
    exit;
}

$sql = "SELECT id, name FROM subcategories WHERE category_id = $category_id ORDER BY id ASC";
$res = mysqli_query($conn, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}

$response["status"] = true;
$response["data"] = $data;
echo json_encode($response);
?>
