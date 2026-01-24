<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . "/db_config.php";

$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

$phone = $input["phone"] ?? null;

if (!$phone) {
    echo json_encode(["status" => "error", "message" => "Phone required"]);
    exit;
}

// 1. Check if user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE phone = :phone");
$stmt->execute(["phone" => $phone]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. If not, create user
if (!$user) {
    $stmt = $pdo->prepare("INSERT INTO users (phone) VALUES (:phone) RETURNING *");
    $stmt->execute(["phone" => $phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// 3. Start session
session_start();
$_SESSION["user_id"] = $user["id"];

echo json_encode([
    "status" => "success",
    "user" => $user,
    "session" => session_id()
]);
