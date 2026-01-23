<?php
// ==================================================
// db_config.php — SabJab9 (Connection-Safe Version)
// ==================================================

// Turn off mysqli exceptions (VERY IMPORTANT)
mysqli_report(MYSQLI_REPORT_OFF);

// Fetch credentials from environment
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$dbname = getenv('DB_NAME');

// Fallback to Hostinger (if env not set)
if (!$host) {
    // Use PERSISTENT connection (p:)
    $host = "p:srv2124.hstgr.io";
    $user = "u183862199_sj";
    $pass = "YOUR_REAL_PASSWORD_HERE";
    $dbname = "u183862199_sj";
} else {
    // Also persistent for Render
    $host = "p:" . $host;
}

// Create connection
$conn = @mysqli_connect($host, $user, $pass, $dbname);

// If failed, return JSON instead of crashing
if (!$conn) {
    header("Content-Type: application/json");
    echo json_encode([
        "success" => false,
        "message" => "DB connection failed: " . mysqli_connect_error()
    ]);
    exit;
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");
