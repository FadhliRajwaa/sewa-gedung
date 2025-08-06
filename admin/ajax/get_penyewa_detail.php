<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$id = $_GET['id'] ?? 0;

try {
    $query = "SELECT id_penyewa, 
                     CASE 
                         WHEN tipe_penyewa = 'instansi' THEN nama_instansi 
                         ELSE nama_lengkap 
                     END as nama,
                     email, 
                     no_telepon as telepon, 
                     alamat, 
                     tipe_penyewa as jenis_penyewa
              FROM penyewa WHERE id_penyewa = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(['error' => 'Data tidak ditemukan']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
