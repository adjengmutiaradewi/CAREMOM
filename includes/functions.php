<?php
require_once 'config.php';

class Functions
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Hitung IMT
    public function hitungIMT($tinggi, $berat)
    {
        $tinggi_m = $tinggi / 100;
        return $berat / ($tinggi_m * $tinggi_m);
    }

    // Tentukan kategori IMT
    public function kategoriIMT($imt)
    {
        if ($imt < 18.5) return 'Underweight';
        if ($imt >= 18.5 && $imt < 25) return 'Normal';
        if ($imt >= 25 && $imt < 30) return 'Overweight';
        return 'Obese';
    }

    // Tentukan trimester berdasarkan usia kehamilan
    public function hitungTrimester($usia_kehamilan)
    {
        if ($usia_kehamilan <= 13) return 1;
        if ($usia_kehamilan <= 27) return 2;
        return 3;
    }

    // Login user
    public function login($username, $password)
    {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                return true;
            }
        }
        return false;
    }

    // Check if user is logged in
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    // Check if user is admin
    public function isAdmin()
    {
        return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
    }

    // Redirect function
    public function redirect($url)
    {
        header("Location: $url");
        exit();
    }

    // Get user data
    public function getUserData($user_id)
    {
        $query = "SELECT * FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function register($username, $email, $password)
    {
        // Check if username exists
        $query = "SELECT id FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Username sudah digunakan'];
        }

        // Check if email exists
        $query = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Email sudah digunakan'];
        }

        // Insert new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'user')";
        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashedPassword
            ]);

            return ['success' => true, 'message' => 'Registrasi berhasil'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
}
