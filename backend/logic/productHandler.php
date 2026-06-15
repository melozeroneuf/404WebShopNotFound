<?php

header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit;
}


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
