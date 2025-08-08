<?php
/**
 * Reset AUTO_INCREMENT semua tabel ke nilai yang benar
 * Jalankan file ini untuk memastikan ID dimulai dari 1
 */

require_once 'config.php';

echo "<h2>🔧 Reset AUTO_INCREMENT ke Nilai yang Benar</h2>";

// Daftar tabel dengan field ID masing-masing
$tables = [
    'acara' => 'id_acara',
    'admin' => 'id_admin', 
    'pembayaran' => 'id_pembayaran',
    'pemesanan' => 'id_pemesanan',
    'penyewa' => 'id_penyewa',
    'verifikasi_email' => 'id_verifikasi'
];

foreach ($tables as $table => $id_field) {
    echo "<h3>📋 Processing Tabel: $table</h3>";
    
    try {
        // Hitung jumlah data
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetchColumn();
        
        // Cek ID maksimum
        $stmt = $pdo->query("SELECT MAX($id_field) as max_id FROM $table");
        $max_id = $stmt->fetchColumn() ?? 0;
        
        // Tentukan nilai AUTO_INCREMENT yang benar
        $correct_auto_increment = $max_id + 1;
        
        // Jika tidak ada data, set ke 1
        if ($count == 0) {
            $correct_auto_increment = 1;
        }
        
        // Reset AUTO_INCREMENT
        $sql = "ALTER TABLE $table AUTO_INCREMENT = $correct_auto_increment";
        $pdo->exec($sql);
        
        echo "<ul>";
        echo "<li><strong>Jumlah Data:</strong> $count</li>";
        echo "<li><strong>ID Maksimum:</strong> $max_id</li>";
        echo "<li><strong>AUTO_INCREMENT Diset ke:</strong> $correct_auto_increment</li>";
        echo "</ul>";
        echo "<p style='color: green;'><strong>✅ Berhasil!</strong> AUTO_INCREMENT tabel $table telah direset.</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'><strong>❌ Error:</strong> " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
}

echo "<h3>🎉 Selesai!</h3>";
echo "<p>Semua AUTO_INCREMENT telah direset ke nilai yang benar.</p>";
echo "<p><strong>Sekarang coba tambah data baru:</strong></p>";
echo "<ul>";
echo "<li>Data Penyewa akan mulai dari ID yang benar</li>";
echo "<li>Data Pemesanan akan mulai dari ID 1 (jika kosong) atau ID berikutnya</li>";
echo "<li>Data Pembayaran akan mulai dari ID 1 (jika kosong) atau ID berikutnya</li>";
echo "</ul>";

echo "<h3>📊 Verifikasi Status Terbaru:</h3>";
foreach ($tables as $table => $id_field) {
    $stmt = $pdo->query("SHOW TABLE STATUS LIKE '$table'");
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p><strong>$table:</strong> AUTO_INCREMENT = " . ($info['Auto_increment'] ?? 'N/A') . "</p>";
}
?>
