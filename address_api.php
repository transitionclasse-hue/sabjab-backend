<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");

ini_set("session.cookie_samesite", "None");
ini_set("session.cookie_secure", "1");
session_start();

require_once __DIR__ . "/db_config.php";

// ---------------- SESSION CHECK ----------------
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

$user_id = (int) $_SESSION["user_id"];

$method = $_SERVER["REQUEST_METHOD"];

// ==================================================
// GET → Fetch addresses
// ==================================================
if ($method === "GET") {
    try {
        $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([$user_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "status" => "success",
            "data" => $rows
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to load addresses",
            "error" => $e->getMessage()
        ]);
        exit;
    }
}

// ==================================================
// POST → Save address
// ==================================================
if ($method === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    if (
        empty($input["name"]) ||
        empty($input["phone"]) ||
        empty($input["address_line"]) ||
        empty($input["city"]) ||
        empty($input["pincode"])
    ) {
        echo json_encode(["status" => "error", "message" => "Missing fields"]);
        exit;
    }

    $name = $input["name"];
    $phone = $input["phone"];
    $address_line = $input["address_line"];
    $city = $input["city"];
    $pincode = $input["pincode"];
    $lat = $input["lat"] ?? null;
    $lng = $input["lng"] ?? null;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO addresses 
            (user_id, name, phone, address_line, city, pincode, lat, lng)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            RETURNING id
        ");

        $stmt->execute([
            $user_id,
            $name,
            $phone,
            $address_line,
            $city,
            $pincode,
            $lat,
            $lng
        ]);

        $newId = $stmt->fetchColumn();

        echo json_encode([
            "status" => "success",
            "message" => "Address saved",
            "id" => $newId
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to save address",
            "error" => $e->getMessage()
        ]);
        exit;
    }
}

// ==================================================
// INVALID METHOD
// ==================================================
http_response_code(405);
echo json_encode(["status" => "error", "message" => "Method not allowed"]);
exit;
