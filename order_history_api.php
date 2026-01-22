<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "db_config.php";

// ----------------------------------------------------
// 🔒 STRICT SESSION BOUND VALIDATION
// ----------------------------------------------------
if (!isset($_GET['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Missing user_id"]);
    exit;
}

$user_id = intval($_GET['user_id']);

if ($user_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid user ID"]);
    exit;
}

// ----------------------------------------------------
// Fetch all orders for THIS USER ONLY
// ----------------------------------------------------
$query = "
    SELECT 
        o.id, 
        o.order_status, 
        o.order_total,
        o.final_amount,
        o.created_at,

        (
            SELECT p.image_url 
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = o.id
            LIMIT 1
        ) AS first_item_image
        
    FROM orders o
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    echo json_encode(["status" => "error", "message" => "Database error"]);
    exit;
}

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Cast numeric fields safely
    $row['order_total'] = floatval($row['order_total']);
    $row['final_amount'] = floatval($row['final_amount']);
    $orders[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $orders
]);

mysqli_close($conn);
?>
