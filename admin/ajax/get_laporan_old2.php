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
            acara.id,
            acara.tanggal_acara,
            acara.nama_acara,
            users.nama as nama_penyewa,
            users.email,
            users.no_telepon,
            COALESCE(acara.harga_paket, 0) as total_biaya,
            acara.status_pembayaran,
            acara.created_at,
            CASE 
                WHEN acara.status_pembayaran = 'Lunas' THEN COALESCE(acara.harga_paket, 0)
                ELSE 0
            END as pendapatan
        FROM acara 
        LEFT JOIN users ON acara.user_id = users.id 
        WHERE acara.tanggal_acara IS NOT NULL
        ORDER BY acara.tanggal_acara DESC
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
            'pendapatan' => (int)$row['pendapatan']
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
            'acara_populer' => $acaraPopuler
        ],
        'chart_data' => [
            'labels' => $chartLabels,
            'values' => $chartValues
        ],
        'pemesanan_data' => $pemesananData
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
