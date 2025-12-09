<?php
// Set headers for CORS and JSON response
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'db_config.php';

// Get the subcategory ID, ensuring it's an integer
$sub_id = isset($_GET['subcategory_id']) ? intval($_GET['subcategory_id']) : 0;

if ($sub_id <= 0) {
    // Handle missing ID gracefully
    echo json_encode(["data" => []]);
    exit;
}

// SQL query filtering by the correct column (subcategory_id)
$sql = "SELECT id, name, image_url, price, regular_price, sale_price, stock_status
        FROM products
        WHERE subcategory_id = $sub_id";

$res = mysqli_query($conn, $sql);

if (!$res) {
    // Error handling
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
    exit;
}

$products = [];
while ($row = mysqli_fetch_assoc($res)) { 
    // Convert price and stock fields to correct types for consistency
    $row['price'] = floatval($row['price']);
    $row['regular_price'] = floatval($row['regular_price']);
    $row['sale_price'] = floatval($row['sale_price']);
    
    // The frontend SubCategoryScreen.js expects a key named 'image' for its left pane 
    // and 'image_url' for the right product list.
    $row['image'] = $row['image_url']; 

    $products[] = $row;
}

// CRITICAL FIX: Wrap the data in the "data" key as expected by the frontend
echo json_encode([
    "status" => "success", 
    "count" => count($products), 
    "data" => $products
]);

mysqli_close($conn);
?>
