<?php

session_start();

header("Content-Type: application/json");

require_once "../config/dbaccess.php";

// Prüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Nicht eingeloggt"]);
    exit;
}

// JSON-Daten aus der Anfrage auslesen
$data = json_decode(file_get_contents("php://input"), true);

// Kundendaten auslesen und Leerzeichen entfernen
$firstname = trim($data["firstname"] ?? "");
$lastname = trim($data["lastname"] ?? "");
$email = trim($data["email"] ?? "");
$address = trim($data["address"] ?? "");
$zip = trim($data["zip"] ?? "");
$city = trim($data["city"] ?? "");
$discount = (float) ($data["discount"] ?? 0);

// Prüfen, ob alle Pflichtfelder ausgefüllt wurden
if (!$firstname || !$lastname || !$email || !$address || !$zip || !$city) {
    echo json_encode(["success" => false, "message" => "Bitte fülle alle Felder aus"]);
    exit;
}

try {
    // Datenbankverbindung herstellen
    $db = (new DBAccess())->connect();
    $userId = (int) $_SESSION["user_id"];

    // Warenkorb des eingeloggten Benutzers aus der Datenbank laden
    $stmt = $db->prepare("SELECT product_id AS id, name, price, quantity FROM cart_items WHERE user_id = :uid");
    $stmt->bindParam(":uid", $userId);
    $stmt->execute();
    $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Bestellung abbrechen, wenn der Warenkorb leer ist
    if (empty($cart)) {
        echo json_encode(["success" => false, "message" => "Warenkorb ist leer"]);
        exit;
    }

    // Gesamtsumme aus Preis und Menge berechnen
    $total = 0;
    foreach ($cart as $item) {
        $total += (float)$item["price"] * (int)$item["quantity"];
    }

    // Rabatt berechnen und vom Gesamtpreis abziehen
    $discountAmount = $total * $discount / 100;
    $total = $total - $discountAmount;

    // Neue Bestellung speichern
    $stmt = $db->prepare("INSERT INTO orders (user_id, total, status) VALUES (:uid, :total, 'neu')");
    $stmt->bindParam(":uid", $userId);
    $stmt->bindParam(":total", $total);
    $stmt->execute();

    // ID der gerade erstellten Bestellung holen
    $orderId = $db->lastInsertId();

    // Einzelne Produkte der Bestellung speichern
    $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, name, price, quantity) VALUES (:oid, :pid, :name, :price, :qty)");
    foreach ($cart as $item) {
        $stmt->bindParam(":oid", $orderId);
        $stmt->bindParam(":pid", $item["id"]);
        $stmt->bindParam(":name", $item["name"]);
        $stmt->bindParam(":price", $item["price"]);
        $stmt->bindParam(":qty", $item["quantity"]);
        $stmt->execute();
    }

    // Kundendaten im Benutzerkonto aktualisieren
    $stmt = $db->prepare("UPDATE users SET firstname = :f, lastname = :l, email = :e, address = :a, zip = :z, city = :c WHERE id = :uid");
    $stmt->bindParam(":f", $firstname);
    $stmt->bindParam(":l", $lastname);
    $stmt->bindParam(":e", $email);
    $stmt->bindParam(":a", $address);
    $stmt->bindParam(":z", $zip);
    $stmt->bindParam(":c", $city);
    $stmt->bindParam(":uid", $userId);
    $stmt->execute();

    // Warenkorb nach erfolgreicher Bestellung leeren
    $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = :uid");
    $stmt->bindParam(":uid", $userId);
    $stmt->execute();

    echo json_encode(["success" => true, "message" => "Bestellung gespeichert"]);

} catch (Exception $e) {
    // Fehler abfangen und als JSON zurückgeben
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Fehler: " . $e->getMessage()]);
}