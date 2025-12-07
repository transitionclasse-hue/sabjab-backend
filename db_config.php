<?php
$host = "srv2124.hstgr.io";  // Hostinger MySQL Host
$user = "u183862199_sj";     // Your DB Username
$pass = "Na2b4o7.10h20";  // Your DB Password
$dbname = "u183862199_sj";   // Your Database Name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
