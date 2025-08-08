<?php
/**
 * Test file untuk menguji fungsi simple reorder IDs
 * Versi sederhana tanpa transaksi kompleks
 */

require_once 'config.php';
require_once 'simple_reorder.php';

echo "<h2>Test Simple Reorder IDs - Sistem Pengurutan Ulang ID (Versi Sederhana)</h2>";
echo "<p>File ini akan mengatur ulang semua ID agar berurutan tanpa gap menggunakan fungsi sederhana.</p>";

// Test reorder penyewa
echo "<h3>1. Mengatur ulang ID Penyewa</h3>";
$result_penyewa = simpleReorderPenyewaIds();
if ($result_penyewa) {
    echo "✅ ID Penyewa berhasil diurutkan ulang<br>";
    
    // Tampilkan data penyewa setelah reorder
    $stmt = $pdo->query("SELECT id_penyewa, nama_lengkap, email FROM penyewa ORDER BY id_penyewa");
    $penyewaList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($penyewaList)) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Nama</th><th>Email</th></tr>";
        foreach ($penyewaList as $penyewa) {
            echo "<tr>";
            echo "<td>" . $penyewa['id_penyewa'] . "</td>";
            echo "<td>" . htmlspecialchars($penyewa['nama_lengkap']) . "</td>";
            echo "<td>" . htmlspecialchars($penyewa['email']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Tidak ada data penyewa.</p>";
    }
} else {
    echo "❌ Gagal mengatur ulang ID Penyewa<br>";
}

echo "<hr>";

// Test reorder pemesanan
echo "<h3>2. Mengatur ulang ID Pemesanan</h3>";
$result_pemesanan = simpleReorderPemesananIds();
if ($result_pemesanan) {
    echo "✅ ID Pemesanan berhasil diurutkan ulang<br>";
    
    // Tampilkan data pemesanan setelah reorder
    $stmt = $pdo->query("SELECT p.id_pemesanan, py.nama_lengkap, a.nama_acara, p.tanggal_sewa 
                        FROM pemesanan p 
                        LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa 
                        LEFT JOIN acara a ON p.id_acara = a.id_acara 
                        ORDER BY p.id_pemesanan");
    $pemesananList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($pemesananList)) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Penyewa</th><th>Acara</th><th>Tanggal Sewa</th></tr>";
        foreach ($pemesananList as $pemesanan) {
            echo "<tr>";
            echo "<td>" . $pemesanan['id_pemesanan'] . "</td>";
            echo "<td>" . htmlspecialchars($pemesanan['nama_lengkap']) . "</td>";
            echo "<td>" . htmlspecialchars($pemesanan['nama_acara']) . "</td>";
            echo "<td>" . $pemesanan['tanggal_sewa'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Tidak ada data pemesanan.</p>";
    }
} else {
    echo "❌ Gagal mengatur ulang ID Pemesanan<br>";
}

echo "<hr>";

// Test reorder pembayaran
echo "<h3>3. Mengatur ulang ID Pembayaran</h3>";
$result_pembayaran = simpleReorderPembayaranIds();
if ($result_pembayaran) {
    echo "✅ ID Pembayaran berhasil diurutkan ulang<br>";
    
    // Tampilkan data pembayaran setelah reorder
    $stmt = $pdo->query("SELECT pb.id_pembayaran, pb.id_pemesanan, pb.status_pembayaran, pb.tanggal_upload 
                        FROM pembayaran pb 
                        ORDER BY pb.id_pembayaran");
    $pembayaranList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($pembayaranList)) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID Pembayaran</th><th>ID Pemesanan</th><th>Status</th><th>Tanggal Upload</th></tr>";
        foreach ($pembayaranList as $pembayaran) {
            echo "<tr>";
            echo "<td>" . $pembayaran['id_pembayaran'] . "</td>";
            echo "<td>" . $pembayaran['id_pemesanan'] . "</td>";
            echo "<td>" . $pembayaran['status_pembayaran'] . "</td>";
            echo "<td>" . ($pembayaran['tanggal_upload'] ?? 'Belum upload') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Tidak ada data pembayaran.</p>";
    }
} else {
    echo "❌ Gagal mengatur ulang ID Pembayaran<br>";
}

echo "<hr>";

echo "<h3>Informasi AUTO_INCREMENT Saat Ini:</h3>";
$tables = ['penyewa', 'pemesanan', 'pembayaran'];
foreach ($tables as $table) {
    $stmt = $pdo->query("SHOW TABLE STATUS LIKE '$table'");
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p><strong>$table:</strong> AUTO_INCREMENT = " . $info['Auto_increment'] . "</p>";
}

echo "<hr>";
echo "<h3>🎉 Selesai!</h3>";
echo "<p>Semua ID sekarang sudah berurutan tanpa gap menggunakan metode sederhana tanpa transaksi kompleks.</p>";
echo "<p><strong>Keuntungan versi sederhana:</strong></p>";
echo "<ul>";
echo "<li>✅ Tidak ada konflik transaksi</li>";
echo "<li>✅ Lebih stabil untuk AUTO_INCREMENT</li>";
echo "<li>✅ Menghindari error 'There is no active transaction'</li>";
echo "</ul>";
echo "<p><strong>Contoh:</strong> Jika Anda memiliki ID 1,2,3 kemudian menghapus ID 2, maka ID 3 akan menjadi ID 2, dan data baru akan mendapat ID 3.</p>";
?>
