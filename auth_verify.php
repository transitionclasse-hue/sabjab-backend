<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/db_config.php";

// Test DB connection
$stmt = $pdo->query("SELECT 1");

echo json_encode([
    "status" => "success",
    "message" => "Connected to Supabase PostgreSQL successfully"
]);
