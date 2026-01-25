<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . "/db_config.php";

// ---------------- VALIDATE INPUT ----------------
if (!isset($_GET["user_id"])) {
    echo json_encode([
        "success" => false,
        "items" => [],
        "message" => "user_id required"
    ]);
    exit;
}

$user_id = (int) $_GET["user_id"];

if ($user_id <= 0) {
    echo json_encode([
        "success" => false,
        "items" => [],
        "message" => "Invalid user_id"
    ]);
    exit;
}

// ---------------- LOAD CART ----------------
try {
    $sql = "
        SELECT
            c.id,
            c.product_id,
            c.qty,
            p.name,
            p.price,
            p.image
        FROM cart c
        JOIN products p ON p.id = c.product_id
        WHERE c.user_id = ?
        ORDER BY c.id DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "items" => $items
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "items" => [],
        "message" => "Failed to load cart",
        "error" => $e->getMessage()
    ]);
    exit;
}
