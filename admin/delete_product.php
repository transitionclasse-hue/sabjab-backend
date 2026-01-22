<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

include "../db_config.php";

$id = (int)$_GET["id"];

$conn->query("DELETE FROM products WHERE id=$id");

header("Location: products.php");
exit;
