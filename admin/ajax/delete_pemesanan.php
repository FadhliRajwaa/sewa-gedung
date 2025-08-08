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
    // Delete pembayaran first (foreign key constraint)
    $queryPembayaran = "DELETE FROM pembayaran WHERE id_pemesanan = ?";
    $stmtPembayaran = $pdo->prepare($queryPembayaran);
    $stmtPembayaran->execute([$id]);
    
    // Delete pemesanan
    $queryPemesanan = "DELETE FROM pemesanan WHERE id_pemesanan = ?";
    $stmtPemesanan = $pdo->prepare($queryPemesanan);
    $result = $stmtPemesanan->execute([$id]);
    
    if ($result) {
        // Reorder IDs for both tables to maintain sequential numbering
        simpleReorderPembayaranIds();
        simpleReorderPemesananIds();
        
        echo json_encode(['success' => true, 'message' => 'Pemesanan berhasil dihapus dan ID telah diurutkan ulang']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus pemesanan']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
