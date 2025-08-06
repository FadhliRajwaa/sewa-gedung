<?php
require_once '../config.php';

// Test koneksi database
try {
    echo "<h2>Database Connection Test</h2>";
    echo "Connection: OK<br><br>";
    
    // Test query pemesanan
    echo "<h3>Test Query Pemesanan:</h3>";
    $query = "SELECT p.id_pemesanan, p.tanggal_pesan as tanggal_pemesanan, 
                     CASE 
                         WHEN py.tipe_penyewa = 'instansi' THEN py.nama_instansi 
                         ELSE py.nama_lengkap 
                     END as nama_penyewa,
              a.nama_acara, p.total as total_biaya, 
              COALESCE(pb.status_pembayaran, 'Belum Lunas') as status_pembayaran,
              p.tanggal_sewa as tanggal_acara
              FROM pemesanan p
              LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa
              LEFT JOIN acara a ON p.id_acara = a.id_acara
              LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
              ORDER BY p.tanggal_pesan DESC LIMIT 5";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
    // Test JSON response
    echo "<h3>JSON Response:</h3>";
    echo json_encode($result);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
