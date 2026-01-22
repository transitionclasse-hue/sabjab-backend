<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "db_config.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// IMPORTANT: Replace this with actual authentication/session logic
$user_id = 1; 

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        echo json_encode(["status" => "error", "message" => "Invalid JSON input"]);
        exit;
    }
    
    // Basic order data validation
    if (
        empty($data['address_id']) || 
        empty($data['final_amount']) || 
        empty($data['payment_method']) || 
        empty($data['cart_items'])
    ) {
        echo json_encode(["status" => "error", "message" => "Missing order details"]);
        exit;
    }

    $address_id = intval($data['address_id']);
    $order_total = floatval($data['order_total']);
    $discount_amount = floatval($data['discount_amount']);
    $final_amount = floatval($data['final_amount']);
    $payment_method = $data['payment_method'];

    mysqli_begin_transaction($conn);
    
    try {
        // 1. Insert into orders table
        $order_query = "
            INSERT INTO orders 
            (user_id, address_id, order_total, discount_amount, final_amount, payment_method, order_status) 
            VALUES (?, ?, ?, ?, ?, ?, 'Processing')
        ";
        $stmt = mysqli_prepare($conn, $order_query);
        mysqli_stmt_bind_param(
            $stmt, 
            "iiddds", 
            $user_id, 
            $address_id, 
            $order_total, 
            $discount_amount, 
            $final_amount, 
            $payment_method
        );
        mysqli_stmt_execute($stmt);

        $order_id = mysqli_insert_id($conn);

        // 2. Insert into order_items table
        $item_query = "
            INSERT INTO order_items 
            (order_id, product_id, product_name, price_at_purchase, qty) 
            VALUES (?, ?, ?, ?, ?)
        ";
        $stmt_item = mysqli_prepare($conn, $item_query);
        
        foreach ($data['cart_items'] as $item) {
            mysqli_stmt_bind_param(
                $stmt_item, 
                "iisdi", 
                $order_id, 
                intval($item['id']), 
                $item['name'], 
                floatval($item['price']), 
                intval($item['qty'])
            );
            mysqli_stmt_execute($stmt_item);
        }

        // 3. Clear the user's cart
        $clear_query = "DELETE FROM cart WHERE user_id = ?";
        $stmt_clear = mysqli_prepare($conn, $clear_query);
        mysqli_stmt_bind_param($stmt_clear, "i", $user_id);
        mysqli_stmt_execute($stmt_clear);

        mysqli_commit($conn);
        
        echo json_encode([
            "status" => "success", 
            "message" => "Order placed successfully", 
            "order_id" => $order_id
        ]);

    } catch (Throwable $e) {
        mysqli_rollback($conn);
        echo json_encode([
            "status" => "error", 
            "message" => "Order failed: " . $e->getMessage()
        ]);
    }

} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
}

mysqli_close($conn);
?>
