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
    // Query untuk mengambil data riwayat pemesanan (acara yang sudah selesai)
    $sql = "
        SELECT 
            acara.id,
            acara.tanggal_acara,
            acara.nama_acara,
            users.nama as nama_penyewa,
            users.email,
            users.no_telepon,
            COALESCE(acara.harga_paket, 0) as total_biaya,
            acara.status_pembayaran as status_pemesanan,
            acara.created_at
        FROM acara 
        LEFT JOIN users ON acara.user_id = users.id 
        WHERE acara.tanggal_acara IS NOT NULL
        AND (acara.status_pembayaran = 'Lunas' OR acara.tanggal_acara < CURDATE())
        ORDER BY acara.tanggal_acara DESC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format data
    $data = [];
    foreach ($result as $row) {
        $status = $row['status_pemesanan'];
        if ($row['tanggal_acara'] < date('Y-m-d') && $status !== 'Lunas') {
            $status = 'completed';
        } elseif ($status === 'Lunas') {
            $status = 'confirmed';
        }
        
        $data[] = [
            'id' => $row['id'],
            'tanggal_acara' => $row['tanggal_acara'],
            'nama_acara' => $row['nama_acara'],
            'nama_penyewa' => $row['nama_penyewa'],
            'email' => $row['email'],
            'no_telepon' => $row['no_telepon'],
            'total_biaya' => (int)$row['total_biaya'],
            'harga_paket' => (int)$row['total_biaya'], // alias untuk compatibility
            'status_pemesanan' => $status,
            'status_pembayaran' => $row['status_pemesanan'], // original status
            'created_at' => $row['created_at']
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($data);
    
} catch (Exception $e) {
    error_log("Error in get_riwayat.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
}
?>
