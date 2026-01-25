<?php
header("Content-Type: application/json");

require_once __DIR__ . "/db_config.php";

try {
    $stmt = $pdo->query("SELECT 1");
    echo json_encode([
        "status" => "success",
        "message" => "DB connected successfully"
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "DB not connected",
        "details" => $e->getMessage()
    ]);
}
