<?php

session_start();
header("Content-Type: application/json");

require_once "../config/dbaccess.php";

// Prüfe Login
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Nicht eingeloggt"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$firstname = trim($data["firstname"] ?? "");
$lastname = trim($data["lastname"] ?? "");
$email = trim($data["email"] ?? "");
$address = trim($data["address"] ?? "");
$zip = trim($data["zip"] ?? "");
$city = trim($data["city"] ?? "");
$discount = (float) ($data["discount"] ?? 0);

if (!$firstname || !$lastname || !$email || !$address || !$zip || !$city) {
    echo json_encode(["success" => false, "message" => "Bitte fülle alle Felder aus"]);
    exit;
}

try {
    $db = (new DBAccess())->connect();
    $userId = (int) $_SESSION["user_id"];

    // Hole Warenkorb
    $stmt = $db->prepare("SELECT product_id AS id, name, price, quantity FROM cart_items WHERE user_id = :uid");
    $stmt->bindParam(":uid", $userId);
    $stmt->execute();
    $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cart)) {
        echo json_encode(["success" => false, "message" => "Warenkorb ist leer"]);
        exit;
    }

    $total = 0;
    foreach ($cart as $item) {
        $total += (float)$item["price"] * (int)$item["quantity"];
    }

    // Rabatt abziehen
    $discountAmount = $total * $discount / 100;
    $total = $total - $discountAmount;

    // Speichere Bestellung
    $stmt = $db->prepare("INSERT INTO orders (user_id, total, status) VALUES (:uid, :total, 'neu')");
    $stmt->bindParam(":uid", $userId);
    $stmt->bindParam(":total", $total);
    $stmt->execute();
    $orderId = $db->lastInsertId();

    // Speichere Bestellpositionen
    $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, name, price, quantity) VALUES (:oid, :pid, :name, :price, :qty)");
    foreach ($cart as $item) {
        $stmt->bindParam(":oid", $orderId);
        $stmt->bindParam(":pid", $item["id"]);
        $stmt->bindParam(":name", $item["name"]);
        $stmt->bindParam(":price", $item["price"]);
        $stmt->bindParam(":qty", $item["quantity"]);
        $stmt->execute();
    }

    // Update Userdaten
    $stmt = $db->prepare("UPDATE users SET firstname = :f, lastname = :l, email = :e, address = :a, zip = :z, city = :c WHERE id = :uid");
    $stmt->bindParam(":f", $firstname);
    $stmt->bindParam(":l", $lastname);
    $stmt->bindParam(":e", $email);
    $stmt->bindParam(":a", $address);
    $stmt->bindParam(":z", $zip);
    $stmt->bindParam(":c", $city);
    $stmt->bindParam(":uid", $userId);
    $stmt->execute();

    // Leere Warenkorb
    $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = :uid");
    $stmt->bindParam(":uid", $userId);
    $stmt->execute();

    echo json_encode(["success" => true, "message" => "Bestellung gespeichert"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Fehler: " . $e->getMessage()]);
}
