<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include '../config.php';  // Mengakses file config.php
include '../includes/db.php';  // Mengakses file db.php

// Query untuk mendapatkan histori transaksi
$transaction_query = "SELECT p.id_pemesanan, p.id_penyewa, p.tanggal_sewa, p.tanggal_selesai, p.total, p.metode_pembayaran, 
                             pb.status_pembayaran, pb.bukti_pembayaran 
                      FROM pemesanan p 
                      LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan";

// Mengeksekusi query dan memeriksa apakah berhasil
$transaction_result = mysqli_query($conn, $transaction_query);

// Jika query gagal, tampilkan error
if (!$transaction_result) {
    die('Query error: ' . mysqli_error($conn));  // Menampilkan pesan error jika query gagal
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php include 'includes/admin_styles.php'; ?>
</head>
<body>
    <div class="container">
        <?php include 'includes/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1><i class="fas fa-history"></i> Riwayat Transaksi</h1>
            </div>

            <!-- Transaction Table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID Penyewa</th>
                            <th>Tanggal Sewa</th>
                            <th>Tanggal Selesai</th>
                            <th>Total</th>
                            <th>Metode Pembayaran</th>
                            <th>Status Pembayaran</th>
                            <th>Bukti Pembayaran</th>

        .btn-view {
            background-color: #28a745;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
        }

        .btn-view:hover {
            background-color: #218838;
        }

        /* Responsif untuk layar kecil */
        @media (max-width: 768px) {
            table th, table td {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Histori Transaksi</h2>

        <!-- Tabel Histori Transaksi -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID Pemesanan</th>
                    <th>ID Penyewa</th>
                    <th>Tanggal Sewa</th>
                    <th>Tanggal Selesai</th>
                    <th>Total</th>
                    <th>Metode Pembayaran</th>
                    <th>Status Pembayaran</th>
                    <th>Bukti Pembayaran</th>
                </tr>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($transaction_result) > 0): ?>
            <?php while ($transaction = mysqli_fetch_assoc($transaction_result)): ?>
                <tr>
                    <td><?= htmlspecialchars($transaction['id_pemesanan']) ?></td>
                    <td><?= htmlspecialchars($transaction['id_penyewa']) ?></td>
                    <td><?= date('d/m/Y', strtotime($transaction['tanggal_sewa'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($transaction['tanggal_selesai'])) ?></td>
                    <td>Rp <?= number_format($transaction['total'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($transaction['metode_pembayaran']) ?></td>
                    <td>
                        <?php 
                        $status = $transaction['status_pembayaran'];
                        $badgeClass = 'badge-info';
                        if ($status == 'Diterima') $badgeClass = 'badge-success';
                        elseif ($status == 'Ditolak') $badgeClass = 'badge-danger';
                        elseif ($status == 'Menunggu Verifikasi') $badgeClass = 'badge-warning';
                        ?>
                        <span class="badge <?= $badgeClass ?>">
                            <?= htmlspecialchars($status ?: 'Belum Upload') ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($transaction['bukti_pembayaran']): ?>
                            <a href="uploads/<?= htmlspecialchars($transaction['bukti_pembayaran']) ?>" target="_blank" class="btn btn-info">
                                <i class="fas fa-eye"></i> Lihat Bukti
                            </a>
                        <?php else: ?>
                            <span class="text-muted">
                                <i class="fas fa-times"></i> Belum Upload
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align: center; padding: 30px; color: #666;">
                    <i class="fas fa-inbox" style="font-size: 48px; color: #ddd; margin-bottom: 10px;"></i><br>
                    Belum ada transaksi
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
            </div>
        </main>
    </div>
</body>
</html>
