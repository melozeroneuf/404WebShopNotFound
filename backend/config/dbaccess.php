<?php

class DBAccess{
    private string $host = "localhost";
    private string $dbname = "404webshopnotfound";
    private string $username = "root";
    private string $password = "";

    public function connect(): PDO {
        try{
            $pdo = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                $this->username,
                $this->password);


                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
        } catch (PDOException $e){
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Datenbankverbindung fehlgeschlagen"
            ]);
            exit;
        }
    }
}