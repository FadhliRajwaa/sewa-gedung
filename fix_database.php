<?php
// Fix database structure
require_once 'config.php';

echo "<h2>Memperbaiki Struktur Database</h2>";

try {
    // 1. Pastikan kolom email_terverifikasi ada di tabel penyewa
    $stmt = $pdo->query("SHOW COLUMNS FROM penyewa LIKE 'email_terverifikasi'");
    if ($stmt->rowCount() == 0) {
        echo "Menambahkan kolom email_terverifikasi...<br>";
        $pdo->exec("ALTER TABLE penyewa ADD COLUMN email_terverifikasi TINYINT(1) DEFAULT 1");
        echo "✅ Kolom email_terverifikasi berhasil ditambahkan<br>";
    } else {
        echo "✅ Kolom email_terverifikasi sudah ada<br>";
    }
    
    // 2. Update semua penyewa yang sudah ada menjadi terverifikasi (untuk development)
    $stmt = $pdo->exec("UPDATE penyewa SET email_terverifikasi = 1 WHERE email_terverifikasi IS NULL OR email_terverifikasi = 0");
    echo "✅ Semua penyewa existing sudah diset sebagai terverifikasi<br>";
    
    // 3. Pastikan tabel verifikasi_email ada
    $checkTable = $pdo->query("SHOW TABLES LIKE 'verifikasi_email'")->rowCount();
    if ($checkTable == 0) {
        echo "Membuat tabel verifikasi_email...<br>";
        $pdo->exec("
            CREATE TABLE verifikasi_email (
                id_verifikasi INT AUTO_INCREMENT PRIMARY KEY,
                id_penyewa INT NOT NULL,
                kode_verifikasi VARCHAR(6) NOT NULL,
                tanggal_dibuat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (id_penyewa) REFERENCES penyewa(id_penyewa)
            )
        ");
        echo "✅ Tabel verifikasi_email berhasil dibuat<br>";
    } else {
        echo "✅ Tabel verifikasi_email sudah ada<br>";
    }
    
    // 4. Hash password admin jika belum di-hash
    $stmt = $pdo->prepare("SELECT password FROM admin WHERE username = 'will'");
    $stmt->execute();
    $adminData = $stmt->fetch();
    
    if ($adminData && strlen($adminData['password']) < 60) { // Password belum di-hash
        echo "Mengupdate password admin menjadi hash...<br>";
        $hashedPassword = password_hash('will123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE admin SET password = ? WHERE username = 'will'");
        $stmt->execute([$hashedPassword]);
        echo "✅ Password admin berhasil di-hash<br>";
    } else {
        echo "✅ Password admin sudah dalam format hash<br>";
    }
    
    echo "<h3>Database siap digunakan!</h3>";
    echo "<p><a href='register.php'>Test Registrasi</a> | <a href='login.php'>Test Login</a> | <a href='admin/login.php'>Admin Login</a></p>";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
