<?php
/**
 * CLEAN RESET - Hapus semua data pemesanan dan pembayaran, reset AUTO_INCREMENT ke 1
 * HATI-HATI: Script ini akan menghapus SEMUA data pemesanan dan pembayaran!
 */

require_once 'config.php';

// Confirmation check
$confirm = $_GET['confirm'] ?? '';

if ($confirm !== 'yes') {
    echo "<h2>⚠️ PERINGATAN: CLEAN RESET</h2>";
    echo "<p style='color: red; font-weight: bold;'>Script ini akan menghapus SEMUA data pemesanan dan pembayaran!</p>";
    echo "<p>Hanya gunakan ini jika Anda ingin memulai fresh dengan ID 1.</p>";
    
    // Tampilkan data yang akan dihapus
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM pemesanan");
    $countPemesanan = $stmt->fetchColumn();
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM pembayaran");
    $countPembayaran = $stmt->fetchColumn();
    
    echo "<p><strong>Data yang akan dihapus:</strong></p>";
    echo "<ul>";
    echo "<li>Pemesanan: $countPemesanan data</li>";
    echo "<li>Pembayaran: $countPembayaran data</li>";
    echo "</ul>";
    
    echo "<p><strong>Klik link berikut jika Anda yakin:</strong></p>";
    echo "<p><a href='?confirm=yes' style='background: red; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🗑️ YA, HAPUS SEMUA DATA</a></p>";
    echo "<p><a href='fix_pemesanan_ids.php' style='background: green; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔧 Tidak, Gunakan Reset Biasa</a></p>";
    exit;
}

echo "<h2>🗑️ CLEAN RESET - Menghapus Semua Data</h2>";

try {
    // Nonaktifkan foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    echo "<p>🔄 Menghapus semua data pembayaran...</p>";
    $pdo->exec("DELETE FROM pembayaran");
    
    echo "<p>🔄 Menghapus semua data pemesanan...</p>";
    $pdo->exec("DELETE FROM pemesanan");
    
    echo "<p>🔄 Reset AUTO_INCREMENT pembayaran ke 1...</p>";
    $pdo->exec("ALTER TABLE pembayaran AUTO_INCREMENT = 1");
    
    echo "<p>🔄 Reset AUTO_INCREMENT pemesanan ke 1...</p>";
    $pdo->exec("ALTER TABLE pemesanan AUTO_INCREMENT = 1");
    
    // Aktifkan kembali foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "<p style='color: green; font-weight: bold;'>✅ CLEAN RESET BERHASIL!</p>";
    
    // Verifikasi
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM pemesanan");
    $countPemesanan = $stmt->fetchColumn();
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM pembayaran");
    $countPembayaran = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SHOW TABLE STATUS LIKE 'pemesanan'");
    $infoPemesanan = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = $pdo->query("SHOW TABLE STATUS LIKE 'pembayaran'");
    $infoPembayaran = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h3>📊 Status Setelah Clean Reset:</h3>";
    echo "<ul>";
    echo "<li><strong>Pemesanan:</strong> $countPemesanan data, AUTO_INCREMENT = " . $infoPemesanan['Auto_increment'] . "</li>";
    echo "<li><strong>Pembayaran:</strong> $countPembayaran data, AUTO_INCREMENT = " . $infoPembayaran['Auto_increment'] . "</li>";
    echo "</ul>";
    
    echo "<h3>🎉 Selesai!</h3>";
    echo "<p><strong>Sekarang ketika Anda menambah pemesanan baru:</strong></p>";
    echo "<ul>";
    echo "<li>Pemesanan pertama akan mendapat ID 1</li>";
    echo "<li>Pemesanan kedua akan mendapat ID 2</li>";
    echo "<li>Dan seterusnya berurutan tanpa gap</li>";
    echo "</ul>";
    
    echo "<p><a href='admin/data_pemesanan.php' target='_blank' style='background: blue; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📋 Buka Halaman Data Pemesanan</a></p>";
    
} catch (Exception $e) {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
