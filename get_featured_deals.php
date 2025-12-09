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
        WHERE p.is_big_deal = 1 AND p.status = 'publish'
        ORDER BY p.sale_price ASC 
        LIMIT 1
    ";
    $stmt_big = $pdo->query($sql_big);
    $bigDeal = $stmt_big->fetch(PDO::FETCH_ASSOC);

    if ($bigDeal) {
        // Ensure prices are converted to float/numeric if stored as strings
        $bigDeal['oldPrice'] = (float)$bigDeal['oldPrice'];
        $bigDeal['price'] = (float)$bigDeal['price'];
        $response['bigDeal'] = $bigDeal;
    }


    // --- 3. FETCH MINI DEALS (4 PRODUCTS/CATEGORIES) ---
    // Assumes 'products' table has column: is_mini_deal
    $sql_mini = "
        SELECT 
            p.id,
            p.name AS title, 
            -- Calculate discount percentage for the badge
            CONCAT(FLOOR((p.regular_price - p.sale_price) / p.regular_price * 100), '% OFF') AS discount, 
            p.image_url AS image 
        FROM products p
        WHERE p.is_mini_deal = 1 AND p.status = 'publish'
        LIMIT 4
    ";
    $stmt_mini = $pdo->query($sql_mini);
    $response['miniDeals'] = $stmt_mini->fetchAll(PDO::FETCH_ASSOC);
    
    // Fallback/Simulated Data if no real deals are found (Optional)
    if (empty($response['bigDeal']) && empty($response['miniDeals'])) {
         $response['bigDeal'] = [
            "id" => "P999", "title" => "Default Deal Title", "oldPrice" => 500.0, "price" => 250.0,
            "subtitle" => "Placeholder Deal", "image" => "https://via.placeholder.com/150",
         ];
         // Add 4 mini deal placeholders here if needed
    }

} catch (PDOException $e) {
    // Return an error response
    $response['status'] = 'error';
    $response['message'] = 'Database query failed.';
    // Log the error detail internally: error_log($e->getMessage()); 
}

echo json_encode($response);
?>
