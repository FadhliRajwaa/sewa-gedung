<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $query = "SELECT p.id_pemesanan, 
                     CASE 
                         WHEN py.tipe_penyewa = 'instansi' THEN py.nama_instansi 
                         ELSE py.nama_lengkap 
                     END as nama_penyewa,
                     a.nama_acara, 
                     p.tanggal_sewa, 
                     p.tanggal_selesai, 
                     p.total, 
                     COALESCE(pb.status_pembayaran, 'Belum Lunas') as status_pembayaran,
                     p.tanggal_pesan,
                     p.metode_pembayaran,
                     p.tipe_pesanan
              FROM pemesanan p
              LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa
              LEFT JOIN acara a ON p.id_acara = a.id_acara
              LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
              ORDER BY p.tanggal_pesan DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
