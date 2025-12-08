<?php
include 'db_config.php';

$sub_id = $_GET['subcategory_id'];

$sql = "SELECT id, name, image_url as image, price
        FROM products
        WHERE subcategory_id = $sub_id";

$res = mysqli_query($conn, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($res)) { $data[] = $row; }

echo json_encode($data);
?>
