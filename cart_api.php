<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/db_config.php";

// ------------------ READ INPUT ------------------
$input = json_decode(file_get_contents("php://input"), true);

// ------------------ SESSION CHECK ------------------
if (!isset($input["session"])) {
    echo json_encode(["success" => false, "message" => "Session required"]);
    exit;
}

$session = $input["session"];

// ------------------ GET USER FROM SESSION ------------------
try {
    $stmt = $pdo->prepare("SELECT user_id FROM sessions WHERE session = ?");
    $stmt->execute([$session]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(["success" => false, "message" => "Invalid session"]);
        exit;
    }

    $user_id = (int)$row["user_id"];
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Session lookup failed"]);
    exit;
}

// ------------------ CLEAR FULL CART ------------------
if (!isset($input["product_id"])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);

        echo json_encode(["success" => true, "message" => "Cart cleared"]);
        exit;
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Failed to clear cart"]);
        exit;
    }
}

// ------------------ VALIDATE PARAMETERS ------------------
if (!isset($input["product_id"]) || !isset($input["qty"])) {
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
    exit;
}

$product_id = (int)$input["product_id"];
$qty = (int)$input["qty"];

if ($product_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid product"]);
    exit;
}

// ------------------ REMOVE ITEM ------------------
if ($qty <= 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);

        echo json_encode(["success" => true, "message" => "Item removed"]);
        exit;
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Failed to remove item"]);
        exit;
    }
}

// ------------------ INSERT OR UPDATE ------------------
try {
    $stmt = $pdo->prepare("
        INSERT INTO cart (user_id, product_id, qty)
        VALUES (?, ?, ?)
        ON CONFLICT (user_id, product_id)
        DO UPDATE SET qty = EXCLUDED.qty
    ");
    $stmt->execute([$user_id, $product_id, $qty]);

    echo json_encode(["success" => true, "message" => "Cart updated"]);
    exit;
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Failed to update cart"]);
    exit;
}
