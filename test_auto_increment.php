<?php
/**
 * Test script untuk memverifikasi auto increment reset
 * Buka di browser: http://localhost/sewa-gedung/test_auto_increment.php
 */

require_once 'config.php';
require_once 'reset_auto_increment.php';

// Function untuk test auto increment penyewa
function testPenyewaAutoIncrement() {
    global $pdo;
    
    echo "<h2>Testing Auto Increment Reset untuk Penyewa</h2>";
    
    try {
        // 1. Insert beberapa data test
        echo "<h3>1. Menambahkan 3 data test</h3>";
        for ($i = 1; $i <= 3; $i++) {
            $stmt = $pdo->prepare("INSERT INTO penyewa (tipe_penyewa, nama_lengkap, email, no_telepon, alamat, username, password, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                'individu',
                "Test User $i",
                "test$i@example.com",
                "08123456789$i",
                "Alamat Test $i",
                "testuser$i",
                password_hash("password$i", PASSWORD_DEFAULT)
            ]);
            $id = $pdo->lastInsertId();
            echo "<p>✅ Inserted: Test User $i (ID: $id)</p>";
        }
        
        // 2. Tampilkan current data
        echo "<h3>2. Data setelah insert</h3>";
        $stmt = $pdo->query("SELECT id_penyewa, nama_lengkap FROM penyewa WHERE nama_lengkap LIKE 'Test User%' ORDER BY id_penyewa");
        $data = $stmt->fetchAll();
        foreach ($data as $row) {
            echo "<p>ID: {$row['id_penyewa']} - {$row['nama_lengkap']}</p>";
        }
        
        // 3. Delete data dengan ID 2
        echo "<h3>3. Menghapus Test User 2 (ID tengah)</h3>";
        $stmt = $pdo->prepare("DELETE FROM penyewa WHERE nama_lengkap = ?");
        $stmt->execute(['Test User 2']);
        echo "<p>✅ Test User 2 dihapus</p>";
        
        // 4. Reset auto increment
        echo "<h3>4. Reset auto increment</h3>";
        $result = resetPenyewaAutoIncrement();
        if ($result) {
            echo "<p>✅ Auto increment berhasil direset</p>";
        } else {
            echo "<p>❌ Auto increment gagal direset</p>";
        }
        
        // 5. Check current auto increment value
        $stmt = $pdo->query("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'sewa_gedung' AND TABLE_NAME = 'penyewa'");
        $autoInc = $stmt->fetchColumn();
        echo "<p>Current AUTO_INCREMENT value: $autoInc</p>";
        
        // 6. Insert data baru
        echo "<h3>5. Menambahkan data baru setelah reset</h3>";
        $stmt = $pdo->prepare("INSERT INTO penyewa (tipe_penyewa, nama_lengkap, email, no_telepon, alamat, username, password, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            'individu',
            "Test User New",
            "testnew@example.com",
            "081234567890",
            "Alamat Test New",
            "testusernew",
            password_hash("passwordnew", PASSWORD_DEFAULT)
        ]);
        $newId = $pdo->lastInsertId();
        echo "<p>✅ Inserted: Test User New (ID: $newId)</p>";
        
        // 7. Tampilkan final data
        echo "<h3>6. Data final</h3>";
        $stmt = $pdo->query("SELECT id_penyewa, nama_lengkap FROM penyewa WHERE nama_lengkap LIKE 'Test User%' ORDER BY id_penyewa");
        $data = $stmt->fetchAll();
        foreach ($data as $row) {
            echo "<p>ID: {$row['id_penyewa']} - {$row['nama_lengkap']}</p>";
        }
        
        // 8. Cleanup - hapus semua test data
        echo "<h3>7. Cleanup test data</h3>";
        $stmt = $pdo->prepare("DELETE FROM penyewa WHERE nama_lengkap LIKE ?");
        $stmt->execute(['Test User%']);
        $deleted = $stmt->rowCount();
        echo "<p>✅ $deleted test records dihapus</p>";
        
        // Final reset
        resetPenyewaAutoIncrement();
        echo "<p>✅ Final auto increment reset</p>";
        
        // Check final auto increment
        $stmt = $pdo->query("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'sewa_gedung' AND TABLE_NAME = 'penyewa'");
        $finalAutoInc = $stmt->fetchColumn();
        echo "<p>Final AUTO_INCREMENT value: $finalAutoInc</p>";
        
        if ($newId == 2) {
            echo "<h3>🎉 SUCCESS: Data baru mendapat ID 2 (mengisi gap yang kosong)</h3>";
        } else {
            echo "<h3>❌ FAILED: Data baru mendapat ID $newId (seharusnya ID 2)</h3>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    }
}

// Check if stored procedures exist
function checkStoredProcedures() {
    global $pdo;
    
    echo "<h2>Checking Stored Procedures</h2>";
    
    $procedures = [
        'reset_penyewa_auto_increment',
        'reset_acara_auto_increment',
        'reset_admin_auto_increment',
        'reset_pembayaran_auto_increment',
        'reset_pemesanan_auto_increment',
        'reset_verifikasi_email_auto_increment'
    ];
    
    foreach ($procedures as $proc) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = 'sewa_gedung' AND ROUTINE_NAME = ?");
        $stmt->execute([$proc]);
        $exists = $stmt->fetchColumn() > 0;
        
        if ($exists) {
            echo "<p>✅ $proc exists</p>";
        } else {
            echo "<p>❌ $proc not found</p>";
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Auto Increment Reset</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; max-width: 800px; }
        button { padding: 10px 20px; margin: 10px 0; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .success { color: green; }
        .error { color: red; }
        .info { background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Test Auto Increment Reset System</h1>
    
    <div class="info">
        <p><strong>Test ini akan:</strong></p>
        <ul>
            <li>Menambah 3 data test ke tabel penyewa</li>
            <li>Menghapus data dengan ID tengah</li>
            <li>Reset auto increment</li>
            <li>Menambah data baru dan memverifikasi ID mengisi gap</li>
            <li>Cleanup semua test data</li>
        </ul>
    </div>
    
    <form method="POST">
        <button type="submit" name="action" value="check">Check Stored Procedures</button>
        <button type="submit" name="action" value="test">Run Test</button>
        <button type="submit" name="action" value="both">Check & Test</button>
    </form>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'check':
                checkStoredProcedures();
                break;
            case 'test':
                testPenyewaAutoIncrement();
                break;
            case 'both':
                checkStoredProcedures();
                echo "<hr>";
                testPenyewaAutoIncrement();
                break;
        }
    }
    ?>
</body>
</html>
