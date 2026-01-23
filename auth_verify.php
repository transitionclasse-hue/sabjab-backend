<?php
// =====================================================
// SabJab9 — auth_verify.php (DEBUG VERSION)
// =====================================================

// FORCE PHP TO SHOW ERRORS (TEMP DEBUG)
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

// HEADERS
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// START SESSION
session_start();

// DB CONFIG
require_once "db_config.php";

// READ INPUT
$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

// BASIC VALIDATION
if (!isset($input["phone"])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing phone"
    ]);
    exit;
}

$phone = trim($input["phone"]);

if (strlen($phone) != 10) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid phone number"
    ]);
    exit;
}

// CHECK DB CONNECTION
if (!isset($conn) || !$conn) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

// 1. CHECK IF USER EXISTS
$stmt = mysqli_prepare($conn, "SELECT id, phone, name FROM users WHERE phone = ?");
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Prepare failed: " . mysqli_error($conn)
    ]);
    exit;
}

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

// 2. CREATE NEW USER
$name = "User " . substr($phone, -4);

$stmt = mysqli_prepare($conn, "INSERT INTO users (phone, name) VALUES (?, ?)");
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Prepare insert failed: " . mysqli_error($conn)
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "ss", $phone, $name);
$ok = mysqli_stmt_execute($stmt);

if (!$ok) {
    echo json_encode([
        "success" => false,
        "message" => "Insert failed: " . mysqli_error($conn)
    ]);
    exit;
}

$newUserId = mysqli_insert_id($conn);

// SAVE SESSION
$_SESSION["user_id"] = $newUserId;

// 3. RETURN NEW USER
echo json_encode([
    "success" => true,
    "user" => [
        "id" => $newUserId,
        "phone" => $phone,
        "name" => $name
    ]
]);
