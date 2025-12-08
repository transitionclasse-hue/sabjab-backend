<?php

header("Content-Type: application/json");

// Your WooCommerce REST API keys
$consumer_key    = "ck_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
$consumer_secret = "cs_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx";

$store_url = "https://sabjab.com/wp-json/wc/v3/products/categories";

// Prepare request URL
$url = $store_url . "?per_page=100";  // Fetch up to 100 categories

// cURL Call
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERPWD, $consumer_key . ":" . $consumer_secret);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Error Handling
if ($httpcode !== 200) {
    echo json_encode([
        "status" => false,
        "message" => "Failed to fetch WooCommerce categories",
        "http_code" => $httpcode
    ]);
    exit;
}

// Convert JSON
$categories = json_decode($response, true);

// Send final output
echo json_encode([
    "status" => true,
    "count" => count($categories),
    "data" => $categories
], JSON_PRETTY_PRINT);
