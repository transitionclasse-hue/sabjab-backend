<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "db_config.php";

// IMPORTANT: Replace this with actual authentication/session logic
$user_id = 1; 

$method = $_SERVER['REQUEST_METHOD'];

// Handle GET request (Fetch Cart)
if ($method === 'GET') {
    $query = "
        SELECT 
            c.product_id AS id, 
            c.qty, 
            p.name, 
            p.price, 
            p.image_url
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $cart_items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['price'] = floatval($row['price']);
        $cart_items[] = $row;
    }
    
    echo json_encode(["status" => "success", "data" => $cart_items]);

} 
// Handle POST request (Add/Update/Remove Item)
else if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $product_id = intval($data['product_id']);
    $qty = intval($data['qty']);

    if ($product_id <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid product ID"]);
        exit;
    }

    if ($qty <= 0) {
        // Remove item if quantity is zero or less
        $query = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt);
    } else {
        // Insert or update item (UPSERT logic)
        $query = "
            INSERT INTO cart (user_id, product_id, qty) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE qty = VALUES(qty)
        ";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iii", $user_id, $product_id, $qty);
        mysqli_stmt_execute($stmt);
    }
    
    echo json_encode(["status" => "success", "message" => "Cart updated"]);

} 
else {
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
}

mysqli_close($conn);
?>
