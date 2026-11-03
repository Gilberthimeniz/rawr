<?php
require_once __DIR__ . '/../config/Database.php';

class Bankaccount extends Database {
    public function __construct() {
        $this->connect();
    }

    // id is AUTO_INCREMENT, so it is not inserted
    public function addBankaccount($brand, $name, $email, $phone, $registrationNumber) {
        $stmt = $this->conn->prepare(
            "INSERT INTO bankaccount (brand, name, email, phone, registrationNumber) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssss", $brand, $name, $email, $phone, $registrationNumber);
        return $stmt->execute();
    }

    public function updateBankaccount($id, $brand, $name, $email, $phone, $registrationNumber) {
        $stmt = $this->conn->prepare(
            "UPDATE bankaccount SET brand = ?, name = ?, email = ?, phone = ?, registrationNumber = ? WHERE id = ?"
        );
        $stmt->bind_param("sssssi", $brand, $name, $email, $phone, $registrationNumber, $id);
        return $stmt->execute();
    }

    public function deleteBankaccount($id) {
        $stmt = $this->conn->prepare("DELETE FROM bankaccount WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getBankaccount($id) {
        $stmt = $this->conn->prepare("SELECT * FROM bankaccount WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc(); // null if not found
    }

    public function getAllBankaccounts() {
        return $this->conn->query("SELECT * FROM bankaccount ORDER BY name")->fetch_all(MYSQLI_ASSOC);
    }

    public function countBankaccounts() {
        $row = $this->conn->query("SELECT COUNT(*) AS total FROM bankaccount")->fetch_assoc();
        return (int)$row['total'];
    }
}
