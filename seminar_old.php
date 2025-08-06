<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['id_penyewa'])) {
    header('Location: login.php');
    exit;
}

$nama = $_SESSION['nama_lengkap'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Gedung Seminar</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
        }

        header {
            background-color: #8C4B12;
            color: white;
            padding: 20px 0;
        }

        .header-logo {
            max-width: 150px;
            height: auto;
        }

        nav {
            text-align: right;
        }

        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #F6A800;
        }

        main {
            padding: 40px 0;
        }

        .gedung-info {
            background-color: #f8f1e5;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .gedung-info img {
            width: 100%;
            border-radius: 10px;
        }

        .gedung-info h2 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #8C4B12;
        }

        .form-group label {
            font-weight: 500;
        }

        .btn-primary {
            background-color: #F6A800;
            border: none;
            color: white;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #8C4B12;
        }

        .rincian-harga {
            font-weight: 500;
            font-size: 1.2rem;
            color: #F6A800;
        }

        .informasi {
            margin-top: 20px;
            font-size: 1rem;
            color: #555;
        }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1 class="text-center">Booking Gedung Seminar</h1>
        <nav class="text-center">
            <a href="dashboard_user.php">Beranda</a> |
            <a href="acara_saya.php">Acara Saya</a> |
            <a href="panduan.php">Panduan</a> |
            <a href="akun.php">Akun</a> |
            <a href="logout.php">Logout</a>
        </nav>
    </div>
</header>

<main>
    <div class="container">
        <div class="gedung-info">
            <h2>Seminar</h2>
            <img src="asset/gambar/gedung_seminar.jpg" alt="Gedung Seminar">

            <form action="proses_booking.php" method="POST">
                <div class="form-group">
                    <label for="tanggal_mulai">Tanggal Mulai:</label>
                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                </div>

                <div class="form-group">
                    <label for="tanggal_selesai">Tanggal Selesai:</label>
                    <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                </div>

                <div class="form-group">
                    <label for="rincian_harga">Rincian Harga:</label>
                    <input type="text" class="form-control" id="rincian_harga" name="rincian_harga" value="Harga sewa Rp 4.500.000" readonly>
                </div>

                <div class="form-group">
                    <label>Informasi:</label>
                    <textarea class="form-control" rows="5" readonly>
Ketersediaan gedung dapat berubah sewaktu-waktu. Mohon melakukan pengecekan sebelumnya di tanggal dan waktu operasional untuk konfirmasi sebelum melakukan penyewaan. Pengurus gedung PT. Aneka Usaha tidak bertanggung jawab apabila terjadi pembatalan saat melakukan penyewaan gedung yang telah dikonfirmasi pengguna sebelumnya. Terimakasih.
                    </textarea>
                </div>

                <button type="submit" class="btn btn-primary">Pesan Sekarang</button>
            </form>
        </div>
    </div>
</main>

</body>
</html>
