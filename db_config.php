<?php
// ==================================================
// db_config.php - CORRECTED TO USE RENDER ENV VARIABLES (MySQLi)
// ==================================================

// 1. Fetch credentials from Render Environment Variables
// NOTE: These environment variable names must match what you set in Render
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$dbname = getenv('DB_NAME');

// If environment variables are not set, use the hardcoded fallback 
// (Useful only if running outside Render, but keep for completeness)
if (!$host) {
    $host = "srv2124.hstgr.io";
    $user = "u183862199_sj";
    $pass = "YOUR_NEW_PASSWORD"; // <-- REPLACE WITH YOUR NEW PASSWORD HERE
    $dbname = "u183862199_sj";
}

// 2. Create the MySQLi connection object
$conn = new mysqli($host, $user, $pass, $dbname);

// 3. Handle connection failure
if ($conn->connect_error) {
    // If connection fails, log the error (internal) and halt execution
    error_log("Database Connection Failed: " . $conn->connect_error);
    
    // Stop the script here so the calling PHP file (like get_featured_deals.php) 
    // doesn't try to query a non-existent connection.
    exit; 
}

// ==================================================
// 4. FIX get_featured_deals.php (To use MySQLi instead of PDO)
// ==================================================
// This change must be applied to get_featured_deals.php!
// It tells the deals script to use the $conn object.

// if (basename($_SERVER['PHP_SELF']) == 'get_featured_deals.php') {
//     // Only run this logic for the deals file
//     $pdo = null; // Unset the old PDO variable to be safe

//     // Now create the $pdo connection if you MUST use PDO, but 
//     // for simplicity, it's better to update get_featured_deals.php to use $conn (MySQLi).
// }

?>
