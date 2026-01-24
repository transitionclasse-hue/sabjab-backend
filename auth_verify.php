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

$phone = $input["phone"] ?? null;
$otp   = $input["otp"] ?? null;

if (!$phone) {
    echo json_encode(["status" => "error", "message" => "Phone required"]);
    exit;
}

// ---------------- DEV MODE OTP CHECK ----------------
// In dev mode accept any OTP
if (!$otp) {
    echo json_encode(["status" => "error", "message" => "OTP required"]);
    exit;
}

// ---------------- FIND OR CREATE USER ----------------
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $stmt = $pdo->prepare("INSERT INTO users (phone) VALUES (?) RETURNING *");
        $stmt->execute([$phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "User DB error"]);
    exit;
}

// ---------------- CREATE SESSION TOKEN ----------------
$sessionToken = bin2hex(random_bytes(24)); // 48 char secure token

// ---------------- SAVE SESSION IN SUPABASE ----------------
try {
    // Optional: delete old sessions for this user
    $stmt = $pdo->prepare("DELETE FROM sessions WHERE user_id = ?");
    $stmt->execute([$user["id"]]);

    // Insert new session
    $stmt = $pdo->prepare("INSERT INTO sessions (user_id, session) VALUES (?, ?)");
    $stmt->execute([$user["id"], $sessionToken]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to create session",
        "details" => $e->getMessage()
    ]);
    exit;
}

// ---------------- RESPONSE ----------------
echo json_encode([
    "status" => "success",
    "user" => $user,
    "session" => $sessionToken
]);
