<?php
// Pengaturan untuk koneksi ke database
$servername = "localhost"; // Ganti dengan nama server database Anda (misalnya, localhost)
$username = "root";        // Ganti dengan username database Anda (biasanya 'root' di localhost)
$password = "";            // Ganti dengan password database Anda (biarkan kosong jika tidak ada)
$dbname = "sewa_gedung"; // Ganti dengan nama database Anda

// Membuat koneksi ke database
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Mengecek apakah koneksi berhasil
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
