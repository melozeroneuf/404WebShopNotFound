<?php

session_start();
header("Content-Type: application/json");

require_once "../config/dbaccess.php";

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Nicht eingeloggt"]);
    exit;
}

try {
    $db = (new DBAccess())->connect();

    $stmt = $db->prepare("
        SELECT firstname, lastname, email, address, zip, city
        FROM users
        WHERE id = :uid
    ");
    $stmt->bindParam(":uid", $_SESSION["user_id"]);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "user" => $user
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}