<?php

session_start();

header("Content-Type: application/json");

require_once "../config/dbaccess.php";

// Prüfen, ob der Benutzer bereits über die Session eingeloggt ist
if (isset($_SESSION["user_id"])) {
    echo json_encode([
        "loggedIn" => true,
        "username" => $_SESSION["username"],
        "role" => $_SESSION["role"]
    ]);
    exit();
}

// Falls keine Session existiert, wird geprüft ob ein Remember-Me-Cookie vorhanden ist
if (isset($_COOKIE["remember_user"])) {
    $userId = (int) $_COOKIE["remember_user"];

    // Datenbankverbindung herstellen
    $dbAccess = new DBAccess();
    $db = $dbAccess->connect();

    // Benutzer anhand der gespeicherten Cookie-ID laden
    $stmt = $db->prepare("
        SELECT id, username, role
        FROM users
        WHERE id = :id AND is_active = 1
        LIMIT 1
    ");

    $stmt->bindParam(":id", $userId, PDO::PARAM_INT);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Wenn ein aktiver Benutzer gefunden wurde, Session neu setzen
    if ($user) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];

        echo json_encode([
            "loggedIn" => true,
            "username" => $_SESSION["username"],
            "role" => $_SESSION["role"]
        ]);
        exit();
    }
}

// Wenn weder Session noch gültiger Cookie vorhanden ist, ist der Benutzer nicht eingeloggt
echo json_encode([
    "loggedIn" => false
]);