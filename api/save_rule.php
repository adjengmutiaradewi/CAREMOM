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
    if (empty($input['rule_id'])) {
        // Insert new rule
        $query = "INSERT INTO aturan_forward_chaining (kode_aturan, nama_aturan, kondisi, aksi, keterangan) 
                  VALUES (:kode_aturan, :nama_aturan, :kondisi, :aksi, :keterangan)";
    } else {
        // Update existing rule
        $query = "UPDATE aturan_forward_chaining 
                  SET kode_aturan = :kode_aturan, nama_aturan = :nama_aturan, 
                      kondisi = :kondisi, aksi = :aksi, keterangan = :keterangan 
                  WHERE id = :rule_id";
    }

    $stmt = $conn->prepare($query);
    $params = [
        ':kode_aturan' => $input['kode_aturan'],
        ':nama_aturan' => $input['nama_aturan'],
        ':kondisi' => $input['kondisi'],
        ':aksi' => $input['aksi'],
        ':keterangan' => $input['keterangan']
    ];

    if (!empty($input['rule_id'])) {
        $params[':rule_id'] = $input['rule_id'];
    }

    $stmt->execute($params);

    echo json_encode(['success' => true, 'message' => 'Rule saved successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error saving rule: ' . $e->getMessage()]);
}
