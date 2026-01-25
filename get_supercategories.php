<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . "/db_config.php";

try {
    $stmt = $pdo->query("
        SELECT id, name, image_url, slug, sort_order 
        FROM supercategories 
        WHERE is_active = true 
        ORDER BY sort_order ASC
    ");

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $rows
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to load supercategories",
        "details" => $e->getMessage()
    ]);
}
