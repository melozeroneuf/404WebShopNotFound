<?php

session_start();

header("Content-Type: application/json");

require_once "../config/dbaccess.php";

// Prüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Nicht eingeloggt"
    ]);
    exit;
}

try {
    // Datenbankverbindung herstellen
    $db = (new DBAccess())->connect();

    // Gespeicherte Benutzerdaten des eingeloggten Benutzers laden
    $stmt = $db->prepare("
        SELECT firstname, lastname, email, address, zip, city
        FROM users
        WHERE id = :uid
    ");

    $stmt->bindParam(":uid", $_SESSION["user_id"]);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Benutzerdaten erfolgreich zurückgeben
    echo json_encode([
        "success" => true,
        "user" => $user
    ]);

} catch (Exception $e) {

    // Fehler abfangen und an das Frontend zurückgeben
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}