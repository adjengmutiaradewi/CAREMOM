<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    $functions = new Functions();

    if ($functions->login($username, $password)) {
        echo json_encode([
            'success' => true,
            'message' => 'Login berhasil',
            'role' => $_SESSION['role'],
            'username' => $_SESSION['username']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Username atau password salah'
        ]);
    }
}
