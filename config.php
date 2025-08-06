<?php
// File: config.php
$host = 'localhost';
$dbname = 'sewa_gedung';  // Sesuaikan dengan nama database di SQL file
$user = 'root'; // ganti jika pakai user lain
$pass = '';     // ganti sesuai password MySQL Anda

// PDO Connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// MySQLi Connection for admin panel
$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Koneksi MySQLi gagal: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");
?>
