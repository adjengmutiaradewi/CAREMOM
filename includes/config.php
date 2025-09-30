<?php
// Tambahkan di atas file config.php untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Database
{
    private $host = "localhost";
    private $db_name = "caremom";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Debug: Uncomment line below untuk melihat pesan sukses
            // error_log("Database connected successfully");

        } catch (PDOException $exception) {
            // Tampilkan error detail untuk debugging
            error_log("Connection error: " . $exception->getMessage());
            throw $exception; // Re-throw exception agar bisa ditangkap di tempat lain
        }
        return $this->conn;
    }
}

// Session start
session_start();
