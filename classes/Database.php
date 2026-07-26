<?php

class Database {

    private $host = "localhost";
    private $db_name = "biblioteca";
    private $username = "root";
    private $password = "gaby";

    public function getConnection() {

        $conn = null;

        try {

            $conn = new PDO(
                "mysql:host=" . $this->host .
                ";dbname=" . $this->db_name .
                ";charset=utf8",
                $this->username,
                $this->password
            );

            $conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

        } catch(PDOException $e) {

            echo "Error de conexión: " . $e->getMessage();

        }

        return $conn;
    }
}
?>