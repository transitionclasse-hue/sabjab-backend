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

ini_set("session.cookie_samesite", "None");
ini_set("session.cookie_secure", "1");
session_start();

require_once __DIR__ . "/db_config.php";

// ---------------- READ INPUT ----------------
$input = json_decode(file_get_contents("php://input"), true);

$phone = $input["phone"] ?? null;
$otp   = $input["otp"] ?? null;

if (!$phone) {
    echo json_encode(["status" => "error", "message" => "Phone required"]);
    exit;
}

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

// ---------------- CREATE PHP SESSION ----------------
$_SESSION["user_id"] = $user["id"];

// ---------------- RESPONSE ----------------
echo json_encode([
    "status" => "success",
    "user" => $user
]);
