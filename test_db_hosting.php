<?php
// Script test untuk debugging koneksi database hosting
// HAPUS FILE INI SETELAH SELESAI TESTING!

echo "<h2>🔧 Test Koneksi Database Hosting</h2>";
echo "<hr>";

// Database configuration
$host = 'sql205.byethost7.com'; // Ganti sesuai hosting Anda
$dbname = 'b7_39639306_gedung';
$username = 'b7_39639306';
$password = 'YOUR_PASSWORD_HERE'; // GANTI dengan password database Anda

echo "<h3>📋 Informasi Koneksi:</h3>";
echo "Host: <strong>$host</strong><br>";
echo "Database: <strong>$dbname</strong><br>";
echo "Username: <strong>$username</strong><br>";
echo "Password: <strong>" . str_repeat('*', strlen($password)) . "</strong><br><br>";

try {
    echo "<h3>🔌 Testing Koneksi...</h3>";
    
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4", 
        $username, 
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "✅ <strong style='color: green;'>Koneksi database BERHASIL!</strong><br><br>";
    
    // Test query: Show tables
    echo "<h3>📋 Daftar Tabel:</h3>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    
    if (count($tables) > 0) {
        echo "<ul>";
        foreach ($tables as $table) {
            $tableName = $table[array_keys($table)[0]];
            echo "<li>$tableName</li>";
        }
        echo "</ul>";
    } else {
        echo "❌ Tidak ada tabel ditemukan. Import SQL belum dilakukan.<br>";
    }
    
    // Test data: Check if sample data exists
    echo "<h3>📊 Test Data Sample:</h3>";
    
    // Check acara table
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM acara");
        $acaraCount = $stmt->fetch()['count'];
        echo "Tabel acara: <strong>$acaraCount</strong> record<br>";
    } catch (PDOException $e) {
        echo "❌ Error pada tabel acara: " . $e->getMessage() . "<br>";
    }
    
    // Check admin table
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin");
        $adminCount = $stmt->fetch()['count'];
        echo "Tabel admin: <strong>$adminCount</strong> record<br>";
        
        // Show admin data
        $stmt = $pdo->query("SELECT username, email FROM admin LIMIT 1");
        $admin = $stmt->fetch();
        if ($admin) {
            echo "Admin username: <strong>{$admin['username']}</strong><br>";
            echo "Admin email: <strong>{$admin['email']}</strong><br>";
        }
    } catch (PDOException $e) {
        echo "❌ Error pada tabel admin: " . $e->getMessage() . "<br>";
    }
    
    // Check penyewa table
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM penyewa");
        $penyewaCount = $stmt->fetch()['count'];
        echo "Tabel penyewa: <strong>$penyewaCount</strong> record<br>";
    } catch (PDOException $e) {
        echo "❌ Error pada tabel penyewa: " . $e->getMessage() . "<br>";
    }
    
    // Check pemesanan table
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM pemesanan");
        $pemesananCount = $stmt->fetch()['count'];
        echo "Tabel pemesanan: <strong>$pemesananCount</strong> record<br>";
    } catch (PDOException $e) {
        echo "❌ Error pada tabel pemesanan: " . $e->getMessage() . "<br>";
    }
    
    echo "<br><h3>🎉 Status Import:</h3>";
    if ($acaraCount > 0 && $adminCount > 0) {
        echo "✅ <strong style='color: green;'>Database sudah ter-import dengan benar!</strong><br>";
        echo "✅ Website siap digunakan!<br>";
        echo "<br><strong>Link untuk testing:</strong><br>";
        echo "- <a href='index.php' target='_blank'>Homepage</a><br>";
        echo "- <a href='login.php' target='_blank'>Login</a><br>";
        echo "- <a href='admin/' target='_blank'>Admin Panel</a><br>";
    } else {
        echo "⚠️ <strong style='color: orange;'>Import SQL belum lengkap atau gagal.</strong><br>";
        echo "Silakan import file <strong>sewa_gedung_hosting.sql</strong><br>";
    }
    
} catch (PDOException $e) {
    echo "❌ <strong style='color: red;'>Koneksi database GAGAL!</strong><br><br>";
    echo "<strong>Error:</strong> " . $e->getMessage() . "<br><br>";
    
    echo "<h3>🔧 Troubleshooting:</h3>";
    echo "<ol>";
    echo "<li>Pastikan password database sudah benar</li>";
    echo "<li>Cek informasi database di cPanel hosting</li>";
    echo "<li>Pastikan database sudah dibuat</li>";
    echo "<li>Cek apakah user database memiliki akses ke database</li>";
    echo "</ol>";
}

echo "<hr>";
echo "<h3>⚠️ PENTING:</h3>";
echo "<p style='color: red;'><strong>HAPUS FILE INI (test_db_hosting.php) SETELAH SELESAI TESTING!</strong></p>";
echo "<p>File ini mengandung informasi sensitif database.</p>";

echo "<br><small>Generated: " . date('Y-m-d H:i:s') . "</small>";
?>
