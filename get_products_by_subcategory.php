<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . "/db_config.php";

$subcategory_id = $_GET["subcategory_id"] ?? null;

if (!$subcategory_id) {
    echo json_encode([
        "status" => "error",
        "message" => "subcategory_id required"
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            id, name, slug, price, sale_price, image, unit, weight,
            stock_qty, is_in_stock, rating, rating_count, brand
        FROM products
        WHERE subcategory_id = :sid AND is_active = true
        ORDER BY id DESC
    ");

    $stmt->execute(["sid" => $subcategory_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $rows
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to load products",
        "details" => $e->getMessage()
    ]);
}
