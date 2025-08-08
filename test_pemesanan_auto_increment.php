<?php
/**
 * Test script untuk auto increment reset untuk Pemesanan dan Pembayaran
 * Buka di browser: http://localhost/sewa-gedung/test_pemesanan_auto_increment.php
 */

require_once 'config.php';
require_once 'reset_auto_increment.php';

function testPemesananAutoIncrement() {
    global $pdo;
    
    echo "<h2>Testing Auto Increment Reset untuk Pemesanan & Pembayaran</h2>";
    
    try {
        // 1. Pastikan ada data penyewa dan acara untuk foreign key
        echo "<h3>1. Memastikan data referensi tersedia</h3>";
        
        // Check penyewa
        $stmtPenyewa = $pdo->query("SELECT COUNT(*) FROM penyewa");
        $countPenyewa = $stmtPenyewa->fetchColumn();
        if ($countPenyewa == 0) {
            // Create test penyewa
            $stmt = $pdo->prepare("INSERT INTO penyewa (tipe_penyewa, nama_lengkap, email, no_telepon, alamat, username, password, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                'individu',
                'Test Penyewa for Pemesanan',
                'testpenyewa@example.com',
                '08123456789',
                'Alamat Test',
                'testpenyewa',
                password_hash('password', PASSWORD_DEFAULT)
            ]);
            $testPenyewaId = $pdo->lastInsertId();
            echo "<p>✅ Test penyewa created (ID: $testPenyewaId)</p>";
        } else {
            $stmt = $pdo->query("SELECT id_penyewa FROM penyewa LIMIT 1");
            $testPenyewaId = $stmt->fetchColumn();
            echo "<p>✅ Using existing penyewa (ID: $testPenyewaId)</p>";
        }
        
        // Check acara
        $stmtAcara = $pdo->query("SELECT id_acara FROM acara LIMIT 1");
        $testAcaraId = $stmtAcara->fetchColumn();
        if (!$testAcaraId) {
            echo "<p>❌ No acara data found. Please add acara data first.</p>";
            return;
        }
        echo "<p>✅ Using acara (ID: $testAcaraId)</p>";
        
        // 2. Insert test pemesanan
        echo "<h3>2. Menambahkan 3 pemesanan test</h3>";
        $pemesananIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $stmt = $pdo->prepare("INSERT INTO pemesanan (id_penyewa, id_acara, tanggal_sewa, tanggal_selesai, durasi, total, metode_pembayaran, tanggal_pesan) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $testPenyewaId,
                $testAcaraId,
                '2025-08-10',
                '2025-08-10',
                1,
                1000000 * $i,
                'Transfer_BCA'
            ]);
            $pemesananId = $pdo->lastInsertId();
            $pemesananIds[] = $pemesananId;
            echo "<p>✅ Inserted: Pemesanan Test $i (ID: $pemesananId)</p>";
            
            // Insert pembayaran untuk setiap pemesanan
            $stmt = $pdo->prepare("INSERT INTO pembayaran (id_pemesanan, status_pembayaran) VALUES (?, ?)");
            $stmt->execute([$pemesananId, 'Belum Lunas']);
            $pembayaranId = $pdo->lastInsertId();
            echo "<p>✅ Inserted: Pembayaran for Pemesanan $i (ID: $pembayaranId)</p>";
        }
        
        // 3. Tampilkan current data
        echo "<h3>3. Data setelah insert</h3>";
        $stmt = $pdo->query("SELECT p.id_pemesanan, p.total, b.id_pembayaran FROM pemesanan p LEFT JOIN pembayaran b ON p.id_pemesanan = b.id_pemesanan WHERE p.id_penyewa = $testPenyewaId ORDER BY p.id_pemesanan");
        $data = $stmt->fetchAll();
        foreach ($data as $row) {
            echo "<p>Pemesanan ID: {$row['id_pemesanan']} - Total: Rp " . number_format($row['total']) . " - Pembayaran ID: {$row['id_pembayaran']}</p>";
        }
        
        // 4. Delete pemesanan dengan ID tengah
        $middleId = $pemesananIds[1]; // ID kedua
        echo "<h3>4. Menghapus Pemesanan ID: $middleId (ID tengah)</h3>";
        
        // Delete pembayaran first
        $stmt = $pdo->prepare("DELETE FROM pembayaran WHERE id_pemesanan = ?");
        $stmt->execute([$middleId]);
        echo "<p>✅ Pembayaran untuk pemesanan $middleId dihapus</p>";
        
        // Delete pemesanan
        $stmt = $pdo->prepare("DELETE FROM pemesanan WHERE id_pemesanan = ?");
        $stmt->execute([$middleId]);
        echo "<p>✅ Pemesanan $middleId dihapus</p>";
        
        // 5. Reset auto increment
        echo "<h3>5. Reset auto increment</h3>";
        $resultPembayaran = resetPembayaranAutoIncrement();
        $resultPemesanan = resetPemesananAutoIncrement();
        
        if ($resultPembayaran && $resultPemesanan) {
            echo "<p>✅ Auto increment berhasil direset untuk kedua tabel</p>";
        } else {
            echo "<p>❌ Auto increment gagal direset</p>";
        }
        
        // 6. Check current auto increment values
        $stmt = $pdo->query("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'sewa_gedung' AND TABLE_NAME = 'pemesanan'");
        $autoIncPemesanan = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'sewa_gedung' AND TABLE_NAME = 'pembayaran'");
        $autoIncPembayaran = $stmt->fetchColumn();
        
        echo "<p>Current AUTO_INCREMENT - Pemesanan: $autoIncPemesanan, Pembayaran: $autoIncPembayaran</p>";
        
        // 7. Insert data baru
        echo "<h3>6. Menambahkan pemesanan baru setelah reset</h3>";
        $stmt = $pdo->prepare("INSERT INTO pemesanan (id_penyewa, id_acara, tanggal_sewa, tanggal_selesai, durasi, total, metode_pembayaran, tanggal_pesan) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $testPenyewaId,
            $testAcaraId,
            '2025-08-11',
            '2025-08-11',
            1,
            2500000,
            'QRIS'
        ]);
        $newPemesananId = $pdo->lastInsertId();
        echo "<p>✅ Inserted: Pemesanan New (ID: $newPemesananId)</p>";
        
        // Insert pembayaran baru
        $stmt = $pdo->prepare("INSERT INTO pembayaran (id_pemesanan, status_pembayaran) VALUES (?, ?)");
        $stmt->execute([$newPemesananId, 'Belum Lunas']);
        $newPembayaranId = $pdo->lastInsertId();
        echo "<p>✅ Inserted: Pembayaran New (ID: $newPembayaranId)</p>";
        
        // 8. Tampilkan final data
        echo "<h3>7. Data final</h3>";
        $stmt = $pdo->query("SELECT p.id_pemesanan, p.total, b.id_pembayaran FROM pemesanan p LEFT JOIN pembayaran b ON p.id_pemesanan = b.id_pemesanan WHERE p.id_penyewa = $testPenyewaId ORDER BY p.id_pemesanan");
        $data = $stmt->fetchAll();
        foreach ($data as $row) {
            echo "<p>Pemesanan ID: {$row['id_pemesanan']} - Total: Rp " . number_format($row['total']) . " - Pembayaran ID: {$row['id_pembayaran']}</p>";
        }
        
        // 9. Cleanup
        echo "<h3>8. Cleanup test data</h3>";
        $stmt = $pdo->prepare("DELETE FROM pembayaran WHERE id_pemesanan IN (SELECT id_pemesanan FROM pemesanan WHERE id_penyewa = ?)");
        $stmt->execute([$testPenyewaId]);
        
        $stmt = $pdo->prepare("DELETE FROM pemesanan WHERE id_penyewa = ?");
        $stmt->execute([$testPenyewaId]);
        
        // Only delete test penyewa if we created it
        if ($countPenyewa == 0) {
            $stmt = $pdo->prepare("DELETE FROM penyewa WHERE id_penyewa = ?");
            $stmt->execute([$testPenyewaId]);
            echo "<p>✅ Test penyewa dihapus</p>";
        }
        
        // Final reset
        resetPembayaranAutoIncrement();
        resetPemesananAutoIncrement();
        resetPenyewaAutoIncrement();
        echo "<p>✅ Final auto increment reset untuk semua tabel</p>";
        
        // Check final results
        if ($newPemesananId == $middleId) {
            echo "<h3>🎉 SUCCESS: Pemesanan baru mendapat ID $newPemesananId (mengisi gap yang kosong)</h3>";
        } else {
            echo "<h3>❌ FAILED: Pemesanan baru mendapat ID $newPemesananId (seharusnya ID $middleId)</h3>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Auto Increment Reset - Pemesanan & Pembayaran</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; max-width: 900px; }
        button { padding: 10px 20px; margin: 10px 0; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .success { color: green; }
        .error { color: red; }
        .info { background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Test Auto Increment Reset System - Pemesanan & Pembayaran</h1>
    
    <div class="info">
        <p><strong>Test ini akan:</strong></p>
        <ul>
            <li>Memastikan ada data penyewa dan acara untuk referensi</li>
            <li>Menambah 3 pemesanan dan pembayaran test</li>
            <li>Menghapus pemesanan dengan ID tengah (dan pembayaran terkait)</li>
            <li>Reset auto increment kedua tabel</li>
            <li>Menambah pemesanan baru dan memverifikasi ID mengisi gap</li>
            <li>Cleanup semua test data</li>
        </ul>
    </div>
    
    <form method="POST">
        <button type="submit" name="action" value="test">Run Test Pemesanan & Pembayaran</button>
    </form>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        if ($_POST['action'] === 'test') {
            testPemesananAutoIncrement();
        }
    }
    ?>
</body>
</html>
