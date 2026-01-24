<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once "db_config.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_GET["user_id"])) {
    echo json_encode(["success" => false, "items" => [], "message" => "Missing user_id"]);
    exit;
}

$user_id = (int)$_GET["user_id"];

if ($user_id <= 0) {
    echo json_encode(["success" => false, "items" => [], "message" => "Invalid user"]);
    exit;
}

$sql = "
SELECT 
    c.id AS cart_id,
    c.product_id,
    c.qty,
    p.name,
    p.price,
    p.image_url AS image<?php
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
    echo json_encode(["success" => false, "items" => [], "message" => "Session required"]);
    exit;
}

$session = $input["session"];

// ------------------ GET USER FROM SESSION ------------------
try {
    $stmt = $pdo->prepare("SELECT user_id FROM sessions WHERE session = ?");
    $stmt->execute([$session]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(["success" => false, "items" => [], "message" => "Invalid session"]);
        exit;
    }

    $user_id = (int)$row["user_id"];
} catch (Exception $e) {
    echo json_encode(["success" => false, "items" => [], "message" => "Session lookup failed"]);
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

FROM cart c
JOIN products p ON p.id = c.product_id
WHERE c.user_id = ?
ORDER BY c.id DESC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}

echo json_encode([
    "success" => true,
    "items" => $items
]);
