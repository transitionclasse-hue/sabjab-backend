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

// ================= READ INPUT =================
$input = json_decode(file_get_contents("php://input"), true);

$session_token = $input["session_token"] ?? null;
$product_id = isset($input["product_id"]) ? (int)$input["product_id"] : 0;
$qty = isset($input["qty"]) ? (int)$input["qty"] : 0;

if (!$session_token || $product_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "session_token and product_id required"
    ]);
    exit;
}

if ($qty == 0) {
    echo json_encode([
        "status" => "error",
        "message" => "qty cannot be zero"
    ]);
    exit;
}

// ================= FIND USER FROM SESSION =================
$stmt = $pdo->prepare("SELECT user_id FROM sessions WHERE session_token = :t LIMIT 1");
$stmt->execute(["t" => $session_token]);
$sess = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sess) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid or expired session"
    ]);
    exit;
}

$user_id = (int)$sess["user_id"];

// ================= CHECK PRODUCT EXISTS =================
$stmt = $pdo->prepare("SELECT id FROM products WHERE id = :pid LIMIT 1");
$stmt->execute(["pid" => $product_id]);

if (!$stmt->fetch()) {
    echo json_encode([
        "status" => "error",
        "message" => "Product not found"
    ]);
    exit;
}

// ================= REMOVE ITEM =================
if ($qty < 0) {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :uid AND product_id = :pid");
    $stmt->execute([
        "uid" => $user_id,
        "pid" => $product_id
    ]);

    echo json_encode([
        "status" => "ok",
        "message" => "Item removed"
    ]);
    exit;
}

// ================= UPSERT CART =================
$stmt = $pdo->prepare("
    INSERT INTO cart (user_id, product_id, qty)
    VALUES (:uid, :pid, :qty)
    ON CONFLICT (user_id, product_id)
    DO UPDATE SET qty = EXCLUDED.qty
");

$stmt->execute([
    "uid" => $user_id,
    "pid" => $product_id,
    "qty" => $qty
]);

echo json_encode([
    "status" => "ok",
    "message" => "Cart updated"
]);
