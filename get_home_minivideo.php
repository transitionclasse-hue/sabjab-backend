<?php
require "db_config.php";

$sql = "
SELECT *
FROM home_minivideos
WHERE is_active = 1
AND (start_time IS NULL OR start_time <= NOW())
AND (end_time IS NULL OR end_time >= NOW())
ORDER BY id DESC
LIMIT 1
";

$res = $conn->query($sql);

if ($row = $res->fetch_assoc()) {
  echo json_encode([
    "success" => true,
    "video" => $row
  ]);
} else {
  echo json_encode([
    "success" => true,
    "video" => null
  ]);
}
