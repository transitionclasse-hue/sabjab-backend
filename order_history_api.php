<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");

ini_set("session.cookie_samesite", "None");
ini_set("session.cookie_secure", "1");
session_start();

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

$user_id = (int) $_SESSION["user_id"];

require_once __DIR__ . "/db_config.php";

try {
    // ---------------- LOAD ORDER HISTORY (ONLY THIS USER) ----------------
    $stmt = $pdo->prepare("
        SELECT 
            o.id,
            o.order_status,
            o.order_total,
            o.final_amount,
            o.created_at,
            (
                SELECT p.image_url
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = o.id
                LIMIT 1
            ) AS first_item_image
        FROM orders o
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC
    ");

    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ---------------- CAST NUMBERS ----------------
    foreach ($orders as &$o) {
        $o["order_total"] = (float) $o["order_total"];
        $o["final_amount"] = (float) $o["final_amount"];
    }

    echo json_encode([
        "status" => "success",
        "data" => $orders
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to load order history",
        "error" => $e->getMessage()
    ]);
    exit;
}
