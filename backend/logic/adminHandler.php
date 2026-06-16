<?php

session_start();

header("Content-Type: application/json");

require_once "../config/dbaccess.php";

// Prüft, ob ein Benutzer eingeloggt ist und Adminrechte besitzt
if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    echo json_encode([
        "success" => false,
        "message" => "Zugriff verweigert"
    ]);
    exit();
}

// Content-Type auslesen, damit JSON und FormData unterstützt werden
$contentType = $_SERVER["CONTENT_TYPE"] ?? "";

// Bei Datei-Uploads kommen die Daten über $_POST, sonst als JSON
if (strpos($contentType, "multipart/form-data") !== false) {
    $input = $_POST;
} else {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        $input = [];
    }
}

// Gewünschte Aktion aus der Anfrage holen
$action = $input["action"] ?? "";

// Datenbankverbindung herstellen
$dbAccess = new DBAccess();
$db = $dbAccess->connect();

// Produktbild hochladen und den Dateinamen zurückgeben
function uploadProductImage($currentImage = "")
{
    // Falls kein neues Bild hochgeladen wurde, bleibt das bisherige Bild erhalten
    if (!isset($_FILES["imageFile"]) || $_FILES["imageFile"]["error"] !== UPLOAD_ERR_OK) {
        return $currentImage;
    }

    $uploadDir = __DIR__ . "/../../frontend/img/";

    $originalName = basename($_FILES["imageFile"]["name"]);
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // Nur sichere Bildformate erlauben
    $allowedExtensions = ["jpg", "jpeg", "png", "webp"];

    if (!in_array($extension, $allowedExtensions)) {
        return $currentImage;
    }

    // Eindeutigen Dateinamen erzeugen, damit keine alten Bilder überschrieben werden
    $newFileName = "product_" . time() . "_" . rand(1000, 9999) . "." . $extension;
    $targetPath = $uploadDir . $newFileName;

    move_uploaded_file($_FILES["imageFile"]["tmp_name"], $targetPath);

    return $newFileName;
}

// Alle Benutzer für die Adminverwaltung laden
if ($action === "getUsers") {
    $stmt = $db->prepare("
        SELECT id, username, email, role, is_active, created_at
        FROM users
        ORDER BY id ASC
    ");
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "users" => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
    exit();
}

// Rolle eines Benutzers ändern
if ($action === "updateRole") {
    $userId = (int) ($input["userId"] ?? 0);
    $role = $input["role"] ?? "customer";

    // Nur vorhandene Rollen zulassen
    if (!in_array($role, ["customer", "admin"])) {
        echo json_encode([
            "success" => false,
            "message" => "Ungültige Rolle"
        ]);
        exit();
    }

    $stmt = $db->prepare("
        UPDATE users
        SET role = :role
        WHERE id = :userId
    ");

    $stmt->bindParam(":role", $role);
    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Rolle wurde geändert"
    ]);
    exit();
}

// Benutzer aktivieren oder deaktivieren
if ($action === "updateUserStatus") {

    $userId = (int) ($input["userId"] ?? 0);
    $isActive = (int) ($input["is_active"] ?? 1);

    $stmt = $db->prepare("
        UPDATE users
        SET is_active = :is_active
        WHERE id = :userId
    ");

    $stmt->bindParam(":is_active", $isActive, PDO::PARAM_INT);
    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);

    $stmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Benutzerstatus aktualisiert"
    ]);

    exit();
}

// Benutzer aus der Datenbank löschen
if ($action === "deleteUser") {
    $userId = (int) ($input["userId"] ?? 0);

    // Verhindert, dass ein Admin sein eigenes Konto löscht
    if ($userId === (int) $_SESSION["user_id"]) {
        echo json_encode([
            "success" => false,
            "message" => "Du kannst dich nicht selbst löschen"
        ]);
        exit();
    }

    $stmt = $db->prepare("
        DELETE FROM users
        WHERE id = :userId
    ");

    $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Benutzer wurde gelöscht"
    ]);
    exit();
}

// Alle Produkte für die Adminübersicht laden
if ($action === "getProducts") {
    $stmt = $db->prepare("
        SELECT
            id,
            name,
            description,
            category,
            price,
            rating,
            image,
            is_active
        FROM products
        ORDER BY id ASC
    ");
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "products" => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
    exit();
}

// Neues Produkt anlegen
if ($action === "createProduct") {
    $name = trim($input["name"] ?? "");
    $description = trim($input["description"] ?? "");
    $category = trim($input["category"] ?? "");
    $price = (float) ($input["price"] ?? 0);
    $rating = (float) ($input["rating"] ?? 0);
    $image = uploadProductImage(trim($input["image"] ?? ""));
    $isActive = (int) ($input["is_active"] ?? 1);

    // Produktname und Preis sind Pflichtfelder
    if ($name === "" || $price <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "Produktname und Preis müssen ausgefüllt sein"
        ]);
        exit();
    }

    $stmt = $db->prepare("
        INSERT INTO products
            (name, description, category, price, rating, image, is_active)
        VALUES
            (:name, :description, :category, :price, :rating, :image, :is_active)
    ");

    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":category", $category);
    $stmt->bindParam(":price", $price);
    $stmt->bindParam(":rating", $rating);
    $stmt->bindParam(":image", $image);
    $stmt->bindParam(":is_active", $isActive, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Produkt wurde angelegt"
    ]);
    exit();
}

