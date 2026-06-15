<?php

class User {
    private PDO $conn;

    public function __construct(PDO $dbConnection) {
        $this->conn = $dbConnection;
    }
    public function register(
        string $username,
        string $email,
        string $password,
        ?string $salutation = null,
        ?string $firstname = null,
        ?string $lastname = null,
        ?string $address = null,
        ?string $zip = null,
        ?string $city = null,
        ?string $paymentInfo = null
    ): bool {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users 
        (salutation, firstname, lastname, address, zip, city, email, username, password, payment_info, role)
        VALUES
        (:salutation, :firstname, :lastname, :address, :zip, :city, :email, :username, :password, :payment_info, 'customer')";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":salutation", $salutation);
        $stmt->bindParam(":firstname", $firstname);
        $stmt->bindParam(":lastname", $lastname);
        $stmt->bindParam(":address", $address);
        $stmt->bindParam(":zip", $zip);
        $stmt->bindParam(":city", $city);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":payment_info", $paymentInfo);

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