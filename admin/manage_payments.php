<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include '../config.php';  // Mengakses file config.php
include '../includes/db.php';  // Mengakses file db.php

// Query untuk mendapatkan daftar pembayaran
$payment_query = "SELECT p.id_pemesanan, pb.id_pembayaran, pb.bukti_pembayaran, pb.status, pb.tanggal_upload 
                  FROM pemesanan p
                  JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
                  WHERE pb.status = 'Menunggu Verifikasi'";
$payment_result = mysqli_query($conn, $payment_query);

// Proses verifikasi pembayaran
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pembayaran = $_POST['id_pembayaran'];
    $status_pembayaran = $_POST['status_pembayaran'];

    $update_sql = "UPDATE pembayaran SET status = '$status_pembayaran' WHERE id_pembayaran = '$id_pembayaran'";
    
    if (mysqli_query($conn, $update_sql)) {
        echo "Status pembayaran berhasil diperbarui!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pembayaran - Admin</title>
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
                <h1><i class="fas fa-credit-card"></i> Kelola Pembayaran</h1>
            </div>

            <!-- Payment Table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID Pembayaran</th>
                            <th>ID Pemesanan</th>
                            <th>Bukti Pembayaran</th>
                            <th>Status</th>
                            <th>Tanggal Upload</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($payment_result) > 0): ?>
                            <?php while ($payment = mysqli_fetch_assoc($payment_result)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($payment['id_pembayaran']) ?></td>
                                    <td><?= htmlspecialchars($payment['id_pemesanan']) ?></td>
                                    <td>
                                        <a href="uploads/<?= htmlspecialchars($payment['bukti_pembayaran']) ?>" target="_blank" class="btn btn-info">
                                            <i class="fas fa-eye"></i> Lihat Bukti
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">
                                            <?= htmlspecialchars($payment['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($payment['tanggal_upload'])) ?></td>
                                    <td>
                                        <form method="POST" action="manage_payments.php" style="display: inline-block;">
                                            <input type="hidden" name="id_pembayaran" value="<?= $payment['id_pembayaran'] ?>">
                                            <select name="status_pembayaran" class="form-select" style="width: auto; display: inline-block; margin-right: 10px;">
                                                <option value="Diterima">Diterima</option>
                                                <option value="Ditolak">Ditolak</option>
                                            </select>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check"></i> Verifikasi
                                            </button>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px; color: #666;">
                                    <i class="fas fa-inbox" style="font-size: 48px; color: #ddd; margin-bottom: 10px;"></i><br>
                                    Tidak ada pembayaran yang menunggu verifikasi
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
