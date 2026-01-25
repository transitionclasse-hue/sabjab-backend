<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://snack.expo.dev");
header("Access-Control-Allow-Credentials: true");

session_start();

require_once __DIR__ . "/db_config.php";

// ---------------- CHECK LOGIN ----------------
if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "items" => [],
        "message" => "Not logged in"
    ]);
    exit;
}

$user_id = (int) $_SESSION["user_id"];

// ---------------- LOAD CART ----------------
try {
    $sql = "
        SELECT
            c.id AS cart_id,
            c.product_id,
            c.qty,
            p.name,
            p.price,
            p.image AS image
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
        "message" => "Failed to load cart"
    ]);
    exit;
}
