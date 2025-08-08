<?php
/**
 * Check AUTO_INCREMENT status untuk semua tabel
 */

require_once 'config.php';

echo "<h2>🔍 Status AUTO_INCREMENT Database Saat Ini</h2>";

$tables = ['acara', 'admin', 'pembayaran', 'pemesanan', 'penyewa', 'verifikasi_email'];

foreach ($tables as $table) {
    echo "<h3>📋 Tabel: $table</h3>";
    
    // Cek status tabel
    $stmt = $pdo->query("SHOW TABLE STATUS LIKE '$table'");
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Hitung jumlah data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
    $count = $stmt->fetchColumn();
    
    // Cek ID maksimum
    $id_field = "id_" . ($table === 'acara' ? 'acara' : ($table === 'admin' ? 'admin' : ($table === 'verifikasi_email' ? 'verifikasi' : $table)));
    $stmt = $pdo->query("SELECT MAX($id_field) as max_id FROM $table");
    $max_id = $stmt->fetchColumn() ?? 0;
    
    echo "<ul>";
    echo "<li><strong>Jumlah Data:</strong> $count</li>";
    echo "<li><strong>ID Maksimum:</strong> $max_id</li>";
    echo "<li><strong>AUTO_INCREMENT:</strong> " . ($info['Auto_increment'] ?? 'N/A') . "</li>";
    echo "</ul>";
    
    // Cek apakah AUTO_INCREMENT perlu direset
    $expected_next = $max_id + 1;
    $current_auto = $info['Auto_increment'] ?? 0;
    
    if ($current_auto != $expected_next) {
        echo "<p style='color: red;'><strong>⚠️ Masalah:</strong> AUTO_INCREMENT = $current_auto, seharusnya = $expected_next</p>";
    } else {
        echo "<p style='color: green;'><strong>✅ OK:</strong> AUTO_INCREMENT sudah benar</p>";
    }
    
    echo "<hr>";
}

echo "<h3>🔧 Solusi Manual</h3>";
echo "<p>Jika ada masalah, jalankan perintah berikut untuk mereset AUTO_INCREMENT:</p>";
echo "<ul>";
echo "<li><a href='manual_reorder.php?action=all' target='_blank'>Reset Semua AUTO_INCREMENT</a></li>";
echo "<li><a href='test_simple_reorder.php' target='_blank'>Test Fungsi Reorder</a></li>";
echo "</ul>";
?>
