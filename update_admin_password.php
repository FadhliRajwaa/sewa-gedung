<?php
// File ini digunakan untuk mengupdate password admin yang sudah ada menjadi hash
require_once 'config.php';

try {
    // Hash password untuk admin yang sudah ada
    $newPassword = password_hash('will123', PASSWORD_BCRYPT);
    
    $stmt = $pdo->prepare("UPDATE admin SET password = ? WHERE username = 'will'");
    $stmt->execute([$newPassword]);
    
    echo "Password admin berhasil diupdate menjadi hash!<br>";
    echo "Username: will<br>";
    echo "Password: will123<br>";
    echo "Password Hash: " . $newPassword . "<br>";
    
    // Tambahkan admin baru jika diperlukan
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO admin (nama_admin, no_telepon, email, alamat, username, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['Administrator', '08123456789', 'admin@ptanekaperseroda.com', 'Jakarta', 'admin', $adminPassword]);
        echo "<br>Admin baru ditambahkan:<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
