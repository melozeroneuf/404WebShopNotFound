<?php

session_start();

header("Content-Type: application/json");

require_once "../config/dbaccess.php";

// JSON-Daten aus der Anfrage auslesen
$input = json_decode(file_get_contents("php://input"), true);
$action = $input["action"] ?? "";

// Warenkorb in der Session anlegen, falls noch keiner existiert
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

// Prüfen, ob der Benutzer eingeloggt ist
$isLoggedIn = isset($_SESSION["user_id"]);
$userId = $isLoggedIn ? (int) $_SESSION["user_id"] : null;

// Datenbankverbindung herstellen
$dbAccess = new DBAccess();
$db = $dbAccess->connect();

// Warenkorb aus der Session zurückgeben
function getSessionCart(): array {
    return $_SESSION["cart"] ?? [];
}

// Warenkorb eines eingeloggten Benutzers aus der Datenbank laden
function getDbCart(PDO $db, int $userId): array {
    $stmt = $db->prepare("SELECT product_id AS id, name, price, quantity FROM cart_items WHERE user_id = :userId ORDER BY id DESC");
    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Je nach Loginstatus den richtigen Warenkorb laden
function getCurrentCart(PDO $db, bool $isLoggedIn, ?int $userId): array {
    if ($isLoggedIn && $userId !== null) {
        return getDbCart($db, $userId);
    }

    return getSessionCart();
}

// Produkt zum Session-Warenkorb hinzufügen
function addToSessionCart(array $input): void {
    $id = $input["id"];
    $name = $input["name"];
    $price = (float) $input["price"];

    // Falls Produkt schon existiert, nur Menge erhöhen
    foreach ($_SESSION["cart"] as &$item) {
        if ($item["id"] == $id) {
            $item["quantity"]++;
            return;
        }
    }

    // Neues Produkt in den Warenkorb legen
    $_SESSION["cart"][] = [
        "id" => $id,
        "name" => $name,
        "price" => $price,
        "quantity" => 1
    ];
}

// Produkt zum Datenbank-Warenkorb hinzufügen
function addToDbCart(PDO $db, int $userId, array $input): void {
    $productId = (int) $input["id"];
    $name = $input["name"];
    $price = (float) $input["price"];

    // Falls das Produkt schon vorhanden ist, wird nur die Menge erhöht
    $stmt = $db->prepare("
        INSERT INTO cart_items (user_id, product_id, name, price, quantity)
        VALUES (:userId, :productId, :name, :price, 1)
        ON DUPLICATE KEY UPDATE quantity = quantity + 1
    ");

    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $stmt->bindParam(":productId", $productId, PDO::PARAM_INT);
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":price", $price);
    $stmt->execute();
}

// Menge eines Produkts im Session-Warenkorb ändern
function changeSessionQuantity($id, int $change): void {
    foreach ($_SESSION["cart"] as $index => &$item) {
        if ($item["id"] == $id) {
            $item["quantity"] += $change;

            // Produkt entfernen, wenn Menge 0 oder kleiner wird
            if ($item["quantity"] <= 0) {
                array_splice($_SESSION["cart"], $index, 1);
            }

            return;
        }
    }
}

// Menge eines Produkts im Datenbank-Warenkorb ändern
function changeDbQuantity(PDO $db, int $userId, int $productId, int $change): void {
    if ($change > 0) {
        $stmt = $db->prepare("
            UPDATE cart_items
            SET quantity = quantity + 1
            WHERE user_id = :userId AND product_id = :productId
        ");
    } else {
        $stmt = $db->prepare("
            UPDATE cart_items
            SET quantity = quantity - 1
            WHERE user_id = :userId AND product_id = :productId
        ");
    }

    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $stmt->bindParam(":productId", $productId, PDO::PARAM_INT);
    $stmt->execute();

    // Produkte mit Menge 0 oder kleiner direkt aus dem Warenkorb entfernen
    $delete = $db->prepare("
        DELETE FROM cart_items
        WHERE user_id = :userId AND product_id = :productId AND quantity <= 0
    ");
    $delete->bindParam(":userId", $userId, PDO::PARAM_INT);
    $delete->bindParam(":productId", $productId, PDO::PARAM_INT);
    $delete->execute();
}

// Warenkorb eines eingeloggten Benutzers komplett leeren
function clearDbCart(PDO $db, int $userId): void {
    $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = :userId");
    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $stmt->execute();
}

// Aktuellen Warenkorb laden
if ($action === "get") {
    echo json_encode([
        "success" => true,
        "cart" => getCurrentCart($db, $isLoggedIn, $userId)
    ]);
    exit();
}

// Produkt zum Warenkorb hinzufügen
if ($action === "add") {
    if ($isLoggedIn) {
        addToDbCart($db, $userId, $input);
    } else {
        addToSessionCart($input);
    }

    echo json_encode([
        "success" => true,
        "cart" => getCurrentCart($db, $isLoggedIn, $userId)
    ]);
    exit();
}

// Produktmenge erhöhen
if ($action === "increase") {
    $id = (int) $input["id"];

    if ($isLoggedIn) {
        changeDbQuantity($db, $userId, $id, 1);
    } else {
        changeSessionQuantity($id, 1);
    }

    echo json_encode([
        "success" => true,
        "cart" => getCurrentCart($db, $isLoggedIn, $userId)
    ]);
    exit();
}

// Produktmenge verringern
if ($action === "decrease") {
    $id = (int) $input["id"];

    if ($isLoggedIn) {
        changeDbQuantity($db, $userId, $id, -1);
    } else {
        changeSessionQuantity($id, -1);
    }

    echo json_encode([
        "success" => true,
        "cart" => getCurrentCart($db, $isLoggedIn, $userId)
    ]);
    exit();
}

// Warenkorb komplett leeren
if ($action === "clear") {
    if ($isLoggedIn) {
        clearDbCart($db, $userId);
    } else {
        $_SESSION["cart"] = [];
    }

    echo json_encode([
        "success" => true,
        "cart" => []
    ]);
    exit();
}

// Einzelnes Produkt aus dem Warenkorb entfernen
if ($action === "remove") {
    $id = (int) $input["id"];

    if ($isLoggedIn) {
        $stmt = $db->prepare("
            DELETE FROM cart_items
            WHERE user_id = :userId AND product_id = :productId
        ");
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmt->bindParam(":productId", $id, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // Produkt im Session-Warenkorb suchen und entfernen
        foreach ($_SESSION["cart"] as $index => $item) {
            if ($item["id"] == $id) {
                array_splice($_SESSION["cart"], $index, 1);
                break;
            }
        }
    }

    echo json_encode([
        "success" => true,
        "cart" => getCurrentCart($db, $isLoggedIn, $userId)
    ]);
    exit();
}

// Falls keine bekannte Aktion übergeben wurde
echo json_encode([
    "success" => false,
    "message" => "Unbekannte Aktion"
]);