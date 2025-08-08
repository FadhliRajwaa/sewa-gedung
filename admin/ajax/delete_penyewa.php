<?php
session_start();
require_once '../../config.php';
require_once '../../simple_reorder.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? 0;

try {
    $query = "DELETE FROM penyewa WHERE id_penyewa = ?";
    $stmt = $pdo->prepare($query);
    $result = $stmt->execute([$id]);
    
    if ($result) {
        // Reorder all IDs to ensure sequential numbering
        simpleReorderPenyewaIds();
        
        echo json_encode(['success' => true, 'message' => 'Penyewa berhasil dihapus dan ID telah diurutkan ulang']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus penyewa']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
