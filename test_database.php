<?php
// Test koneksi database
echo "<h2>Test Koneksi Database</h2>";

// Test dengan config.php (PDO)
try {
    require_once 'config.php';
    echo "✅ Koneksi PDO berhasil ke database: " . $pdo->query('SELECT DATABASE()')->fetchColumn() . "<br>";
    
    // Cek tabel yang ada
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<h3>Tabel yang tersedia:</h3>";
    foreach ($tables as $table) {
        echo "- " . $table . "<br>";
    }
    
    // Cek struktur tabel penyewa
    echo "<h3>Struktur tabel penyewa:</h3>";
    $stmt = $pdo->query("DESCRIBE penyewa");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")<br>";
    }
    
    // Cek apakah ada kolom email_terverifikasi
    $stmt = $pdo->query("SHOW COLUMNS FROM penyewa LIKE 'email_terverifikasi'");
    if ($stmt->rowCount() == 0) {
        echo "<h3>⚠️ Kolom email_terverifikasi tidak ada, menambahkan...</h3>";
        $pdo->exec("ALTER TABLE penyewa ADD COLUMN email_terverifikasi TINYINT(1) DEFAULT 1");
        echo "✅ Kolom email_terverifikasi berhasil ditambahkan<br>";
    } else {
        echo "✅ Kolom email_terverifikasi sudah ada<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test dengan includes/db.php (MySQLi)
echo "<h3>Test MySQLi Connection:</h3>";
try {
    require_once 'includes/db.php';
    if ($conn) {
        echo "✅ Koneksi MySQLi berhasil<br>";
    }
} catch (Exception $e) {
    echo "❌ MySQLi Error: " . $e->getMessage() . "<br>";
}
?>
