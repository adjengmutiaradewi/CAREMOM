<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $username = $input['username'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';

    // Validasi input
    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Semua field harus diisi'
        ]);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode([
            'success' => false,
            'message' => 'Password harus minimal 6 karakter'
        ]);
        exit;
    }

    $database = new Database();
    $conn = $database->getConnection();

    try {
        // Check if username already exists
        $query = "SELECT id FROM users WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->execute([':username' => $username]);

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Username sudah digunakan'
            ]);
            exit;
        }

        // Check if email already exists
        $query = "SELECT id FROM users WHERE email = :email";
        $stmt = $conn->prepare($query);
        $stmt->execute([':email' => $email]);

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Email sudah digunakan'
            ]);
            exit;
        }

        // Insert new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'user')";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Registrasi berhasil'
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}
