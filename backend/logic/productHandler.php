<?php

header("Content-Type: application/json");

require_once "../config/dbaccess.php";

// Datenbankverbindung herstellen
$dbAccess = new DBAccess();
$db = $dbAccess->connect();

// Alle aktiven Produkte aus der Datenbank laden
$stmt = $db->prepare("
    SELECT *
    FROM products
    WHERE is_active = 1
    ORDER BY id ASC
");

$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Produktliste an das Frontend zurückgeben
echo json_encode([
    "success" => true,
    "products" => $products
]);