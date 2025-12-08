<?php
header("Content-Type: application/json");

// Load DB credentials
$DB_HOST = $_ENV["DB_HOST"];
$DB_USER = $_ENV["DB_USER"];
$DB_PASS = $_ENV["DB_PASS"];
$DB_NAME = $_ENV["DB_NAME"];

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die(json_encode([
        "status" => false,
        "message" => "Database connection failed."
    ]));
}

// Load WooCommerce credentials
$WC_KEY    = $_ENV["WC_KEY"];
$WC_SECRET = $_ENV["WC_SECRET"];
$WC_STORE  = $_ENV["WC_STORE_URL"];

$url = $WC_STORE . "/wp-json/wc/v3/products/categories?per_page=100";

// Fetch categories from WooCommerce
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_USERPWD, $WC_KEY . ":" . $WC_SECRET);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    die(json_encode([
        "status" => false,
        "message" => "Failed to fetch categories from WooCommerce.",
        "http_code" => $httpCode
    ]));
}

$wc_categories = json_decode($response, true);

if (!is_array($wc_categories)) {
    die(json_encode([
        "status" => false,
        "message" => "Invalid data format from WooCommerce."
    ]));
}

// Insert into MySQL
$inserted = 0;

foreach ($wc_categories as $cat) {

    $wc_id  = $cat["id"];
    $name   = $conn->real_escape_string($cat["name"]);
    $slug   = $conn->real_escape_string($cat["slug"]);
    $parent = intval($cat["parent"]);
    $image  = isset($cat["image"]["src"]) ? $conn->real_escape_string($cat["image"]["src"]) : NULL;

    // UPSERT → insert if not exists
    $sql = "
        INSERT INTO categories (wc_category_id, name, slug, parent_wc_id, image_url, is_active)
        VALUES ($wc_id, '$name', '$slug', $parent, " . ($image ? "'$image'" : "NULL") . ", 1)
        ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            slug = VALUES(slug),
            parent_wc_id = VALUES(parent_wc_id),
            image_url = VALUES(image_url);
    ";

    if ($conn->query($sql)) {
        $inserted++;
    }
}

echo json_encode([
    "status"       => true,
    "message"      => "WooCommerce categories synced successfully.",
    "total_synced" => $inserted,
], JSON_PRETTY_PRINT);

?>
