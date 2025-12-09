<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "db_config.php";

// IMPORTANT: Replace this with actual authentication/session logic
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 1; 
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid order ID"]);
    exit;
}

// ----------------------------------------------------
// 1. Fetch main order details
// ----------------------------------------------------
$order_query = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $order_query);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$order_result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    echo json_encode(["status" => "error", "message" => "Order not found or access denied."]);
    exit;
}

// 2. Fetch linked address details
$address_query = "SELECT * FROM addresses WHERE id = ?";
$stmt = mysqli_prepare($conn, $address_query);
mysqli_stmt_bind_param($stmt, "i", $order['address_id']);
mysqli_stmt_execute($stmt);
$address_result = mysqli_stmt_get_result($stmt);
$address = mysqli_fetch_assoc($address_result);

// 3. Fetch all order items (products)
$items_query = "
    SELECT 
        oi.product_name, 
        oi.qty, 
        oi.price_at_purchase,
        p.image_url 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
";
$stmt = mysqli_prepare($conn, $items_query);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);

$items = [];
while ($row = mysqli_fetch_assoc($items_result)) {
    $row['price_at_purchase'] = floatval($row['price_at_purchase']);
    $items[] = $row;
}

// 4. Consolidate and prepare final JSON response
$final_order_data = $order;
$final_order_data['address_details'] = $address;
$final_order_data['items'] = $items;

// Ensure main order numeric fields are cast
$final_order_data['order_total'] = floatval($final_order_data['order_total']);
$final_order_data['final_amount'] = floatval($final_order_data['final_amount']);


echo json_encode(["status" => "success", "data" => $final_order_data]);

mysqli_close($conn);
?>
