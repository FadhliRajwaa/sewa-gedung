<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once '../../config.php';

try {
    $query = "SELECT 
                p.id_pemesanan as id,
                p.tanggal_sewa as tanggal_acara,
                a.nama_acara,
                CASE 
                    WHEN pen.tipe_penyewa = 'instansi' THEN pen.nama_instansi
                    ELSE pen.nama_lengkap
                END as nama_penyewa,
                pen.email,
                pen.no_telepon,
                p.total as total_biaya,
                pb.status_pembayaran,
                p.tanggal_pesan
              FROM pemesanan p
              LEFT JOIN acara a ON p.id_acara = a.id_acara
              LEFT JOIN penyewa pen ON p.id_penyewa = pen.id_penyewa
              LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
              ORDER BY p.tanggal_sewa DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [];
    foreach ($results as $row) {
        $data[] = [
            'id' => (int)$row['id'],
            'tanggal_acara' => $row['tanggal_acara'],
            'nama_acara' => $row['nama_acara'],
            'nama_penyewa' => $row['nama_penyewa'],
            'email' => $row['email'],
            'no_telepon' => $row['no_telepon'],
            'total_biaya' => (int)$row['total_biaya'],
            'total' => (int)$row['total_biaya'],
            'status_pembayaran' => $row['status_pembayaran'],
            'created_at' => $row['tanggal_pesan'],
            'pendapatan' => $row['status_pembayaran'] === 'Lunas' ? (int)$row['total_biaya'] : 0
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($data);
    
} catch (Exception $e) {
    error_log("Error in get_laporan.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
}
?>
