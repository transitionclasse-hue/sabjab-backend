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
        "status" => "error",
        "message" => "Not logged in"
    ]);
    exit;
}

$user_id = (int) $_SESSION["user_id"];

// ---------------- READ INPUT ----------------
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid JSON input"
    ]);
    exit;
}

// ---------------- VALIDATE INPUT ----------------
if (
    !isset($data["address_id"]) ||
    !isset($data["final_amount"]) ||
    empty($data["payment_method"]) ||
    empty($data["cart_items"])
) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing order details"
    ]);
    exit;
}

$address_id = (int) $data["address_id"];
$order_total = (float) ($data["order_total"] ?? 0);
$discount_amount = (float) ($data["discount_amount"] ?? 0);
$delivery_fee = (float) ($data["delivery_fee"] ?? 0);
$final_amount = (float) $data["final_amount"];
$payment_method = (string) $data["payment_method"];

$customer_name = trim($data["customer_name"] ?? "");
$customer_phone = trim($data["customer_phone"] ?? "");

// ---------------- START TRANSACTION ----------------
try {
    $pdo->beginTransaction();

    // 1️⃣ Insert order
    $stmt = $pdo->prepare("
        INSERT INTO orders 
        (user_id, address_id, order_total, discount_amount, delivery_fee, final_amount, payment_method, order_status, customer_name, customer_phone) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Processing', ?, ?)
        RETURNING id
    ");

    $stmt->execute([
        $user_id,
        $address_id,
        $order_total,
        $discount_amount,
        $delivery_fee,
        $final_amount,
        $payment_method,
        $customer_name,
        $customer_phone
    ]);

    $order_id = $stmt->fetchColumn();

    if (!$order_id) {
        throw new Exception("Failed to create order");
    }

    // 2️⃣ Insert order items
    $stmtItem = $pdo->prepare("
        INSERT INTO order_items 
        (order_id, product_id, price_at_purchase, qty) 
        VALUES (?, ?, ?, ?)
    ");

    foreach ($data["cart_items"] as $item) {
        $product_id = (int) $item["product_id"];
        $price = (float) $item["price"];
        $qty = (int) $item["qty"];

        if ($product_id <= 0 || $qty <= 0) {
            throw new Exception("Invalid cart item");
        }

        $stmtItem->execute([
            $order_id,
            $product_id,
            $price,
            $qty
        ]);
    }

    // 3️⃣ Clear cart
    $stmtClear = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmtClear->execute([$user_id]);

    // ✅ COMMIT
    $pdo->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Order placed successfully",
        "order_id" => $order_id
    ]);
    exit;

} catch (Exception $e) {
    // ❌ ROLLBACK
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        "status" => "error",
        "message" => "Checkout failed",
        "error" => $e->getMessage()
    ]);
    exit;
}
