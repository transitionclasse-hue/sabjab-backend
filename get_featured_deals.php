<?php
// Set response header to JSON
header('Content-Type: application/json');

// --- 1. INCLUDE DATABASE CONFIG ---
// Assumes db_config.php sets up the PDO connection ($pdo)
require_once 'db_config.php';

$response = [
    'status' => 'success',
    'bigDeal' => null,
    'miniDeals' => [],
];

// CRITICAL CHECK: Ensure $pdo object exists after requiring the config file
if (!isset($pdo) || is_null($pdo)) {
    $response['status'] = 'error';
    $response['message'] = 'Database connection failed. Please check db_config.php.';
    echo json_encode($response);
    exit;
}

try {
    // --- 2. FETCH THE BIG DEAL (ONE PRODUCT) ---
    // Assumes 'products' table has columns: id, name, regular_price, sale_price, short_description, image_url, is_big_deal
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
    
    // NOTE: Removed `p.status = 'publish'` as that column is not in your SQL dump. 
    // Using `p.stock_status = 'instock'` based on your product data.
    
    $stmt_big = $pdo->query($sql_big);
    $bigDeal = $stmt_big->fetch(PDO::FETCH_ASSOC);

    if ($bigDeal) {
        // Ensure prices are converted to float/numeric
        $bigDeal['oldPrice'] = (float)$bigDeal['oldPrice'];
        $bigDeal['price'] = (float)$bigDeal['price'];
        $response['bigDeal'] = $bigDeal;
    }


    // --- 3. FETCH MINI DEALS (4 PRODUCTS/CATEGORIES) ---
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
    
    // NOTE: Removed `p.status = 'publish'` and replaced with `p.stock_status = 'instock'`

    $stmt_mini = $pdo->query($sql_mini);
    $response['miniDeals'] = $stmt_mini->fetchAll(PDO::FETCH_ASSOC);

    // Fallback/Simulated Data if no real deals are found (Optional)
    if (empty($response['bigDeal']) && empty($response['miniDeals'])) {
         $response['bigDeal'] = [
             "id" => "P999", "title" => "Default Deal Title", "oldPrice" => 500.0, "price" => 250.0,
             "subtitle" => "Placeholder Deal", "image" => "https://via.placeholder.com/150",
         ];
         // You can add placeholder mini deals here if you want them to show even if the DB is empty
    }

} catch (PDOException $e) {
    // Return an error response
    $response['status'] = 'error';
    $response['message'] = 'Database query failed in try block. Details hidden.';
    // For debugging, you could show $e->getMessage(), but hide it for production.
}

echo json_encode($response);
?>
