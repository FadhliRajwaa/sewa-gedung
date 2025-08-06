<?php
// Mulai sesi
session_start();

// Hapus semua sesi yang ada
session_unset();

// Hancurkan sesi
session_destroy();

// Arahkan pengguna kembali ke halaman login setelah logout
header("Location: login.php");
exit();
?>
