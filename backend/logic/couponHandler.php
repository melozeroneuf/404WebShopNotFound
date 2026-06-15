<?php
require_once "../config/dbaccess.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$code = trim($data["code"] ?? "");

$db = new DBAccess();
$pdo = $db->connect();

$stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ?");
$stmt->execute([$code]);

$coupon = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$coupon) {
    echo json_encode([
        "success" => false,
        "message" => "Ungültiger Gutschein"
    ]);
    exit;
}

if (
    !empty($coupon["expires_at"]) &&
    strtotime($coupon["expires_at"]) < strtotime(date("Y-m-d"))
) {
    echo json_encode([
        "success" => false,
        "message" => "Dieser Gutschein ist abgelaufen"
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => $coupon["value"] . "% Gutschein eingelöst!",
    "value" => $coupon["value"]
]);