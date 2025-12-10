<?php
// Set response header to JSON
header('Content-Type: application/json');

// --- 1. INCLUDE DATABASE CONFIG (Creates $conn - MySQLi object) ---
require_once 'db_config.php';

$response = [
    'status' => 'success',
    'products' => [], // Will hold the bestsellers list
];

// CRITICAL CHECK: Ensure $conn object exists and is connected
if (!isset($conn) || $conn->connect_error) {
    $response['status'] = 'error';
    $response['message'] = 'Database connection failed. Please check db_config.php and credentials.';
    echo json_encode($response);
    exit;
}

try {
    // --- 2. SQL: Find Bestsellers by Summing Quantity in Order Items ---
    // NOTE: This query assumes bestsellers are based on total quantity sold from order_items table.
    $query = "
        SELECT 
            p.id,
            p.wc_product_id,
            p.name,
            p.slug,
            p.price,
            p.regular_price,
            p.sale_price,
            p.stock_status,
            p.image_url,
            p.short_description,
            p.is_bestseller,
            -- Calculate total quantity sold for sorting
            SUM(oi.qty) AS total_sold
        FROM products p
        INNER JOIN order_items oi ON p.id = oi.product_id
        WHERE p.stock_status = 'instock' AND p.is_bestseller = 1
        GROUP BY p.id
        ORDER BY total_sold DESC
        LIMIT 10
    ";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        $response['status'] = 'error';
        $response['message'] = 'Bestsellers query failed: ' . mysqli_error($conn);
    } else {
        $bestsellers_list = [];
        // Use a while loop to fetch results, ensuring compatibility across all PHP versions
        while ($row = mysqli_fetch_assoc($result)) {
            // Ensure numeric fields are cast correctly for the React Native app
            $row['price'] = (float)$row['price'];
            $row['regular_price'] = (float)$row['regular_price'];
            $row['sale_price'] = (float)$row['sale_price'];
            $row['total_sold'] = (int)$row['total_sold']; // Add quantity sold for reference
            $row['is_bestseller'] = (int)$row['is_bestseller'];
            
            $bestsellers_list[] = $row;
        }
        $response['products'] = $bestsellers_list;
    }

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'An unexpected error occurred during processing.';
    error_log("Bestsellers Exception: " . $e->getMessage());
}

// Close the connection
if (isset($conn)) {
    mysqli_close($conn);
}

echo json_encode($response);
?>
