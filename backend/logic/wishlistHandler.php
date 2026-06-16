<?php

session_start();

header("Content-Type: application/json");

require_once "../config/dbaccess.php";

// JSON-Daten aus der Anfrage auslesen
$input = json_decode(file_get_contents("php://input"), true);
$action = $input["action"] ?? "";

// Wunschliste darf nur von eingeloggten Benutzern genutzt werden
if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Nicht eingeloggt"
    ]);
    exit();
}

// Benutzer-ID aus der Session speichern
$userId = (int) $_SESSION["user_id"];

// Datenbankverbindung herstellen
$dbAccess = new DBAccess();
$db = $dbAccess->connect();

// Alle Produkt-IDs aus der Wunschliste des Benutzers laden
function getWishlistItems(PDO $db, int $userId): array {
    $stmt = $db->prepare("SELECT product_id FROM wishlist WHERE user_id = :userId ORDER BY id DESC");
    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $stmt->execute();

    // IDs als Strings zurückgeben, damit sie im Frontend leichter verglichen werden können
    return array_map(function ($row) {
        return (string) $row["product_id"];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));
}

// Wunschliste laden
if ($action === "get") {
    echo json_encode([
        "success" => true,
        "items" => getWishlistItems($db, $userId)
    ]);
    exit();
}

// Produkt zur Wunschliste hinzufügen oder daraus entfernen
if ($action === "toggle") {
    $productId = (int) ($input["id"] ?? 0);

    // Prüfen, ob eine gültige Produkt-ID übergeben wurde
    if ($productId <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "Ungultige Produkt-ID"
        ]);
        exit();
    }

    // Prüfen, ob das Produkt bereits in der Wunschliste liegt
    $check = $db->prepare("SELECT id FROM wishlist WHERE user_id = :userId AND product_id = :productId LIMIT 1");
    $check->bindParam(":userId", $userId, PDO::PARAM_INT);
    $check->bindParam(":productId", $productId, PDO::PARAM_INT);
    $check->execute();

    $existing = $check->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Wenn vorhanden, wird es aus der Wunschliste entfernt
        $delete = $db->prepare("DELETE FROM wishlist WHERE id = :id");
        $delete->bindParam(":id", $existing["id"], PDO::PARAM_INT);
        $delete->execute();
    } else {
        // Wenn nicht vorhanden, wird es zur Wunschliste hinzugefügt
        $insert = $db->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (:userId, :productId)");
        $insert->bindParam(":userId", $userId, PDO::PARAM_INT);
        $insert->bindParam(":productId", $productId, PDO::PARAM_INT);
        $insert->execute();
    }

    // Aktualisierte Wunschliste zurückgeben
    echo json_encode([
        "success" => true,
        "items" => getWishlistItems($db, $userId)
    ]);
    exit();
}

// Falls keine bekannte Aktion übergeben wurde
echo json_encode([
    "success" => false,
    "message" => "Unbekannte Aktion"
]);