<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Total Penyewa
    $totalPenyewaQuery = "SELECT COUNT(*) as count FROM penyewa";
    $stmt = $pdo->prepare($totalPenyewaQuery);
    $stmt->execute();
    $totalPenyewa = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total Pemesanan
    $totalPemesananQuery = "SELECT COUNT(*) as count FROM pemesanan";
    $stmt = $pdo->prepare($totalPemesananQuery);
    $stmt->execute();
    $totalPemesanan = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total Pendapatan (hanya dari pembayaran yang lunas)
    $totalPendapatanQuery = "SELECT SUM(p.total) as total 
                           FROM pemesanan p 
                           LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan 
                           WHERE pb.status_pembayaran = 'Lunas'";
    $stmt = $pdo->prepare($totalPendapatanQuery);
    $stmt->execute();
    $totalPendapatan = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Pemesanan Bulan Ini
    $pemesananBulanIniQuery = "SELECT COUNT(*) as count FROM pemesanan 
                               WHERE MONTH(tanggal_pesan) = MONTH(CURRENT_DATE()) 
                               AND YEAR(tanggal_pesan) = YEAR(CURRENT_DATE())";
    $stmt = $pdo->prepare($pemesananBulanIniQuery);
    $stmt->execute();
    $pemesananBulanIni = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Data Chart - Pendapatan per bulan (6 bulan terakhir) dari pembayaran lunas
    $chartQuery = "SELECT 
                    MONTH(p.tanggal_pesan) as bulan,
                    YEAR(p.tanggal_pesan) as tahun,
                    SUM(p.total) as pendapatan
                   FROM pemesanan p
                   LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan 
                   WHERE pb.status_pembayaran = 'Lunas'
                   AND p.tanggal_pesan >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
                   GROUP BY YEAR(p.tanggal_pesan), MONTH(p.tanggal_pesan)
                   ORDER BY tahun, bulan";
    $stmt = $pdo->prepare($chartQuery);
    $stmt->execute();
    $chartResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Prepare chart data for last 6 months
    $chartData = [];
    $chartLabels = [];
    $currentDate = new DateTime();
    
    // Array nama bulan dalam bahasa Indonesia
    $bulanIndonesia = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];
    
    for ($i = 5; $i >= 0; $i--) {
        $date = clone $currentDate;
        $date->sub(new DateInterval('P' . $i . 'M'));
        $month = $date->format('n');
        $year = $date->format('Y');
        
        // Add label
        $chartLabels[] = $bulanIndonesia[$month] . ' ' . $year;
        
        $found = false;
        foreach ($chartResult as $data) {
            if ($data['bulan'] == $month && $data['tahun'] == $year) {
                $chartData[] = (float)$data['pendapatan'];
                $found = true;
                break;
            }
        }
        if (!$found) {
            $chartData[] = 0;
        }
    }
    
    $result = [
        'total_penyewa' => $totalPenyewa,
        'total_pemesanan' => $totalPemesanan,
        'total_pendapatan' => $totalPendapatan,
        'pemesanan_bulan_ini' => $pemesananBulanIni,
        'chart_data' => [
            'labels' => $chartLabels,
            'values' => $chartData
        ]
    ];
    
    echo json_encode($result);
    
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
