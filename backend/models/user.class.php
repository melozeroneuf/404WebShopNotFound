<?php

class User {
    private PDO $conn;

    public function __construct(PDO $dbConnection) {
        $this->conn = $dbConnection;
    }

    public function register(string $username, string $email, string $password): bool {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'customer')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashedPassword);

        return $stmt->execute();
    }

    public function findByEmailOrUsername(string $login): ?array {
        $sql = "SELECT * FROM users 
                WHERE email = :login OR username = :login 
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":login", $login);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function login(string $login, string $password): ?array {
        $user = $this->findByEmailOrUsername($login);

        if (!$user) {
            return null;
        }

        if (!$user["is_active"]) {
            return null;
        }

        if (!password_verify($password, $user["password"])) {
            return null;
        }

        return $user;
    }
}