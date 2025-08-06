<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Total pendapatan
    $totalPendapatanQuery = "SELECT SUM(total_biaya) as total_pendapatan FROM pemesanan 
                             WHERE status_pemesanan IN ('confirmed', 'completed')";
    $stmt = $pdo->prepare($totalPendapatanQuery);
    $stmt->execute();
    $totalPendapatan = $stmt->fetch(PDO::FETCH_ASSOC)['total_pendapatan'] ?? 0;
    
    // Pendapatan bulan ini
    $pendapatanBulanIniQuery = "SELECT SUM(total_biaya) as pendapatan FROM pemesanan 
                                WHERE status_pemesanan IN ('confirmed', 'completed')
                                AND MONTH(tanggal_pemesanan) = MONTH(CURRENT_DATE()) 
                                AND YEAR(tanggal_pemesanan) = YEAR(CURRENT_DATE())";
    $stmt = $pdo->prepare($pendapatanBulanIniQuery);
    $stmt->execute();
    $pendapatanBulanIni = $stmt->fetch(PDO::FETCH_ASSOC)['pendapatan'] ?? 0;
    
    // Total pemesanan
    $totalPemesananQuery = "SELECT COUNT(*) as total FROM pemesanan";
    $stmt = $pdo->prepare($totalPemesananQuery);
    $stmt->execute();
    $totalPemesanan = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Pemesanan selesai
    $pemesananSelesaiQuery = "SELECT COUNT(*) as selesai FROM pemesanan WHERE status_pemesanan = 'completed'";
    $stmt = $pdo->prepare($pemesananSelesaiQuery);
    $stmt->execute();
    $pemesananSelesai = $stmt->fetch(PDO::FETCH_ASSOC)['selesai'];
    
    // Pemesanan bulan ini
    $pemesananBulanIniQuery = "SELECT COUNT(*) as count FROM pemesanan 
                               WHERE MONTH(tanggal_pemesanan) = MONTH(CURRENT_DATE()) 
                               AND YEAR(tanggal_pemesanan) = YEAR(CURRENT_DATE())";
    $stmt = $pdo->prepare($pemesananBulanIniQuery);
    $stmt->execute();
    $pemesananBulanIni = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Rata-rata per pemesanan
    $rataRata = $totalPemesanan > 0 ? $totalPendapatan / $totalPemesanan : 0;
    
    // Count by venue type
    $pernikahanQuery = "SELECT COUNT(*) as count FROM pemesanan p 
                        LEFT JOIN acara a ON p.id_acara = a.id_acara 
                        WHERE a.nama_acara LIKE '%pernikahan%'";
    $stmt = $pdo->prepare($pernikahanQuery);
    $stmt->execute();
    $pernikahanCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $rapatQuery = "SELECT COUNT(*) as count FROM pemesanan p 
                   LEFT JOIN acara a ON p.id_acara = a.id_acara 
                   WHERE a.nama_acara LIKE '%rapat%'";
    $stmt = $pdo->prepare($rapatQuery);
    $stmt->execute();
    $rapatCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $seminarQuery = "SELECT COUNT(*) as count FROM pemesanan p 
                     LEFT JOIN acara a ON p.id_acara = a.id_acara 
                     WHERE a.nama_acara LIKE '%seminar%'";
    $stmt = $pdo->prepare($seminarQuery);
    $stmt->execute();
    $seminarCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Gedung terpopuler
    $gedungTerpopulerQuery = "SELECT a.nama_acara, COUNT(*) as count FROM pemesanan p 
                              LEFT JOIN acara a ON p.id_acara = a.id_acara 
                              GROUP BY a.id_acara 
                              ORDER BY count DESC LIMIT 1";
    $stmt = $pdo->prepare($gedungTerpopulerQuery);
    $stmt->execute();
    $gedungTerpopuler = $stmt->fetch(PDO::FETCH_ASSOC)['nama_acara'] ?? 'N/A';
    
    $result = [
        'total_pendapatan' => $totalPendapatan,
        'pendapatan_bulan_ini' => $pendapatanBulanIni,
        'rata_rata' => $rataRata,
        'total_pemesanan' => $totalPemesanan,
        'pemesanan_selesai' => $pemesananSelesai,
        'pemesanan_bulan_ini' => $pemesananBulanIni,
        'pernikahan_count' => $pernikahanCount,
        'rapat_count' => $rapatCount,
        'seminar_count' => $seminarCount,
        'gedung_terpopuler' => $gedungTerpopuler
    ];
    
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
