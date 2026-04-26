<?php

session_start();

header("Content-Type: application/json");

require_once "../config/dbaccess.php";
require_once "../models/user.class.php";

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode([
        "success" => false,
        "message" => "Keine JSON-Daten erhalten"
    ]);
    exit;
}

$action = $input["action"] ?? "";
if ($action === "test") {
    echo json_encode([
        "success" => true,
        "message" => "JSON-Kommunikation funktioniert",
        "receivedData" => $input
    ]);
    exit;
}

if ($action === "login") {
    $login = trim($input["login"] ?? "");
    $password = trim($input["password"] ?? "");
    $remember = $input["remember"] ?? false;

    if ($login === "" || $password === "") {
        echo json_encode([
            "success" => false,
            "message" => "Login und Passwort müssen ausgefüllt sein"
        ]);
        exit;
    }

    $dbAccess = new DBAccess();
    $db = $dbAccess->connect();

    $userModel = new User($db);
    $user = $userModel->login($login, $password);

    if ($user) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];

        if ($remember) {
            setcookie("remember_user", $user["id"], time() + (86400 * 30), "/");
        }

        echo json_encode([
            "success" => true,
            "message" => "Login erfolgreich",
            "user" => [
                "id" => $user["id"],
                "username" => $user["username"],
                "email" => $user["email"],
                "role" => $user["role"]
            ]
        ]);
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "Login fehlgeschlagen"
    ]);
    exit;
}

if ($action === "logout") {
    session_destroy();
    setcookie("remember_user", "", time() - 3600, "/");

    echo json_encode([
        "success" => true,
        "message" => "Logout erfolgreich"
    ]);
    exit;
}

echo json_encode([
    "success" => false,
    "message" => "Unbekannte Aktion"
]);