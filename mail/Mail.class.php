<?php

class Mail {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    function generatePassword($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function ifExists($email) {
        try {
            $query = "SELECT COUNT(*) FROM `passwd` WHERE id = :email";
            $stmt = $this->conn->connection->prepare($query);
            $stmt->execute([':email' => $email]);
            $rowCount = $stmt->fetchColumn();
            return $rowCount == 1;
        } catch(Exception $e) {
            echo "[!] There are some errors: 
            -> " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function createUser($argv) {
        $email = $argv[1];
        $password = $argv[2];

        if($password === null) {
            echo "[!] You do not entered password -> it will generate automatically!\n";
            $password = $this->generatePassword(8);
        }
            echo "You access data to new E-Mail user: 
                -> E-MaiL: " . $email ." 
                -> Password: " . $password . " \n";

        list($name, $domain) = explode("@", $email);
        try {
            if(!$this->ifExists($email)) {
                $query1 = "INSERT INTO `passwd` (id, clear) VALUES (:email, :pass)";
                $stmtPasswd = $this->conn->connection->prepare($query1);
                $stmtPasswd->bindValue(":email", $email);
                $stmtPasswd->bindValue(":pass", $password);

                $query2 = "INSERT INTO `virtusertable` (user, domain, destination) VALUES (:user, :domain, :email)";
                $stmtVirtusertable = $this->conn->connection->prepare($query2);
                $stmtVirtusertable->bindValue(":email", $email);
                $stmtVirtusertable->bindValue(":user", $name);
                $stmtVirtusertable->bindValue(":domain", $domain);
                $stmtVirtusertable->bindValue(":email", $email);

                if($stmtPasswd->execute() AND $stmtVirtusertable->execute()) {
                    echo "[!] User created successfully!\n";
                }
            } else {
                echo "[!] E-Mail '$email' is already registered!\n";
                $update = "UPDATE `passwd` SET clear = :pass WHERE id = :email";
                $stmtUpdate = $this->conn->connection->prepare($update);
                $stmtUpdate->bindValue(":pass", $password);
                $stmtUpdate->bindValue(":email", $email);
                if($stmtUpdate->execute()) {
                    echo "[!] Password for '$email'' updated successfully!\n";
                }
            }

        } catch(Exception $e) {
            echo "[!] There are some errors: 
                -> " . $e->getMessage() . "\n";
        }
    }




}