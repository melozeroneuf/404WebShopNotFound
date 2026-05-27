<?php

session_start();

header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);

$action = $input["action"] ?? "";

if(!isset($_SESSION["cart"])){
    $_SESSION["cart"] = [];
}

if($action === "add"){

    $product = [
        "id" => $input["id"],
        "name" => $input["name"],
        "price" => $input["price"],
        "quantity" => 1
    ];

    $found = false;

    foreach ($_SESSION["cart"] as &$item) {

        if($item["id"] == $product["id"]){
            $item["quantity"]++;
            $found = true;
            break;
        }
    }

        if (!$found) {
            $_SESSION["cart"][] = $product;
        }

        echo json_encode([
            "success" => true,
            "cart" => $_SESSION["cart"]
        ]);

        exit();
    }

    if ($action === "get"){

        echo json_encode([
            "success" => true,
            "cart" => $_SESSION["cart"]
        ]);

        exit();
    }

    if ($action === "increase") {
        $id = $input["id"];

        foreach ($_SESSION["cart"] as &$item) {
            if ($item["id"] == $id) {
                $item["quantity"]++;
                break;
            }
        }

        echo json_encode([
            "success" => true,
            "cart" => $_SESSION["cart"]
        ]);
        exit;
    }

    if ($action === "decrease") {
        $id = $input["id"];

        foreach ($_SESSION["cart"] as $index => &$item) {
            if ($item["id"] == $id) {
                $item["quantity"]--;

                if ($item["quantity"] <= 0) {
                    array_splice($_SESSION["cart"], $index, 1);
                }

                break;
            }
        }

        echo json_encode([
            "success" => true,
            "cart" => $_SESSION["cart"]
        ]);
        exit;
    }

    if ($action === "clear") {
        $_SESSION["cart"] = [];

        echo json_encode([
            "success" => true,
            "cart" => []
        ]);
        exit;
    }