// Bestehendes Produkt bearbeiten
if ($action === "updateProduct") {
    $productId = (int) ($input["productId"] ?? 0);
    $name = trim($input["name"] ?? "");
    $description = trim($input["description"] ?? "");
    $category = trim($input["category"] ?? "");
    $price = (float) ($input["price"] ?? 0);
    $rating = (float) ($input["rating"] ?? 0);
    $image = uploadProductImage(trim($input["image"] ?? ""));
    $isActive = (int) ($input["is_active"] ?? 1);

    // Prüfen, ob die wichtigsten Produktdaten gültig sind
    if ($productId <= 0 || $name === "" || $price <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "Ungültige Produktdaten"
        ]);
        exit();
    }

    $stmt = $db->prepare("
        UPDATE products
        SET
            name = :name,
            description = :description,
            category = :category,
            price = :price,
            rating = :rating,
            image = :image,
            is_active = :is_active
        WHERE id = :productId
    ");

    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":category", $category);
    $stmt->bindParam(":price", $price);
    $stmt->bindParam(":rating", $rating);
    $stmt->bindParam(":image", $image);
    $stmt->bindParam(":is_active", $isActive, PDO::PARAM_INT);
    $stmt->bindParam(":productId", $productId, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Produkt wurde aktualisiert"
    ]);
    exit();
}

// Produkt aus der Datenbank löschen
if ($action === "deleteProduct") {
    $productId = (int) ($input["productId"] ?? 0);

    // Ohne gültige Produkt-ID darf nichts gelöscht werden
    if ($productId <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "Ungültige Produkt-ID"
        ]);
        exit();
    }

    $stmt = $db->prepare("
        DELETE FROM products
        WHERE id = :productId
    ");

    $stmt->bindParam(":productId", $productId, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Produkt wurde gelöscht"
    ]);
    exit();
}

// Alle Bestellungen mit Benutzerdaten laden
if ($action === "getOrders") {
    $stmt = $db->prepare("
        SELECT
            orders.id,
            orders.user_id,
            users.username,
            users.email,
            orders.total,
            orders.status,
            orders.created_at
        FROM orders
        LEFT JOIN users ON orders.user_id = users.id
        ORDER BY orders.created_at DESC
    ");

    $stmt->execute();

    echo json_encode([
        "success" => true,
        "orders" => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
    exit();
}

// Status einer Bestellung ändern
if ($action === "updateOrderStatus") {
    $orderId = (int) ($input["orderId"] ?? 0);
    $status = $input["status"] ?? "offen";

    // Nur diese Bestellstatus sind erlaubt
    $allowedStatus = ["offen", "bezahlt", "versendet", "storniert"];

    if ($orderId <= 0 || !in_array($status, $allowedStatus)) {
        echo json_encode([
            "success" => false,
            "message" => "Ungültige Bestelldaten"
        ]);
        exit();
    }

    $stmt = $db->prepare("
        UPDATE orders
        SET status = :status
        WHERE id = :orderId
    ");

    $stmt->bindParam(":status", $status);
    $stmt->bindParam(":orderId", $orderId, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Bestellstatus aktualisiert"
    ]);
    exit();
}

// Einzelne Produkte einer Bestellung laden
if ($action === "getOrderDetails") {

    $orderId = (int) ($input["orderId"] ?? 0);

    $stmt = $db->prepare("
        SELECT
            product_id,
            name,
            price,
            quantity
        FROM order_items
        WHERE order_id = :orderId
    ");

    $stmt->bindParam(":orderId", $orderId, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "items" => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);

    exit();
}

// Alle Gutscheine für die Adminübersicht laden
if ($action === "getCoupons") {
    $stmt = $db->prepare("
        SELECT id, code, value, expires_at
        FROM coupons
        ORDER BY id DESC
    ");

    $stmt->execute();

    echo json_encode([
        "success" => true,
        "coupons" => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
    exit();
}

// Neuen Gutschein erstellen
if ($action === "createCoupon") {

    $code = trim($input["code"] ?? "");
    $value = (int) ($input["value"] ?? 0);
    $expiresAt = trim($input["expires_at"] ?? "");

    // Gutschein muss einen Code, einen gültigen Prozentwert und ein Ablaufdatum haben
    if ($code === "" || $value <= 0 || $value > 100 || $expiresAt === "") {
        echo json_encode([
            "success" => false,
            "message" => "Code, Rabatt-Prozent und Ablaufdatum müssen gültig sein"
        ]);
        exit();
    }

    $stmt = $db->prepare("
        INSERT INTO coupons (code, value, expires_at)
        VALUES (:code, :value, :expires_at)
    ");

    $stmt->bindParam(":code", $code);
    $stmt->bindParam(":value", $value);
    $stmt->bindParam(":expires_at", $expiresAt);

    $stmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Gutschein wurde angelegt"
    ]);

    exit();
}

// Falls keine der bekannten Aktionen passt
echo json_encode([
    "success" => false,
    "message" => "Unbekannte Aktion"
]);