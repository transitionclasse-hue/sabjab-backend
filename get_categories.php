<?php
include "db_config.php";
header("Content-Type: application/json; charset=UTF-8");

// ------------------------------
// READ supercategory_id FROM URL
// ------------------------------
$superId = isset($_GET['supercategory_id']) ? intval($_GET['supercategory_id']) : 0;

// ====================================================
// CASE 1 → NO SUPERCATEGORY ID PASSED → RETURN ALL CATEGORIES
// ====================================================
if ($superId === 0) {

    $sql = "SELECT id, wc_category_id, name, image_url 
            FROM categories 
            ORDER BY id ASC";

    $result = $conn->query($sql);
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        "success" => true,
        "data" => $data
    ]);
    exit;
}


// ====================================================
// CASE 2 → SUPERCATEGORY PASSED → FILTER USING category_ids CSV
// ====================================================

// Step 1: Get category_ids CSV from supercategories table
$scQuery = "SELECT category_ids FROM supercategories WHERE id = $superId LIMIT 1";
$scRes = $conn->query($scQuery);

if (!$scRes || $scRes->num_rows == 0) {
    echo json_encode([
        "success" => false,
        "data" => [],
        "message" => "Invalid supercategory"
    ]);
    exit;
}

$scRow = $scRes->fetch_assoc();
$csv = $scRow["category_ids"];

// If no category_ids assigned → return empty list
if ($csv === null || trim($csv) === "") {
    echo json_encode([
        "success" => true,
        "data" => []
    ]);
    exit;
}

// Step 2: Convert CSV ("1,2,4") to array
$idsArray = array_map('intval', explode(",", $csv));

// Step 3: Create SQL IN(…) list
$idList = implode(",", $idsArray);

// Step 4: Fetch ONLY the categories that belong to this supercategory
$sql = "SELECT id, wc_category_id, name, image_url 
        FROM categories 
        WHERE id IN ($idList)
        ORDER BY id ASC";

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $data
]);

?>
