<?php

// THIS FILE IS SAFE TO UPLOAD TO GITHUB
// Real keys come from environment variables (Render Dashboard)

$WC_CONSUMER_KEY    = getenv("WC_KEY");
$WC_CONSUMER_SECRET = getenv("WC_SECRET");

// Optional: WooCommerce store URL (also kept in env for flexibility)
$WC_STORE_URL = getenv("WC_STORE_URL");

// Fallback check
if (!$WC_CONSUMER_KEY || !$WC_CONSUMER_SECRET) {
    die(json_encode([
        "status" => false,
        "message" => "WooCommerce API keys not configured in Render environment variables."
    ]));
}
