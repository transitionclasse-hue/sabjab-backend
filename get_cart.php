<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once "db_config.php";

if (!isset($_GET["user_id"])) {
    echo json_encode(["success" => false, "items" => [], "message" => "Missing user_id"]);
    exit;
}

$user_id = intval($_GET["user_id"]);

if ($user_id <= 0) {
    echo json_encode(["success" => false, "items" => [], "message" => "Invalid user"]);
    exit;
}

$sql = "
SELECT 
    c.id AS cart_id,
    c.product_id,
    c.qty,
    p.name,
    p.price
FROM cart c
JOIN products p ON p.id = c.product_id
WHERE c.user_id = ?
ORDER BY c.id DESC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Add placeholder image
    $row["image"] = "https://via.placeholder.com/150";
    $items[] = $row;
}

mysqli_stmt_close($stmt);

echo json_encode([
    "success" => true,
    "items" => $items
]);

mysqli_close($conn);
