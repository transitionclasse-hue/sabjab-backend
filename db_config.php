<?php
// ==================================================
// db_config.php — SabJab9 (FIXED)
// ==================================================

mysqli_report(MYSQLI_REPORT_OFF);

$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$dbname = getenv('DB_NAME');

// Fallback to Hostinger
if (!$host) {
    // REMOVED "p:" - This was the leak!
    $host = "srv2124.hstgr.io"; 
    $user = "u183862199_sj";
    $pass = "YOUR_REAL_PASSWORD_HERE"; // Ensure this is correct
    $dbname = "u183862199_sj";
}

// Create standard connection (No "p:")
$conn = @mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    header("Content-Type: application/json");
    // This is the error you are seeing. 
    // Once the p: connections clear (after 1 hour), this will stop.
    echo json_encode([
        "success" => false,
        "message" => "DB connection failed: " . mysqli_connect_error()
    ]);
    exit;
}

mysqli_set_charset($conn, "utf8mb4");
