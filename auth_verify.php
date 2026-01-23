<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

error_reporting(0);
ini_set("display_errors", 0);

session_start();

require_once __DIR__ . "/db_config.php";

$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

if (!isset($input["phone"])) {
    echo json_encode(["success" => false, "message" => "Missing phone"]);
    mysqli_close($conn); // ALWAYS CLOSE before exit
    exit;
}

$phone = trim($input["phone"]);

if (strlen($phone) != 10) {
    echo json_encode(["success" => false, "message" => "Invalid phone number"]);
    mysqli_close($conn); // ALWAYS CLOSE before exit
    exit;
}

// 1. Check user
$stmt = mysqli_prepare($conn, "SELECT id, phone, name FROM users WHERE phone = ?");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Query prepare failed"]);
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $phone);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $_SESSION["user_id"] = $row["id"];
    echo json_encode(["success" => true, "user" => $row]);
    
    // Clean up before exiting
    mysqli_stmt_close($stmt);
    mysqli_close($conn); 
    exit;
}
mysqli_stmt_close($stmt);

// 2. Create user if not found
$name = "User " . substr($phone, -4);
$stmt2 = mysqli_prepare($conn, "INSERT INTO users (phone, name) VALUES (?, ?)");
if (!$stmt2) {
    echo json_encode(["success" => false, "message" => "Insert prepare failed"]);
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmt2, "ss", $phone, $name);
$ok = mysqli_stmt_execute($stmt2);

if (!$ok) {
    echo json_encode(["success" => false, "message" => "Insert failed"]);
    mysqli_stmt_close($stmt2);
    mysqli_close($conn);
    exit;
}

$newUserId = mysqli_insert_id($conn);
$_SESSION["user_id"] = $newUserId;

echo json_encode([
    "success" => true,
    "user" => [
        "id" => $newUserId,
        "phone" => $phone,
        "name" => $name
    ]
]);

// Final cleanup
mysqli_stmt_close($stmt2);
mysqli_close($conn);
?>
