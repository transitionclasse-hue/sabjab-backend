<?php
// Set headers to prevent caching and ensure JSON output, though this file only returns a key.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// ----------------------------------------------------
// 1. Define the name of the environment variable on Render
// ----------------------------------------------------
$env_variable_name = 'GOOGLE_MAPS_API_KEY';

// ----------------------------------------------------
// 2. Retrieve the API Key from the environment
//    Note: getenv() is generally reliable in server environments like Render.
// ----------------------------------------------------
$google_maps_api_key = getenv($env_variable_name);

// ----------------------------------------------------
// 3. Return the key as a JSON object
// ----------------------------------------------------

if ($google_maps_api_key) {
    echo json_encode([
        "status" => "success",
        "key" => $google_maps_api_key
    ]);
} else {
    // Log an error if the environment variable is missing
    error_log("CRITICAL: GOOGLE_MAPS_API_KEY environment variable is not set.");

    echo json_encode([
        "status" => "error",
        "message" => "API key configuration error."
    ]);
}
?>
