<?php

session_start();

header("Content-Type: application/json");

require_once "../config/dbaccess.php";

$input = json_decode(file_get_contents("php://input"), true);
$action = $input["action"] ?? "";

if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Nicht eingeloggt"
    ]);
    exit();
}

$userId = (int) $_SESSION["user_id"];

$dbAccess = new DBAccess();
$db = $dbAccess->connect();

function getWishlistItems(PDO $db, int $userId): array {
    $stmt = $db->prepare("SELECT product_id FROM wishlist WHERE user_id = :userId ORDER BY id DESC");
    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $stmt->execute();

    return array_map(function ($row) {
        return (string) $row["product_id"];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($action === "get") {
    echo json_encode([
        "success" => true,
        "items" => getWishlistItems($db, $userId)
    ]);
    exit();
}

if ($action === "toggle") {
    $productId = (int) ($input["id"] ?? 0);

    if ($productId <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "Ungultige Produkt-ID"
        ]);
        exit();
    }

    $check = $db->prepare("SELECT id FROM wishlist WHERE user_id = :userId AND product_id = :productId LIMIT 1");
    $check->bindParam(":userId", $userId, PDO::PARAM_INT);
    $check->bindParam(":productId", $productId, PDO::PARAM_INT);
    $check->execute();

    $existing = $check->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $delete = $db->prepare("DELETE FROM wishlist WHERE id = :id");
        $delete->bindParam(":id", $existing["id"], PDO::PARAM_INT);
        $delete->execute();
    } else {
        $insert = $db->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (:userId, :productId)");
        $insert->bindParam(":userId", $userId, PDO::PARAM_INT);
        $insert->bindParam(":productId", $productId, PDO::PARAM_INT);
        $insert->execute();
    }

    echo json_encode([
        "success" => true,
        "items" => getWishlistItems($db, $userId)
    ]);
    exit();
}

echo json_encode([
    "success" => false,
    "message" => "Unbekannte Aktion"
]);
