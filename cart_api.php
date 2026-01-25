<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/db_config.php";

// ---------------- READ INPUT ----------------
$input = json_decode(file_get_contents("php://input"), true);

// ---------------- VALIDATE INPUT ----------------
if (!isset($input["user_id"]) || !isset($input["product_id"]) || !isset($input["qty"])) {
    echo json_encode([
        "success" => false,
        "message" => "user_id, product_id and qty required"
    ]);
    exit;
}

$user_id = (int) $input["user_id"];
$product_id = (int) $input["product_id"];
$qty = (int) $input["qty"];

if ($user_id <= 0 || $product_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid user or product"
    ]);
    exit;
}

// ---------------- REMOVE ITEM ----------------
if ($qty <= 0) {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);

    echo json_encode([
        "success" => true,
        "message" => "Item removed"
    ]);
    exit;
}

// ---------------- INSERT OR UPDATE (POSTGRES UPSERT) ----------------
$stmt = $pdo->prepare("
    INSERT INTO cart (user_id, product_id, qty)
    VALUES (?, ?, ?)
    ON CONFLICT (user_id, product_id)
    DO UPDATE SET qty = EXCLUDED.qty
");

$stmt->execute([$user_id, $product_id, $qty]);

echo json_encode([
    "success" => true,
    "message" => "Cart updated"
]);
