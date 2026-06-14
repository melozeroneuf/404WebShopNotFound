<?php

session_start();

header("Content-Type: application/json");

require_once "../config/dbaccess.php";

if (isset($_SESSION["user_id"])) {
    echo json_encode([
        "loggedIn" => true,
        "username" => $_SESSION["username"],
        "role" => $_SESSION["role"]
    ]);
    exit();
}

if (isset($_COOKIE["remember_user"])) {
    $userId = (int) $_COOKIE["remember_user"];

    $dbAccess = new DBAccess();
    $db = $dbAccess->connect();

    $stmt = $db->prepare("
        SELECT id, username, role
        FROM users
        WHERE id = :id AND is_active = 1
        LIMIT 1
    ");

    $stmt->bindParam(":id", $userId, PDO::PARAM_INT);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

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

echo json_encode([
    "loggedIn" => false
]);