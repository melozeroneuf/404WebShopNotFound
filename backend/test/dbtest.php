<?php

header("Content-Type: application/json");

require_once "../config/dbaccess.php";

$dbAccess = new DBAccess();
$db = $dbAccess->connect();

echo json_encode([
    "success" => true,
    "message" => "Backend und Datenbank funktionieren"
]);