<?php
header("Content-Type: application/json");
require_once "db_config.php";

if ($conn) {
    echo json_encode(["status" => "ok", "message" => "DB connected"]);
} else {
    echo json_encode(["status" => "error", "message" => "DB not connected"]);
}
