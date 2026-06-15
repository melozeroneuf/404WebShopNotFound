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

// Register
if ($action === "register") {
    $username = trim($input["username"] ?? "");
    $email = trim($input["email"] ?? "");
    $password = trim($input["password"] ?? "");
    $passwordRepeat = trim($input["passwordRepeat"] ?? "");
    $salutation = trim($input["salutation"] ?? "");
    $firstname = trim($input["firstname"] ?? "");
    $lastname = trim($input["lastname"] ?? "");
    $address = trim($input["address"] ?? "");
    $zip = trim($input["zip"] ?? "");
    $city = trim($input["city"] ?? "");
    $paymentInfo = trim($input["payment_info"] ?? "");

    if ($username === "" || $email === "" || $password === "" || $passwordRepeat === "") {
        echo json_encode([
            "success" => false,
            "message" => "Bitte alle Felder ausfüllen"
        ]);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "success" => false,
            "message" => "Ungültige E-Mail-Adresse"
        ]);
        exit();
    }

    if ($password !== $passwordRepeat) {
        echo json_encode([
            "success" => false,
            "message" => "Die Passwörter stimmen nicht überein"
        ]);
        exit();
    }

    if (strlen($password) < 8) {
        echo json_encode([
            "success" => false,
            "message" => "Das Passwort muss mindestens 8 Zeichen lang sein"
        ]);
        exit();
    }

    if (
        $firstname === "" ||
        $lastname === "" ||
        $address === "" ||
        $zip === "" ||
        $city === ""
    ) {
        echo json_encode([
            "success" => false,
            "message" => "Bitte alle Pflichtfelder ausfüllen"
        ]);
        exit();
    }

    if (strlen($username) < 3) {
        echo json_encode([
            "success" => false,
            "message" => "Der Benutzername muss mindestens 3 Zeichen lang sein"
        ]);
        exit();
    }

    if (!preg_match("/^\d{4}$/", $zip)) {
        echo json_encode([
            "success" => false,
            "message" => "Bitte eine gültige PLZ eingeben"
        ]);
        exit();
    }

    $dbAccess = new DBAccess();
    $db = $dbAccess->connect();

    $userModel = new User($db);

    try {
        $created = $userModel->register(
            $username,
            $email,
            $password,
            $salutation,
            $firstname,
            $lastname,
            $address,
            $zip,
            $city,
            $paymentInfo
        );

        if ($created) {
            $user = $userModel->findByEmailOrUsername($email);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];

            echo json_encode([
                "success" => true,
                "message" => "Registrierung erfolgreich, du bist jetzt eingeloggt",
                "user" => [
                    "id" => $user["id"],
                    "username" => $user["username"],
                    "email" => $user["email"],
                    "role" => $user["role"]
                ]
            ]);
            exit();
        }

        echo json_encode([
            "success" => false,
            "message" => "Registrierung fehlgeschlagen"
        ]);
        exit();

    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "message" => "Der Username oder E-Mail existiert bereits"
        ]);
        exit();
    }
}

// Login
if ($action === "login") {
    $login = trim($input["login"] ?? "");
    $password = trim($input["password"] ?? "");
    $remember = $input["remember"] ?? false;

    if ($login === "" || $password === "") {
        echo json_encode([
            "success" => false,
            "message" => "Login und Passwort müssen ausgefüllt sein"
        ]);
        exit();
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
        exit();
    }

    echo json_encode([
        "success" => false,
        "message" => "Login fehlgeschlagen"
    ]);
    exit();
}

// Logout
if ($action === "logout") {
    $_SESSION["cart"] = [];

    session_destroy();
    setcookie("remember_user", "", time() - 3600, "/");

    echo json_encode([
        "success" => true,
        "message" => "Logout erfolgreich"
    ]);
    exit();
}

// Wenn unbekannter Fehler kommt
echo json_encode([
    "success" => false,
    "message" => "Unbekannte Aktion"
]);