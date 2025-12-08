<?php
// CONFIRMATION TEXT (remove later if you want)
echo "get_products.php → NEW VERSION LOADED SUCCESSFULLY<br>";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "db_config.php";

// OPTIONAL category filter
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Build query
if ($category_id > 0) {
    $query = "
        SELECT 
            id, 
            name, 
            price, 
            sale_price, 
            image, 
            category_id, 
            stock_qty, 
            short_description
        FROM products 
        WHERE category_id = $category_id
        ORDER BY id DESC
    ";
} else {
    $query = "
        SELECT 
            id, 
            name, 
            price, 
            sale_price, 
            image, 
            category_id, 
            stock_qty, 
            short_description
        FROM products
        ORDER BY id DESC
    ";
}

// Run query
$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode([
        "status" => "error",
        "message" => mysqli_error($conn)
    ]);
    exit;
}

$products = [];

// Format rows
while ($row = mysqli_fetch_assoc($result)) {
    $row['price'] = floatval($row['price']);
    $row['sale_price'] = floatval($row['sale_price']);
    $row['stock_qty'] = intval($row['stock_qty']);
    $products[] = $row;
}

// Output result
echo json_encode([
    "status" => "success",
    "count" => count($products),
    "data" => $products
]);

mysqli_close($conn);
?>
