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

// ---------------- READ INPUT ----------------
$order_id = isset($_GET["order_id"]) ? (int) $_GET["order_id"] : 0;

if ($order_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid order id"]);
    exit;
}

try {
    // ---------------- 1. LOAD ORDER (LOCKED TO USER) ----------------
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(["status" => "error", "message" => "Order not found"]);
        exit;
    }

    // ---------------- 2. LOAD ADDRESS ----------------
    $stmt = $pdo->prepare("SELECT * FROM addresses WHERE id = ?");
    $stmt->execute([$order["address_id"]]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);

    // ---------------- 3. LOAD ITEMS ----------------
    $stmt = $pdo->prepare("
        SELECT 
            oi.product_id,
            oi.product_name,
            oi.qty,
            oi.price_at_purchase,
            p.image_url
        FROM order_items oi
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ---------------- CAST NUMBERS ----------------
    $order["order_total"] = (float)$order["order_total"];
    $order["discount_amount"] = (float)$order["discount_amount"];
    $order["final_amount"] = (float)$order["final_amount"];

    foreach ($items as &$it) {
        $it["qty"] = (int)$it["qty"];
        $it["price_at_purchase"] = (float)$it["price_at_purchase"];
    }

    // ---------------- RESPONSE ----------------
    echo json_encode([
        "status" => "success",
        "data" => [
            "order" => $order,
            "address" => $address,
            "items" => $items
        ]
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to load order details",
        "error" => $e->getMessage()
    ]);
    exit;
}
