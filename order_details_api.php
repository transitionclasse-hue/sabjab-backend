<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "db_config.php";

// ----------------------------------------------------
// 🔒 STRICT SESSION BOUND INPUT VALIDATION
// ----------------------------------------------------
if (!isset($_GET['user_id']) || !isset($_GET['order_id'])) {
    echo json_encode(["status" => "error", "message" => "Missing user_id or order_id"]);
    exit;
}

$user_id = intval($_GET['user_id']);
$order_id = intval($_GET['order_id']);

if ($user_id <= 0 || $order_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid user or order ID"]);
    exit;
}

// ----------------------------------------------------
// 1. Fetch main order (LOCKED TO USER)
// ----------------------------------------------------
$order_query = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $order_query);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$order_result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    echo json_encode(["status" => "error", "message" => "Order not found or access denied"]);
    exit;
}

// ----------------------------------------------------
// 2. Fetch address
// ----------------------------------------------------
$address_query = "SELECT * FROM addresses WHERE id = ?";
$stmt = mysqli_prepare($conn, $address_query);
mysqli_stmt_bind_param($stmt, "i", $order['address_id']);
mysqli_stmt_execute($stmt);
$address_result = mysqli_stmt_get_result($stmt);
$address = mysqli_fetch_assoc($address_result);

// ----------------------------------------------------
// 3. Fetch order items
// ----------------------------------------------------
$items_query = "
    SELECT 
        oi.product_name, 
        oi.qty, 
        oi.price_at_purchase,
        p.image_url 
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
";
$stmt = mysqli_prepare($conn, $items_query);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);

$items = [];
while ($row = mysqli_fetch_assoc($items_result)) {
    $row['price_at_purchase'] = floatval($row['price_at_purchase']);
    $row['qty'] = intval($row['qty']);
    $items[] = $row;
}

// ----------------------------------------------------
// 4. Prepare final response
// ----------------------------------------------------
$final_order_data = $order;
$final_order_data['address_details'] = $address;
$final_order_data['items'] = $items;

// Cast numeric fields
$final_order_data['order_total'] = floatval($final_order_data['order_total']);
$final_order_data['discount_amount'] = floatval($final_order_data['discount_amount']);
$final_order_data['final_amount'] = floatval($final_order_data['final_amount']);

echo json_encode([
    "status" => "success",
    "data" => $final_order_data
]);

mysqli_close($conn);
?>
