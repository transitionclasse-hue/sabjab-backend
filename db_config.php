<?php
// ==========================================
// db_config.php — Supabase PostgreSQL PDO
// ==========================================

// ⚠️ PUT YOUR REAL VALUES HERE FROM SUPABASE SETTINGS

$DB_HOST = "db.oywspaweispkmljnxgzo.supabase.co";
$DB_PORT = "5432";
$DB_NAME = "postgres";
$DB_USER = "postgres";
$DB_PASS = "caso4.2h2ogypsum";

// ==========================================

try {
    $dsn = "pgsql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;sslmode=require";

    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_PERSISTENT => true,
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed",
        "details" => $e->getMessage()
    ]);
    exit;
}
