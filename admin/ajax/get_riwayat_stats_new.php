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
    // Total riwayat (pemesanan selesai)
    $query = "SELECT COUNT(*) as total_riwayat 
              FROM acara 
              WHERE (status_pembayaran = 'Lunas' OR tanggal_acara < CURDATE())";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $totalRiwayat = $stmt->fetch(PDO::FETCH_ASSOC)['total_riwayat'] ?? 0;
    
    // Bulan ini
    $query = "SELECT COUNT(*) as bulan_ini 
              FROM acara 
              WHERE (status_pembayaran = 'Lunas' OR tanggal_acara < CURDATE())
              AND MONTH(tanggal_acara) = MONTH(CURRENT_DATE()) 
              AND YEAR(tanggal_acara) = YEAR(CURRENT_DATE())";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $bulanIni = $stmt->fetch(PDO::FETCH_ASSOC)['bulan_ini'] ?? 0;
    
    // Total pendapatan dari riwayat
    $query = "SELECT SUM(harga_paket) as total_pendapatan 
              FROM acara 
              WHERE status_pembayaran = 'Lunas'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $totalPendapatan = $stmt->fetch(PDO::FETCH_ASSOC)['total_pendapatan'] ?? 0;
    
    // Rata-rata per pemesanan
    $rataRata = $totalRiwayat > 0 ? $totalPendapatan / $totalRiwayat : 0;
    
    header('Content-Type: application/json');
    echo json_encode([
        'total_riwayat' => (int)$totalRiwayat,
        'bulan_ini' => (int)$bulanIni,
        'total_pendapatan' => (int)$totalPendapatan,
        'rata_rata' => (int)$rataRata
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_riwayat_stats.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
}
?>
