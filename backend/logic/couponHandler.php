<?php

require_once "../config/dbaccess.php";

header("Content-Type: application/json");

// JSON-Daten aus der Anfrage auslesen
$data = json_decode(file_get_contents("php://input"), true);

// Gutscheincode auslesen und Leerzeichen entfernen
$code = trim($data["code"] ?? "");

// Datenbankverbindung herstellen
$db = new DBAccess();
$pdo = $db->connect();

// Gutschein anhand des eingegebenen Codes suchen
$stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ?");
$stmt->execute([$code]);

$coupon = $stmt->fetch(PDO::FETCH_ASSOC);

// Prüfen, ob ein Gutschein mit diesem Code existiert
if (!$coupon) {
    echo json_encode([
        "success" => false,
        "message" => "Ungültiger Gutschein"
    ]);
    exit;
}

// Prüfen, ob der Gutschein bereits abgelaufen ist
if (
    !empty($coupon["expires_at"]) &&
    strtotime($coupon["expires_at"]) < strtotime(date("Y-m-d"))
) {
    echo json_encode([
        "success" => false,
        "message" => "Dieser Gutschein ist abgelaufen"
    ]);
    exit;
}

// Gutschein ist gültig und der Rabattwert wird zurückgegeben
echo json_encode([
    "success" => true,
    "message" => $coupon["value"] . "% Gutschein eingelöst!",
    "value" => $coupon["value"]
]);