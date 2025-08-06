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
    // Total pendapatan dari acara yang sudah lunas
    $query = "SELECT SUM(harga_paket) as total_revenue 
              FROM acara 
              WHERE status_pembayaran = 'Lunas'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;
    
    // Total transaksi lunas
    $query = "SELECT COUNT(*) as total_transactions 
              FROM acara 
              WHERE status_pembayaran = 'Lunas'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $totalTransactions = $stmt->fetch(PDO::FETCH_ASSOC)['total_transactions'] ?? 0;
    
    // Rata-rata pendapatan per hari (30 hari terakhir)
    $query = "SELECT AVG(daily_revenue) as avg_daily
              FROM (
                  SELECT DATE(tanggal_acara) as date, 
                         SUM(harga_paket) as daily_revenue
                  FROM acara 
                  WHERE status_pembayaran = 'Lunas' 
                  AND tanggal_acara >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)
                  GROUP BY DATE(tanggal_acara)
              ) daily_stats";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $avgDaily = $stmt->fetch(PDO::FETCH_ASSOC)['avg_daily'] ?? 0;
    
    // Gedung/acara paling populer
    $query = "SELECT 
                  CASE 
                      WHEN nama_acara LIKE '%pernikahan%' OR nama_acara LIKE '%nikah%' THEN 'Gedung Pernikahan'
                      WHEN nama_acara LIKE '%rapat%' OR nama_acara LIKE '%meeting%' THEN 'Gedung Rapat'
                      WHEN nama_acara LIKE '%seminar%' OR nama_acara LIKE '%workshop%' THEN 'Gedung Seminar'
                      ELSE 'Gedung Umum'
                  END as venue_type,
                  COUNT(*) as booking_count
              FROM acara 
              WHERE status_pembayaran = 'Lunas'
              GROUP BY venue_type
              ORDER BY booking_count DESC
              LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $popularVenue = $stmt->fetch(PDO::FETCH_ASSOC);
    $popularVenueName = $popularVenue ? $popularVenue['venue_type'] : 'Tidak ada data';
    
    header('Content-Type: application/json');
    echo json_encode([
        'total_revenue' => (int)$totalRevenue,
        'total_transactions' => (int)$totalTransactions,
        'avg_daily' => (int)$avgDaily,
        'popular_venue' => $popularVenueName
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_laporan_stats.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
}
?>
