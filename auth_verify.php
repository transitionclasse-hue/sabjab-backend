<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

error_reporting(0);
ini_set("display_errors", 0);

require_once "db_config.php";

// DO NOT use ob_clean()

$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

if (!isset($input["phone"])) {
    echo json_encode(["success" => false, "message" => "Missing phone"]);
    exit;
}

$phone = trim($input["phone"]);

if (strlen($phone) != 10) {
    echo json_encode(["success" => false, "message" => "Invalid phone number"]);
    exit;
}

// Start session
session_start();

// 1. Check if user exists
$stmt = mysqli_prepare($conn, "SELECT id, phone, name FROM users WHERE phone = ?");
mysqli_stmt_bind_param($stmt, "s", $phone);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {

    $_SESSION["user_id"] = $row["id"];

    echo json_encode([
        "success" => true,
        "user" => $row
    ]);
    exit;
}

mysqli_stmt_close($stmt);

// 2. Create new user
$name = "User " . substr($phone, -4);

$stmt = mysqli_prepare($conn, "INSERT INTO users (phone, name) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, "ss", $phone, $name);
$ok = mysqli_stmt_execute($stmt);

if (!$ok) {
    echo json_encode(["success" => false, "message" => "Failed to create user"]);
    exit;
}

$newUserId = mysqli_insert_id($conn);

// Save session
$_SESSION["user_id"] = $newUserId;

// 3. Return new user
echo json_encode([
    "success" => true,
    "user" => [
        "id" => $newUserId,
        "phone" => $phone,
        "name" => $name
    ]
]);
