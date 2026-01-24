<?php
header("Content-Type: application/json");
require_once "db_config.php";

// Fetch products from Supabase
try {
    $stmt = $pdo->query("SELECT id, name, price, image FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "products" => $products
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "products" => [],
        "error" => $e->getMessage()
    ]);
}
