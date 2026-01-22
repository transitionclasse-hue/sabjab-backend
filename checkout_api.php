<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once "db_config.php";

// Throw MySQL errors as exceptions
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// TEMP: until session system is ready
$user_id = 1;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON input"]);
    exit;
}

if (
    !isset($data['address_id']) ||
    !isset($data['final_amount']) ||
    empty($data['payment_method']) ||
    empty($data['cart_items'])
) {
    echo json_encode(["status" => "error", "message" => "Missing order details"]);
    exit;
}

$address_id = (int)$data['address_id'];
$order_total = (float)$data['order_total'];
$discount_amount = (float)$data['discount_amount'];
$final_amount = (float)$data['final_amount'];
$payment_method = (string)$data['payment_method'];

mysqli_begin_transaction($conn);

try {
    // 1. Insert order
    $stmt = mysqli_prepare($conn, "
        INSERT INTO orders 
        (user_id, address_id, order_total, discount_amount, final_amount, payment_method, order_status) 
        VALUES (?, ?, ?, ?, ?, ?, 'Processing')
    ");
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

    // 2. Insert order items
    $stmt_item = mysqli_prepare($conn, "
        INSERT INTO order_items 
        (order_id, product_id, product_name, price_at_purchase, qty) 
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($data['cart_items'] as $item) {

        $product_id = (int)$item['id'];
        $product_name = (string)$item['name'];
        $price_at_purchase = (float)$item['price'];
        $qty = (int)$item['qty'];

        mysqli_stmt_bind_param(
            $stmt_item,
            "iisdi",
            $order_id,
            $product_id,
            $product_name,
            $price_at_purchase,
            $qty
        );

        mysqli_stmt_execute($stmt_item);
    }

    // 3. Clear cart
    $stmt_clear = mysqli_prepare($conn, "DELETE FROM cart WHERE user_id = ?");
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
        "message" => $e->getMessage()
    ]);
}
