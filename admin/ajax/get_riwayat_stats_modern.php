<?php
session_start();
require_once '../../config.php';

// Check admin authentication
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

try {
    // Stats queries
    $stats = [];
    
    // Total riwayat
    $total_query = "SELECT COUNT(*) as total FROM pemesanan";
    $total_result = mysqli_query($conn, $total_query);
    $stats['total_riwayat'] = mysqli_fetch_assoc($total_result)['total'] ?? 0;
    
    // Bulan ini
    $bulan_query = "SELECT COUNT(*) as bulan FROM pemesanan WHERE MONTH(tanggal_acara) = MONTH(CURDATE()) AND YEAR(tanggal_acara) = YEAR(CURDATE())";
    $bulan_result = mysqli_query($conn, $bulan_query);
    $stats['bulan_ini'] = mysqli_fetch_assoc($bulan_result)['bulan'] ?? 0;
    
    // Total pendapatan
    $pendapatan_query = "SELECT SUM(total_biaya) as pendapatan FROM pemesanan WHERE status_pembayaran IN ('Lunas', 'Completed', 'Confirmed')";
    $pendapatan_result = mysqli_query($conn, $pendapatan_query);
    $stats['total_pendapatan'] = mysqli_fetch_assoc($pendapatan_result)['pendapatan'] ?? 0;
    
    // Rata-rata transaksi
    $rata_rata = $stats['total_riwayat'] > 0 ? $stats['total_pendapatan'] / $stats['total_riwayat'] : 0;
    $stats['rata_rata'] = $rata_rata;
    
    echo json_encode($stats);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
