<?php

session_start();

header("Content-Type: application/json");

require_once "../config/dbaccess.php";

$input = json_decode(file_get_contents("php://input"), true);
$action = $input["action"] ?? "";

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

$isLoggedIn = isset($_SESSION["user_id"]);
$userId = $isLoggedIn ? (int) $_SESSION["user_id"] : null;

$dbAccess = new DBAccess();
$db = $dbAccess->connect();

function getSessionCart(): array {
    return $_SESSION["cart"] ?? [];
}

function getDbCart(PDO $db, int $userId): array {
    $stmt = $db->prepare("SELECT product_id AS id, name, price, quantity FROM cart_items WHERE user_id = :userId ORDER BY id DESC");
    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCurrentCart(PDO $db, bool $isLoggedIn, ?int $userId): array {
    if ($isLoggedIn && $userId !== null) {
        return getDbCart($db, $userId);
    }

    return getSessionCart();
}

function addToSessionCart(array $input): void {
    $id = $input["id"];
    $name = $input["name"];
    $price = (float) $input["price"];

    foreach ($_SESSION["cart"] as &$item) {
        if ($item["id"] == $id) {
            $item["quantity"]++;
            return;
        }
    }

    $_SESSION["cart"][] = [
        "id" => $id,
        "name" => $name,
        "price" => $price,
        "quantity" => 1
    ];
}

function addToDbCart(PDO $db, int $userId, array $input): void {
    $productId = (int) $input["id"];
    $name = $input["name"];
    $price = (float) $input["price"];

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

function changeSessionQuantity($id, int $change): void {
    foreach ($_SESSION["cart"] as $index => &$item) {
        if ($item["id"] == $id) {
            $item["quantity"] += $change;

            if ($item["quantity"] <= 0) {
                array_splice($_SESSION["cart"], $index, 1);
            }

            return;
        }
    }
}

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

    $delete = $db->prepare("
        DELETE FROM cart_items 
        WHERE user_id = :userId AND product_id = :productId AND quantity <= 0
    ");
    $delete->bindParam(":userId", $userId, PDO::PARAM_INT);
    $delete->bindParam(":productId", $productId, PDO::PARAM_INT);
    $delete->execute();
}

function clearDbCart(PDO $db, int $userId): void {
    $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = :userId");
    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $stmt->execute();
}

if ($action === "get") {
    echo json_encode([
        "success" => true,
        "cart" => getCurrentCart($db, $isLoggedIn, $userId)
    ]);
    exit();
}

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

echo json_encode([
    "success" => false,
    "message" => "Unbekannte Aktion"
]);