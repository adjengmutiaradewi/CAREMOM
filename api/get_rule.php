<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$functions = new Functions();
if (!$functions->isLoggedIn() || !$functions->isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$rule_id = $_GET['id'] ?? 0;

$database = new Database();
$conn = $database->getConnection();

$query = "SELECT * FROM aturan_forward_chaining WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->execute([':id' => $rule_id]);
$rule = $stmt->fetch(PDO::FETCH_ASSOC);

if ($rule) {
    echo json_encode($rule);
} else {
    echo json_encode(['success' => false, 'message' => 'Rule not found']);
}
