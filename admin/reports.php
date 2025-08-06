<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include '../config.php';  // Mengakses file config.php
include '../includes/db.php';  // Mengakses file db.php

// Query untuk mendapatkan laporan penyewa dan data pemesanan
$penyewa_query = "
    SELECT 
        penyewa.id_penyewa,
        penyewa.nama_lengkap,
        penyewa.no_telepon,
        penyewa.email,
        penyewa.alamat,
        pemesanan.tanggal_sewa,
        pemesanan.durasi,
        pemesanan.total
    FROM penyewa
    JOIN pemesanan ON penyewa.id_penyewa = pemesanan.id_penyewa";

$penyewa_result = mysqli_query($conn, $penyewa_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penyewa</title>
    <!-- Menambahkan Bootstrap untuk desain responsif dan tabel yang rapi -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .container {
            margin-top: 50px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            margin-top: 20px;
        }

        table th, table td {
            text-align: center;
        }

        table th {
            background-color: #007bff;
            color: white;
            padding: 12px;
        }

        table td {
            padding: 10px;
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
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
        <h2>Laporan Penyewa</h2>

        <!-- Tabel Laporan Penyewa -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID Penyewa</th>
                    <th>Nama Penyewa</th>
                    <th>No. Telepon</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th>Tanggal Sewa</th>
                    <th>Durasi</th>
                    <th>Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($penyewa = mysqli_fetch_assoc($penyewa_result)) { ?>
                    <tr>
                        <td><?php echo $penyewa['id_penyewa']; ?></td>
                        <td><?php echo $penyewa['nama_lengkap']; ?></td>
                        <td><?php echo $penyewa['no_telepon']; ?></td>
                        <td><?php echo $penyewa['email']; ?></td>
                        <td><?php echo $penyewa['alamat']; ?></td>
                        <td><?php echo $penyewa['tanggal_sewa']; ?></td>
                        <td><?php echo $penyewa['durasi']; ?> hari</td>
                        <td><?php echo number_format($penyewa['total'], 2, ',', '.'); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Menambahkan JS Bootstrap dan jQuery untuk interaksi dan responsivitas -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
