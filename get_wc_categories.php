<?php
header("Content-Type: application/json");

// Load WooCommerce API keys from Render environment
$WC_KEY    = $_ENV["WC_KEY"];
$WC_SECRET = $_ENV["WC_SECRET"];
$WC_STORE  = $_ENV["WC_STORE_URL"];

if (!$WC_KEY || !$WC_SECRET || !$WC_STORE) {
    echo json_encode([
        "status"  => false,
        "message" => "WooCommerce API keys or store URL missing."
    ]);
    exit;
}

$url = $WC_STORE . "/wp-json/wc/v3/products/categories?per_page=100";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_USERPWD, $WC_KEY . ":" . $WC_SECRET);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode([
        "status"  => false,
        "message" => "Failed to fetch WooCommerce categories.",
        "http_code" => $httpCode
    ]);
    exit;
}

$data = json_decode($response, true);

echo json_encode([
    "status"  => true,
    "count"   => count($data),
    "data"    => $data
], JSON_PRETTY_PRINT);
?>
