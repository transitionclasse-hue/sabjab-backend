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
    echo json_encode([
        "success" => false,
        "items" => [],
        "message" => "Session required"
    ]);
    exit;
}

$session = $input["session"];

// ------------------ GET USER FROM SESSION ------------------
try {
    $stmt = $pdo->prepare("SELECT user_id FROM sessions WHERE session = ?");
    $stmt->execute([$session]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode([
            "success" => false,
            "items" => [],
            "message" => "Invalid session"
        ]);
        exit;
    }

    $user_id = (int)$row["user_id"];
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "items" => [],
        "message" => "Session lookup failed"
    ]);
    exit;
}

// ------------------ LOAD CART ------------------
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
