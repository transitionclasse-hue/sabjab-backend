<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . "/db_config.php";

$category_id = $_GET["category_id"] ?? null;

if (!$category_id) {
    echo json_encode([
        "status" => "error",
        "message" => "category_id required"
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT id, name, image_url, slug, sort_order 
        FROM subcategories 
        WHERE category_id = :cid AND is_active = true
        ORDER BY sort_order ASC
    ");

    $stmt->execute(["cid" => $category_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $rows
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to load subcategories",
        "details" => $e->getMessage()
    ]);
}
