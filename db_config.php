<?php
// Load environment variables (Render automatically provides them)
$DB_HOST = getenv("DB_HOST");
$DB_USER = getenv("DB_USER");
$DB_PASS = getenv("DB_PASS");
$DB_NAME = getenv("DB_NAME");

// Create MySQL connection
$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_errno) {
    http_response_code(500);
    die(json_encode([
        "success" => false,
        "message" => "Database Connection Failed",
        "error" => $conn->connect_error
    ]));
}

$conn->set_charset("utf8mb4");
?>
