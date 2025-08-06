<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $query = "SELECT id_penyewa, 
                     CASE 
                         WHEN tipe_penyewa = 'instansi' THEN nama_instansi 
                         ELSE nama_lengkap 
                     END as nama,
                     email, 
                     no_telepon as telepon,
                     alamat, 
                     tipe_penyewa as jenis,
                     email_terverifikasi
              FROM penyewa 
              ORDER BY id_penyewa DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
