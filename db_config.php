<?php
error_reporting(0);
ini_set("display_errors", 0);

$DB_HOST = "aws-1-ap-south-1.pooler.supabase.com";
$DB_PORT = "5432";
$DB_NAME = "postgres";
$DB_USER = "postgres.oywspaweispkmljnxgzo";
$DB_PASS = "hP3mMBGk72oOkaSf";

try {
    $dsn = "pgsql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;sslmode=require";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed",
        "details" => $e->getMessage()
    ]);
    exit;
}
