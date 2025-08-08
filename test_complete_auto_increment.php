<?php
/**
 * Comprehensive Test untuk semua Auto Increment Reset
 * Test untuk Penyewa, Pemesanan, dan Pembayaran
 */

require_once 'config.php';
require_once 'reset_auto_increment.php';

function testAllAutoIncrements() {
    global $pdo;
    
    echo "<h2>🧪 Complete Auto Increment Reset Test</h2>";
    echo "<p>Testing: Penyewa → Pemesanan → Pembayaran (dengan foreign key relationships)</p>";
    
    try {
        // 1. Test Penyewa
        echo "<h3>1️⃣ Testing Penyewa Auto Increment</h3>";
        
        $penyewaIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $stmt = $pdo->prepare("INSERT INTO penyewa (tipe_penyewa, nama_lengkap, email, no_telepon, alamat, username, password, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                'individu',
                "Test Penyewa $i",
                "testpenyewa$i@example.com",
                "08123456789$i",
                "Alamat Test $i",
                "testpenyewa$i",
                password_hash("password$i", PASSWORD_DEFAULT)
            ]);
            $id = $pdo->lastInsertId();
            $penyewaIds[] = $id;
            echo "<span style='color: green'>✅</span> Penyewa $i created (ID: $id)<br>";
        }
        
        // Delete middle penyewa
        $middlePenyewaId = $penyewaIds[1];
        $stmt = $pdo->prepare("DELETE FROM penyewa WHERE id_penyewa = ?");
        $stmt->execute([$middlePenyewaId]);
        resetPenyewaAutoIncrement();
        echo "<span style='color: orange'>🗑️</span> Deleted Penyewa ID: $middlePenyewaId and reset auto increment<br>";
        
        // Add new penyewa
        $stmt = $pdo->prepare("INSERT INTO penyewa (tipe_penyewa, nama_lengkap, email, no_telepon, alamat, username, password, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            'individu',
            "Test Penyewa New",
            "testpenyewanew@example.com",
            "08123456999",
            "Alamat Test New",
            "testpenyewanew",
            password_hash("passwordnew", PASSWORD_DEFAULT)
        ]);
        $newPenyewaId = $pdo->lastInsertId();
        echo "<span style='color: blue'>➕</span> New Penyewa created (ID: $newPenyewaId)<br>";
        
        if ($newPenyewaId == $middlePenyewaId) {
            echo "<span style='color: green'>🎉 PENYEWA SUCCESS: ID $newPenyewaId mengisi gap yang kosong!</span><br><br>";
        } else {
            echo "<span style='color: red'>❌ PENYEWA FAILED: Expected ID $middlePenyewaId, got $newPenyewaId</span><br><br>";
        }
        
        // 2. Test Pemesanan menggunakan penyewa yang ada
        echo "<h3>2️⃣ Testing Pemesanan Auto Increment</h3>";
        
        // Get acara ID
        $stmt = $pdo->query("SELECT id_acara FROM acara LIMIT 1");
        $acaraId = $stmt->fetchColumn();
        
        $pemesananIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $stmt = $pdo->prepare("INSERT INTO pemesanan (id_penyewa, id_acara, tanggal_sewa, tanggal_selesai, durasi, total, metode_pembayaran, tanggal_pesan) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $penyewaIds[0], // Use first penyewa
                $acaraId,
                '2025-08-10',
                '2025-08-10',
                1,
                1000000 * $i,
                'Transfer_BCA'
            ]);
            $id = $pdo->lastInsertId();
            $pemesananIds[] = $id;
            echo "<span style='color: green'>✅</span> Pemesanan $i created (ID: $id)<br>";
        }
        
        // Delete middle pemesanan
        $middlePemesananId = $pemesananIds[1];
        $stmt = $pdo->prepare("DELETE FROM pemesanan WHERE id_pemesanan = ?");
        $stmt->execute([$middlePemesananId]);
        resetPemesananAutoIncrement();
        echo "<span style='color: orange'>🗑️</span> Deleted Pemesanan ID: $middlePemesananId and reset auto increment<br>";
        
        // Add new pemesanan
        $stmt = $pdo->prepare("INSERT INTO pemesanan (id_penyewa, id_acara, tanggal_sewa, tanggal_selesai, durasi, total, metode_pembayaran, tanggal_pesan) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $penyewaIds[0],
            $acaraId,
            '2025-08-11',
            '2025-08-11',
            1,
            2500000,
            'QRIS'
        ]);
        $newPemesananId = $pdo->lastInsertId();
        echo "<span style='color: blue'>➕</span> New Pemesanan created (ID: $newPemesananId)<br>";
        
        if ($newPemesananId == $middlePemesananId) {
            echo "<span style='color: green'>🎉 PEMESANAN SUCCESS: ID $newPemesananId mengisi gap yang kosong!</span><br><br>";
        } else {
            echo "<span style='color: red'>❌ PEMESANAN FAILED: Expected ID $middlePemesananId, got $newPemesananId</span><br><br>";
        }
        
        // 3. Test Pembayaran menggunakan pemesanan yang ada
        echo "<h3>3️⃣ Testing Pembayaran Auto Increment</h3>";
        
        $pembayaranIds = [];
        $activePemesananIds = [$pemesananIds[0], $pemesananIds[2], $newPemesananId]; // Skip deleted middle one
        
        for ($i = 0; $i < 3; $i++) {
            $stmt = $pdo->prepare("INSERT INTO pembayaran (id_pemesanan, status_pembayaran) VALUES (?, ?)");
            $stmt->execute([$activePemesananIds[$i], 'Belum Lunas']);
            $id = $pdo->lastInsertId();
            $pembayaranIds[] = $id;
            echo "<span style='color: green'>✅</span> Pembayaran " . ($i + 1) . " created (ID: $id) for Pemesanan {$activePemesananIds[$i]}<br>";
        }
        
        // Delete middle pembayaran
        $middlePembayaranId = $pembayaranIds[1];
        $stmt = $pdo->prepare("DELETE FROM pembayaran WHERE id_pembayaran = ?");
        $stmt->execute([$middlePembayaranId]);
        resetPembayaranAutoIncrement();
        echo "<span style='color: orange'>🗑️</span> Deleted Pembayaran ID: $middlePembayaranId and reset auto increment<br>";
        
        // Add new pembayaran
        $stmt = $pdo->prepare("INSERT INTO pembayaran (id_pemesanan, status_pembayaran) VALUES (?, ?)");
        $stmt->execute([$newPemesananId, 'Lunas']);
        $newPembayaranId = $pdo->lastInsertId();
        echo "<span style='color: blue'>➕</span> New Pembayaran created (ID: $newPembayaranId)<br>";
        
        if ($newPembayaranId == $middlePembayaranId) {
            echo "<span style='color: green'>🎉 PEMBAYARAN SUCCESS: ID $newPembayaranId mengisi gap yang kosong!</span><br><br>";
        } else {
            echo "<span style='color: red'>❌ PEMBAYARAN FAILED: Expected ID $middlePembayaranId, got $newPembayaranId</span><br><br>";
        }
        
        // 4. Display final state
        echo "<h3>4️⃣ Final Data State</h3>";
        
        echo "<h4>Penyewa:</h4>";
        $stmt = $pdo->query("SELECT id_penyewa, nama_lengkap FROM penyewa WHERE nama_lengkap LIKE 'Test Penyewa%' ORDER BY id_penyewa");
        while ($row = $stmt->fetch()) {
            echo "ID: {$row['id_penyewa']} - {$row['nama_lengkap']}<br>";
        }
        
        echo "<h4>Pemesanan:</h4>";
        $stmt = $pdo->query("SELECT id_pemesanan, id_penyewa, total FROM pemesanan WHERE id_penyewa IN (" . implode(',', $penyewaIds) . ") ORDER BY id_pemesanan");
        while ($row = $stmt->fetch()) {
            echo "ID: {$row['id_pemesanan']} - Penyewa: {$row['id_penyewa']} - Total: Rp " . number_format($row['total']) . "<br>";
        }
        
        echo "<h4>Pembayaran:</h4>";
        $stmt = $pdo->query("SELECT b.id_pembayaran, b.id_pemesanan, b.status_pembayaran FROM pembayaran b JOIN pemesanan p ON b.id_pemesanan = p.id_pemesanan WHERE p.id_penyewa IN (" . implode(',', $penyewaIds) . ") ORDER BY b.id_pembayaran");
        while ($row = $stmt->fetch()) {
            echo "ID: {$row['id_pembayaran']} - Pemesanan: {$row['id_pemesanan']} - Status: {$row['status_pembayaran']}<br>";
        }
        
        // 5. Cleanup
        echo "<h3>5️⃣ Cleanup Test Data</h3>";
        
        $stmt = $pdo->prepare("DELETE FROM pembayaran WHERE id_pemesanan IN (SELECT id_pemesanan FROM pemesanan WHERE id_penyewa IN (" . implode(',', array_filter($penyewaIds)) . "))");
        $stmt->execute();
        
        $stmt = $pdo->prepare("DELETE FROM pemesanan WHERE id_penyewa IN (" . implode(',', array_filter($penyewaIds)) . ")");
        $stmt->execute();
        
        $stmt = $pdo->prepare("DELETE FROM penyewa WHERE nama_lengkap LIKE 'Test Penyewa%'");
        $stmt->execute();
        
        // Final reset all
        resetAllAutoIncrements();
        
        echo "<span style='color: green'>✅ All test data cleaned up and auto increments reset</span><br>";
        
        echo "<h3>🏆 Test Results Summary</h3>";
        echo "<ul>";
        echo "<li>Penyewa Auto Increment: " . ($newPenyewaId == $middlePenyewaId ? "✅ PASSED" : "❌ FAILED") . "</li>";
        echo "<li>Pemesanan Auto Increment: " . ($newPemesananId == $middlePemesananId ? "✅ PASSED" : "❌ FAILED") . "</li>";
        echo "<li>Pembayaran Auto Increment: " . ($newPembayaranId == $middlePembayaranId ? "✅ PASSED" : "❌ FAILED") . "</li>";
        echo "</ul>";
        
    } catch (Exception $e) {
        echo "<span style='color: red'>❌ Error: " . $e->getMessage() . "</span>";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Complete Auto Increment Reset Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; max-width: 1000px; }
        button { padding: 15px 30px; margin: 10px 0; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #218838; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #2196f3; }
        h3 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 5px; }
        h4 { color: #555; margin-top: 15px; }
    </style>
</head>
<body>
    <h1>🔧 Complete Auto Increment Reset Test</h1>
    
    <div class="info">
        <h3>🎯 What This Test Does:</h3>
        <ol>
            <li><strong>Penyewa Test:</strong> Create 3 → Delete middle → Add new → Verify ID fills gap</li>
            <li><strong>Pemesanan Test:</strong> Create 3 → Delete middle → Add new → Verify ID fills gap</li>
            <li><strong>Pembayaran Test:</strong> Create 3 → Delete middle → Add new → Verify ID fills gap</li>
            <li><strong>Foreign Key Handling:</strong> Tests with proper relationships between tables</li>
            <li><strong>Cleanup:</strong> Removes all test data and resets all auto increments</li>
        </ol>
        
        <h3>📋 Expected Results:</h3>
        <p>All three tables should show <strong>✅ PASSED</strong> - meaning new records fill the gaps left by deleted records.</p>
    </div>
    
    <form method="POST">
        <button type="submit" name="action" value="test">🚀 Run Complete Test</button>
    </form>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        if ($_POST['action'] === 'test') {
            testAllAutoIncrements();
        }
    }
    ?>
    
    <div class="info" style="margin-top: 30px; background: #f8f9fa; border-left-color: #6c757d;">
        <h3>ℹ️ Implementation Status:</h3>
        <ul>
            <li>✅ <strong>Penyewa:</strong> Auto-reset on delete via admin panel</li>
            <li>✅ <strong>Pemesanan:</strong> Auto-reset on delete via admin panel</li>
            <li>✅ <strong>Pembayaran:</strong> Auto-reset when pemesanan deleted (CASCADE)</li>
            <li>📋 <strong>Riwayat & Laporan:</strong> Display only, no delete needed</li>
        </ul>
    </div>
</body>
</html>
