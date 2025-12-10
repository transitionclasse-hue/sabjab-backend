<?php
// Set headers for CORS and JSON output
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// --- 1. INCLUDE DATABASE CONFIG ---
// Assumes db_config.php sets up the mysqli connection ($conn)
require_once "db_config.php";

$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Build query
if ($category_id > 0) {

    // Filter using CSV list of categories
    $query = "
        SELECT
            id,
            wc_product_id,
            name,
            slug,
            sku,
            price,
            regular_price,
            sale_price,
            stock_status,
            stock_quantity,
            image_url,
            short_description,
            description,
            is_featured,
            categories_csv,
            updated_at
        FROM products
        WHERE FIND_IN_SET($category_id, categories_csv)
        ORDER BY id DESC
    ";

} else {

    // Get ALL products
    $query = "
        SELECT
            id,
            wc_product_id,
            name,
            slug,
            sku,
            price,
            regular_price,
            sale_price,
            stock_status,
            stock_quantity,
            image_url,
            short_description,
            description,
            is_featured,
            categories_csv,
            updated_at
        FROM products
        ORDER BY id DESC
    ";
}

// Execute the query using the mysqli connection
$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode([
        "status" => "error",
        "message" => "Database query failed: " . mysqli_error($conn)
    ]);
    exit;
}

$products = [];

while ($row = mysqli_fetch_assoc($result)) {
    // Convert all numeric strings to their appropriate types for JSON output
    $row["price"] = floatval($row["price"]);
    $row["regular_price"] = floatval($row["regular_price"]);
    $row["sale_price"] = floatval($row["sale_price"]);
    $row["stock_quantity"] = intval($row["stock_quantity"]);
    $row["is_featured"] = intval($row["is_featured"]);

    $products[] = $row;
}

echo json_encode([
    "status" => "success",
    "count" => count($products),
    "data" => $products
]);

// Ensure $conn is available if the script successfully connects
if (isset($conn)) {
    mysqli_close($conn);
}
?>
