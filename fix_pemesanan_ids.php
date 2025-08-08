<?php
/**
 * Script khusus untuk membersihkan dan mereset data pemesanan
 * Jalankan ini jika ID pemesanan tidak dimulai dari 1
 */

require_once 'config.php';
require_once 'simple_reorder.php';

echo "<h2>🔧 Reset Data Pemesanan dan Pembayaran</h2>";
echo "<p>Script ini akan mengatur ulang semua ID pemesanan dan pembayaran agar dimulai dari 1.</p>";

// Cek data sebelum reset
echo "<h3>📊 Data Sebelum Reset:</h3>";

// Pemesanan
$stmt = $pdo->query("SELECT COUNT(*) as count FROM pemesanan");
$countPemesanan = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT MIN(id_pemesanan) as min_id, MAX(id_pemesanan) as max_id FROM pemesanan");
$dataPemesanan = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<p><strong>Pemesanan:</strong> $countPemesanan data, ID range: " . ($dataPemesanan['min_id'] ?? 'kosong') . " - " . ($dataPemesanan['max_id'] ?? 'kosong') . "</p>";

// Pembayaran
$stmt = $pdo->query("SELECT COUNT(*) as count FROM pembayaran");
$countPembayaran = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT MIN(id_pembayaran) as min_id, MAX(id_pembayaran) as max_id FROM pembayaran");
$dataPembayaran = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<p><strong>Pembayaran:</strong> $countPembayaran data, ID range: " . ($dataPembayaran['min_id'] ?? 'kosong') . " - " . ($dataPembayaran['max_id'] ?? 'kosong') . "</p>";

// Auto increment status
$stmt = $pdo->query("SHOW TABLE STATUS LIKE 'pemesanan'");
$infoPemesanan = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = $pdo->query("SHOW TABLE STATUS LIKE 'pembayaran'");
$infoPembayaran = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<p><strong>AUTO_INCREMENT Pemesanan:</strong> " . $infoPemesanan['Auto_increment'] . "</p>";
echo "<p><strong>AUTO_INCREMENT Pembayaran:</strong> " . $infoPembayaran['Auto_increment'] . "</p>";

echo "<hr>";

// Lakukan reset
echo "<h3>🔄 Melakukan Reset...</h3>";

try {
    // Reset pemesanan
    echo "<p>🔄 Mereset ID Pemesanan...</p>";
    $resultPemesanan = simpleReorderPemesananIds();
    if ($resultPemesanan) {
        echo "<p style='color: green;'>✅ ID Pemesanan berhasil direset!</p>";
    } else {
        echo "<p style='color: red;'>❌ Gagal mereset ID Pemesanan!</p>";
    }
    
    // Reset pembayaran
    echo "<p>🔄 Mereset ID Pembayaran...</p>";
    $resultPembayaran = simpleReorderPembayaranIds();
    if ($resultPembayaran) {
        echo "<p style='color: green;'>✅ ID Pembayaran berhasil direset!</p>";
    } else {
        echo "<p style='color: red;'>❌ Gagal mereset ID Pembayaran!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Cek data setelah reset
echo "<h3>📊 Data Setelah Reset:</h3>";

// Pemesanan
$stmt = $pdo->query("SELECT COUNT(*) as count FROM pemesanan");
$countPemesanan = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT MIN(id_pemesanan) as min_id, MAX(id_pemesanan) as max_id FROM pemesanan");
$dataPemesanan = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<p><strong>Pemesanan:</strong> $countPemesanan data, ID range: " . ($dataPemesanan['min_id'] ?? 'kosong') . " - " . ($dataPemesanan['max_id'] ?? 'kosong') . "</p>";

// Pembayaran
$stmt = $pdo->query("SELECT COUNT(*) as count FROM pembayaran");
$countPembayaran = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT MIN(id_pembayaran) as min_id, MAX(id_pembayaran) as max_id FROM pembayaran");
$dataPembayaran = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<p><strong>Pembayaran:</strong> $countPembayaran data, ID range: " . ($dataPembayaran['min_id'] ?? 'kosong') . " - " . ($dataPembayaran['max_id'] ?? 'kosong') . "</p>";

// Auto increment status
$stmt = $pdo->query("SHOW TABLE STATUS LIKE 'pemesanan'");
$infoPemesanan = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = $pdo->query("SHOW TABLE STATUS LIKE 'pembayaran'");
$infoPembayaran = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<p><strong>AUTO_INCREMENT Pemesanan:</strong> " . $infoPemesanan['Auto_increment'] . "</p>";
echo "<p><strong>AUTO_INCREMENT Pembayaran:</strong> " . $infoPembayaran['Auto_increment'] . "</p>";

echo "<hr>";

// Tampilkan data pemesanan yang ada
if ($countPemesanan > 0) {
    echo "<h3>📋 Daftar Pemesanan Setelah Reset:</h3>";
    $stmt = $pdo->query("SELECT p.id_pemesanan, py.nama_lengkap, a.nama_acara, p.tanggal_sewa, p.tanggal_pesan 
                        FROM pemesanan p 
                        LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa 
                        LEFT JOIN acara a ON p.id_acara = a.id_acara 
                        ORDER BY p.id_pemesanan");
    $pemesananList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
    echo "<tr><th>ID</th><th>Penyewa</th><th>Acara</th><th>Tanggal Sewa</th><th>Tanggal Pesan</th></tr>";
    foreach ($pemesananList as $pemesanan) {
        echo "<tr>";
        echo "<td>" . $pemesanan['id_pemesanan'] . "</td>";
        echo "<td>" . htmlspecialchars($pemesanan['nama_lengkap'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($pemesanan['nama_acara'] ?? 'N/A') . "</td>";
        echo "<td>" . $pemesanan['tanggal_sewa'] . "</td>";
        echo "<td>" . $pemesanan['tanggal_pesan'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h3>🎉 Selesai!</h3>";
echo "<p><strong>Sekarang coba tambah pemesanan baru:</strong></p>";
echo "<ul>";
echo "<li>Pemesanan baru akan mendapat ID yang berurutan dari 1</li>";
echo "<li>Jika ada 2 data, ID akan jadi 1 dan 2</li>";
echo "<li>Data baru berikutnya akan mendapat ID 3, 4, dst.</li>";
echo "</ul>";

echo "<p><a href='admin/data_pemesanan.php' target='_blank'>📋 Buka Halaman Data Pemesanan</a></p>";
?>
