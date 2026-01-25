<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . "/db_config.php";

$supercategory_id = $_GET["supercategory_id"] ?? null;

if (!$supercategory_id) {
    echo json_encode(["status" => "error", "message" => "supercategory_id required"]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, name, image FROM categories WHERE supercategory_id = :sid ORDER BY id ASC");
    $stmt->execute(["sid" => $supercategory_id]);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $rows
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to load categories"
    ]);
}
