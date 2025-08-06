<?php
// Memulai session
session_start();

// Menghapus semua session
session_unset(); 

// Menghancurkan session
session_destroy(); 

// Arahkan kembali ke halaman login setelah logout
header("Location: login.php");
exit();
?>
