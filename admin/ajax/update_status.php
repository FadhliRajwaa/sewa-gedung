<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? 0;
$status = $input['status'] ?? '';

try {
    $query = "UPDATE pemesanan SET status = ? WHERE id_pemesanan = ?";
    $stmt = $pdo->prepare($query);
    $result = $stmt->execute([$status, $id]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Status berhasil diupdate']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupdate status']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
