<?php
require_once '../../config.php';
header('Content-Type: application/json');

try {
    // Insert sample data untuk testing
    
    // 1. Insert sample acara
    $acaraData = [
        ['Pernikahan Adat Jawa', 100, 5000000, 'Gedung Utama'],
        ['Rapat Tahunan', 50, 2000000, 'Gedung Rapat'],
        ['Seminar Teknologi', 200, 3000000, 'Gedung Seminar'],
        ['Pernikahan Modern', 150, 6000000, 'Gedung Utama']
    ];
    
    foreach ($acaraData as $acara) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO acara (nama_acara, kapasitas, harga, lokasi) VALUES (?, ?, ?, ?)");
        $stmt->execute($acara);
    }
    
    // 2. Insert sample penyewa
    $penyewaData = [
        ['individu', null, 'John Doe', '1234567890123456', '081234567890', 'john@email.com', 'Jakarta', 'john_doe', password_hash('password123', PASSWORD_DEFAULT)],
        ['instansi', 'PT ABC Corp', 'Manager ABC', null, '081234567891', 'manager@abc.com', 'Bandung', 'abc_corp', password_hash('password123', PASSWORD_DEFAULT)],
        ['individu', null, 'Jane Smith', '1234567890123457', '081234567892', 'jane@email.com', 'Surabaya', 'jane_smith', password_hash('password123', PASSWORD_DEFAULT)]
    ];
    
    foreach ($penyewaData as $penyewa) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO penyewa (tipe_penyewa, nama_instansi, nama_lengkap, nik, no_telepon, email, alamat, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute($penyewa);
    }
    
    // 3. Insert sample pemesanan
    $stmt = $pdo->query("SELECT id_acara FROM acara LIMIT 4");
    $acaraIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $stmt = $pdo->query("SELECT id_penyewa FROM penyewa LIMIT 3");
    $penyewaIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($acaraIds) && !empty($penyewaIds)) {
        $pemesananData = [
            [$penyewaIds[0], $acaraIds[0], '2025-08-10', '2025-08-10', 1, 'Dekorasi tambahan', 5000000, 'Transfer_BCA'],
            [$penyewaIds[1], $acaraIds[1], '2025-08-15', '2025-08-15', 1, 'Sound system', 2000000, 'QRIS'],
            [$penyewaIds[2], $acaraIds[2], '2025-08-20', '2025-08-20', 1, 'Proyektor', 3000000, 'Transfer_BNI'],
            [$penyewaIds[0], $acaraIds[3], '2025-07-25', '2025-07-25', 1, 'Catering', 6000000, 'Transfer_Mandiri']
        ];
        
        foreach ($pemesananData as $pemesanan) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO pemesanan (id_penyewa, id_acara, tanggal_sewa, tanggal_selesai, durasi, kebutuhan_tambahan, total, metode_pembayaran) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute($pemesanan);
        }
        
        // 4. Insert sample pembayaran
        $stmt = $pdo->query("SELECT id_pemesanan FROM pemesanan");
        $pemesananIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $statusOptions = ['Lunas', 'Belum Lunas'];
        
        foreach ($pemesananIds as $id) {
            $status = $statusOptions[array_rand($statusOptions)];
            $stmt = $pdo->prepare("INSERT IGNORE INTO pembayaran (id_pemesanan, status_pembayaran, tanggal_upload) VALUES (?, ?, NOW())");
            $stmt->execute([$id, $status]);
        }
    }
    
    // Get counts
    $stmt = $pdo->query("SELECT COUNT(*) FROM acara");
    $acaraCount = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM penyewa");
    $penyewaCount = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM pemesanan");
    $pemesananCount = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM pembayaran");
    $pembayaranCount = $stmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'message' => 'Sample data berhasil ditambahkan',
        'counts' => [
            'acara' => $acaraCount,
            'penyewa' => $penyewaCount,
            'pemesanan' => $pemesananCount,
            'pembayaran' => $pembayaranCount
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
