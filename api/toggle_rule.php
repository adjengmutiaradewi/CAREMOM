<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$functions = new Functions();
if (!$functions->isLoggedIn() || !$functions->isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$database = new Database();
$conn = $database->getConnection();

try {
    $query = "UPDATE aturan_forward_chaining SET is_active = :is_active WHERE id = :rule_id";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':is_active' => $input['is_active'] ? 1 : 0,
        ':rule_id' => $input['rule_id']
    ]);

    echo json_encode(['success' => true, 'message' => 'Rule updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error updating rule: ' . $e->getMessage()]);
}
