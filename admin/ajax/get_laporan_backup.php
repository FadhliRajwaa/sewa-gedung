<?php
session_start();
require_once '../../config.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    // Query untuk mengambil data laporan penyewaan
    $sql = "
        SELECT 
            p.id_pemesanan as id,
            p.tanggal_sewa as tanggal_acara,
            a.nama_acara,
            CASE 
                WHEN py.tipe_penyewa = 'instansi' THEN py.nama_instansi
                ELSE py.nama_lengkap
            END as nama_penyewa,
            py.email,
            py.no_telepon,
            p.total as total_biaya,
            COALESCE(pb.status_pembayaran, 'Belum Lunas') as status_pembayaran,
            p.tanggal_pesan as created_at
        FROM pemesanan p
        LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa
        LEFT JOIN acara a ON p.id_acara = a.id_acara
        LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
        WHERE p.tanggal_sewa IS NOT NULL
        ORDER BY p.tanggal_sewa DESC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format data
    $data = [];
    foreach ($result as $row) {
        $data[] = [
            'id' => $row['id'],
            'tanggal_acara' => $row['tanggal_acara'],
            'nama_acara' => $row['nama_acara'],
            'nama_penyewa' => $row['nama_penyewa'],
            'email' => $row['email'],
            'no_telepon' => $row['no_telepon'],
            'total_biaya' => (int)$row['total_biaya'],
            'total' => (int)$row['total_biaya'], // alias untuk compatibility
            'status_pembayaran' => $row['status_pembayaran'],
            'created_at' => $row['created_at'],
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
