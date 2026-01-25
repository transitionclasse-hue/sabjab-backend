<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://snack.expo.dev");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

session_start();

require_once __DIR__ . "/db_config.php";

// ---------------- CHECK LOGIN ----------------
if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Not logged in"
    ]);
    exit;
}

$user_id = (int) $_SESSION["user_id"];

// ---------------- READ INPUT ----------------
$input = json_decode(file_get_contents("php://input"), true);

// ---------------- VALIDATE INPUT ----------------
if (!isset($input["product_id"]) || !isset($input["qty"])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing product_id or qty"
    ]);
    exit;
}

$product_id = (int) $input["product_id"];
$qty = (int) $input["qty"];

if ($product_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid product"
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

// ---------------- INSERT OR UPDATE ----------------
// PostgreSQL UPSERT
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
