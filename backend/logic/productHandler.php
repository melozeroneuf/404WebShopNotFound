<?php

header("Content-Type: application/json");

require_once "../config/dbaccess.php";

$dbAccess = new DBAccess();
$db = $dbAccess->connect();

$stmt = $db->prepare("SELECT * FROM products WHERE is_active = 1 ORDER BY id ASC");
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "products" => $products
]);
