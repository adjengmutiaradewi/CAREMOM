<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

$functions = new Functions();

echo json_encode([
    'logged_in' => $functions->isLoggedIn(),
    'is_admin' => $functions->isAdmin(),
    'username' => $_SESSION['username'] ?? ''
]);
