<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once "db_config.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_GET["user_id"])) {
    echo json_encode(["success" => false, "items" => [], "message" => "Missing user_id"]);
    exit;
}

$user_id = (int)$_GET["user_id"];

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
    p.price,
    p.image_url AS image
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
    $items[] = $row;
}

echo json_encode([
    "success" => true,
    "items" => $items
]);
