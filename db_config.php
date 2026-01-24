<?php
// =====================================================
// db_config.php — Supabase PostgreSQL (PDO, Safe, Pooled)
// =====================================================

error_reporting(0);
ini_set("display_errors", 0);

header("Content-Type: application/json");

// 🔐 PUT YOUR SUPABASE DETAILS HERE (from Connect popup)
$DB_HOST = "db.oywspaweispkmljnxgzo.supabase.co";
$DB_PORT = "5432";
$DB_NAME = "postgres";
$DB_USER = "postgres";
$DB_PASS = "caso4.2h2ogypsum";

// PDO DSN
$dsn = "pgsql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;sslmode=require";

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_PERSISTENT => false, // VERY IMPORTANT: avoid connection leaks
    ]);
} catch (Exception $e) {<?php
// =====================================================
// db_config.php — Supabase PostgreSQL (PDO)
// =====================================================

error_reporting(0);
ini_set("display_errors", 0);

header("Content-Type: application/json");

// 🔐 PUT YOUR SUPABASE DETAILS HERE
$DB_HOST = "db.oywspaweispkmljnxgzo.supabase.co";
$DB_PORT = "5432";
$DB_NAME = "postgres";
$DB_USER = "postgres";
$DB_PASS = "caso4.2h2ogypsum";

$dsn = "pgsql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;sslmode=require";

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_PERSISTENT => false,
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}
