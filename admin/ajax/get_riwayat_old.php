<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$status = $_GET['status'] ?? '';
$date = $_GET['date'] ?? '';

try {
    $query = "SELECT p.id_pemesanan, p.tanggal_pesan as tanggal_pemesanan, 
                     CASE 
                         WHEN py.tipe_penyewa = 'instansi' THEN py.nama_instansi 
                         ELSE py.nama_lengkap 
                     END as nama_penyewa,
              a.nama_acara, p.total as total_biaya, 
              COALESCE(pb.status_pembayaran, 'Belum Lunas') as status_pembayaran,
              p.tanggal_sewa as tanggal_acara
              FROM pemesanan p
              LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa
              LEFT JOIN acara a ON p.id_acara = a.id_acara
              LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
              WHERE 1=1";
    
    $params = [];
    
    if (!empty($status)) {
        $query .= " AND pb.status_pembayaran = ?";
        $params[] = $status;
    }
    
    if (!empty($date)) {
        $query .= " AND DATE(p.tanggal_pesan) = ?";
        $params[] = $date;
    }
    
    $query .= " ORDER BY p.tanggal_pesan DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
