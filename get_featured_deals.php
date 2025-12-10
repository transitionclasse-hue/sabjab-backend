<?php
// Set response header to JSON
header('Content-Type: application/json');

// --- 1. INCLUDE DATABASE CONFIG (Creates $conn - MySQLi object) ---
require_once 'db_config.php';

$response = [
    'status' => 'success',
    'bigDeal' => null,
    'miniDeals' => [],
];

// CRITICAL CHECK: Ensure $conn object exists and is connected
if (!isset($conn) || $conn->connect_error) {
    $response['status'] = 'error';
    $response['message'] = 'Database connection failed. Please check db_config.php.';
    echo json_encode($response);
    exit;
}

try {
    // --- 2. FETCH THE BIG DEAL (MySQLi Query) ---
    $sql_big = "
        SELECT
            p.id,
            p.name AS title,
            p.regular_price AS oldPrice,
            p.sale_price AS price,
            p.short_description AS subtitle,
            p.image_url AS image
        FROM products p
        WHERE p.is_big_deal = 1 AND p.stock_status = 'instock'
        ORDER BY p.sale_price ASC
        LIMIT 1
    ";

    $result_big = mysqli_query($conn, $sql_big);
    $bigDeal = mysqli_fetch_assoc($result_big);

    if ($bigDeal) {
        $bigDeal['oldPrice'] = (float)$bigDeal['oldPrice'];
        $bigDeal['price'] = (float)$bigDeal['price'];
        $response['bigDeal'] = $bigDeal;
    }


    // --- 3. FETCH MINI DEALS (MySQLi Query) ---
    $sql_mini = "
        SELECT
            p.id,
            p.name AS title,
            -- Calculate discount percentage for the badge
            CONCAT(FLOOR((p.regular_price - p.sale_price) / p.regular_price * 100), '% OFF') AS discount,
            p.image_url AS image
        FROM products p
        WHERE p.is_mini_deal = 1 AND p.stock_status = 'instock'
        LIMIT 4
    ";

    $result_mini = mysqli_query($conn, $sql_mini);
    $miniDeals = [];
    while ($row = mysqli_fetch_assoc($result_mini)) {
        $miniDeals[] = $row;
    }
    $response['miniDeals'] = $miniDeals;


    // --- 4. Fallback/Simulated Data (Optional) ---
    if (empty($response['bigDeal']) && empty($response['miniDeals'])) {
         $response['bigDeal'] = [
             "id" => "P999", "title" => "Default Deal Title", "oldPrice" => 500.0, "price" => 250.0,
             "subtitle" => "Placeholder Deal", "image" => "https://via.placeholder.com/150",
         ];
    }

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'Database query failed in try block.';
    error_log("Query Exception: " . $e->getMessage());
}

// Close the connection
if (isset($conn)) {
    mysqli_close($conn);
}

echo json_encode($response);
?>
