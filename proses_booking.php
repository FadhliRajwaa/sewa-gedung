<?php
session_start();
require_once 'config.php';

// Cek apakah user sudah login
if (!isset($_SESSION['id_penyewa'])) {
    header("Location: login.php");
    exit();
}

$id_penyewa = $_SESSION['id_penyewa'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Get form data
        $id_acara = $_POST['id_acara'];
        $tanggal_sewa = $_POST['tanggal_sewa'];
        $tanggal_selesai = $_POST['tanggal_selesai'];
        $kebutuhan_tambahan = $_POST['kebutuhan_tambahan'] ?? '';
        $metode_pembayaran = $_POST['metode_pembayaran'];
        $tipe_pesanan = 'online';
        
        // Calculate duration
        $date1 = new DateTime($tanggal_sewa);
        $date2 = new DateTime($tanggal_selesai);
        $durasi = $date2->diff($date1)->days + 1;
        
        // Get event price
        $event_query = "SELECT harga FROM acara WHERE id_acara = ?";
        $event_stmt = $pdo->prepare($event_query);
        $event_stmt->execute([$id_acara]);
        $event = $event_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$event) {
            throw new Exception("Acara tidak ditemukan");
        }
        
        // Calculate total
        $total = $event['harga'] * $durasi;
        
        // Insert booking
        $insert_query = "
            INSERT INTO pemesanan (id_penyewa, id_acara, tanggal_sewa, tanggal_selesai, durasi, kebutuhan_tambahan, total, metode_pembayaran, tipe_pesanan, tanggal_pesan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ";
        $insert_stmt = $pdo->prepare($insert_query);
        $insert_stmt->execute([
            $id_penyewa,
            $id_acara,
            $tanggal_sewa,
            $tanggal_selesai,
            $durasi,
            $kebutuhan_tambahan,
            $total,
            $metode_pembayaran,
            $tipe_pesanan
        ]);
        
        $id_pemesanan = $pdo->lastInsertId();
        
        // Redirect to payment page
        header("Location: pembayaran.php?id=" . $id_pemesanan);
        exit();
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        // Redirect back with error
        header("Location: sewa.php?gedung=" . $_POST['gedung'] . "&error=" . urlencode($error_message));
        exit();
    }
} else {
    // If not POST request, redirect to home
    header("Location: index.php");
    exit();
}
?>
