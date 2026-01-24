<?php

error_reporting(0);
ini_set("display_errors", 0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once "db_config.php";

$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

if (!isset($input["phone"])) {
    echo json_encode(["success" => false, "message" => "Missing phone"]);
    exit;
}

$phone = trim($input["phone"]);

if (strlen($phone) < 8) {
    echo json_encode(["success" => false, "message" => "Invalid phone"]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, phone, name FROM users WHERE phone = ?");
$stmt->execute([$phone]);
$user = $stmt->fetch();

if ($user) {
    echo json_encode([
        "success" => true,
        "user" => $user
    ]);
    exit;
}

$name = "User " . substr($phone, -4);

$stmt = $pdo->prepare("INSERT INTO users (phone, name) VALUES (?, ?) RETURNING id");
$stmt->execute([$phone, $name]);
$newId = $stmt->fetchColumn();

echo json_encode([
    "success" => true,
    "user" => [
        "id" => $newId,
        "phone" => $phone,
        "name" => $name
    ]
]);
