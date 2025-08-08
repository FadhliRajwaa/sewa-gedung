<?php
/**
 * SAFE RESET - Hanya reset AUTO_INCREMENT berdasarkan data yang ada
 * Script ini TIDAK menghapus data, hanya memperbaiki AUTO_INCREMENT
 */

require_once 'config.php';

echo "<h2>🔧 SAFE RESET - Perbaiki AUTO_INCREMENT</h2>";
echo "<p>Script ini akan memperbaiki AUTO_INCREMENT tanpa menghapus data yang ada.</p>";

// Proses reset AUTO_INCREMENT untuk semua tabel
$tables = [
    'pemesanan' => 'id_pemesanan',
    'pembayaran' => 'id_pembayaran',
    'penyewa' => 'id_penyewa',
    'verifikasi_email' => 'id_verifikasi',
    'acara' => 'id_acara',
    'admin' => 'id_admin'
];

foreach ($tables as $table => $id_field) {
    echo "<h3>📋 Memproses Tabel: $table</h3>";
    
    try {
        // Hitung jumlah data
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetchColumn();
        
        // Cek AUTO_INCREMENT saat ini
        $stmt = $pdo->query("SHOW TABLE STATUS LIKE '$table'");
        $info = $stmt->fetch(PDO::FETCH_ASSOC);
        $currentAutoIncrement = $info['Auto_increment'] ?? 0;
        
        if ($count == 0) {
            // Jika tidak ada data, set AUTO_INCREMENT ke 1
            $pdo->exec("ALTER TABLE $table AUTO_INCREMENT = 1");
            echo "<p>✅ Tabel kosong, AUTO_INCREMENT diset ke 1</p>";
        } else {
            // Jika ada data, set AUTO_INCREMENT ke MAX(id) + 1
            $stmt = $pdo->query("SELECT MAX($id_field) as max_id FROM $table");
            $maxId = $stmt->fetchColumn();
            $correctAutoIncrement = $maxId + 1;
            
            $pdo->exec("ALTER TABLE $table AUTO_INCREMENT = $correctAutoIncrement");
            
            echo "<ul>";
            echo "<li><strong>Jumlah Data:</strong> $count</li>";
            echo "<li><strong>ID Maksimum:</strong> $maxId</li>";
            echo "<li><strong>AUTO_INCREMENT Sebelum:</strong> $currentAutoIncrement</li>";
            echo "<li><strong>AUTO_INCREMENT Sesudah:</strong> $correctAutoIncrement</li>";
            echo "</ul>";
            
            if ($currentAutoIncrement != $correctAutoIncrement) {
                echo "<p style='color: green;'>✅ AUTO_INCREMENT diperbaiki!</p>";
            } else {
                echo "<p style='color: blue;'>ℹ️ AUTO_INCREMENT sudah benar</p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
}

echo "<h3>🎉 Safe Reset Selesai!</h3>";
echo "<p>Semua AUTO_INCREMENT telah diperbaiki sesuai dengan data yang ada.</p>";

// Tampilkan status final
echo "<h3>📊 Status Final AUTO_INCREMENT:</h3>";
foreach ($tables as $table => $id_field) {
    $stmt = $pdo->query("SHOW TABLE STATUS LIKE '$table'");
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
    $count = $stmt->fetchColumn();
    
    echo "<p><strong>$table:</strong> $count data, AUTO_INCREMENT = " . ($info['Auto_increment'] ?? 'N/A') . "</p>";
}

echo "<h3>🚀 Sekarang Coba Tambah Data Baru!</h3>";
echo "<p>Data baru akan mendapat ID yang benar sesuai urutan.</p>";
echo "<ul>";
echo "<li><a href='admin/data_penyewa.php' target='_blank'>📋 Data Penyewa</a></li>";
echo "<li><a href='admin/data_pemesanan.php' target='_blank'>📋 Data Pemesanan</a></li>";
echo "<li><a href='check_auto_increment.php' target='_blank'>🔍 Cek Status AUTO_INCREMENT</a></li>";
echo "</ul>";
?>
