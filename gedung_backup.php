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
    <title>Pilih Gedung</title>
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

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
            cursor: pointer;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card-img-top {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .card-body {
            text-align: center;
            padding: 25px;
        }

        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: #8C4B12;
        }

        footer {
            text-align: center;
            padding: 20px 0;
            background-color: #343a40;
            color: white;
        }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1 class="text-center">Pilih Gedung</h1>
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
        <div class="row text-center">
            <!-- Gedung Pernikahan -->
            <div class="col-md-4 mb-4">
                <div class="card shadow" onclick="window.location='pernikahan.php'">
                    <img src="asset/gambar/gedung_pernikahan.jpg" class="card-img-top" alt="Gedung Pernikahan">
                    <div class="card-body">
                        <h5 class="card-title">Gedung Pernikahan</h5>
                        <p>Kapasitas: 1000 orang. Fasilitas lengkap untuk acara pernikahan.</p>
                    </div>
                </div>
            </div>

            <!-- Gedung Rapat -->
            <div class="col-md-4 mb-4">
                <div class="card shadow" onclick="window.location='rapat.php'">
                    <img src="asset/gambar/gedung_rapat.jpg" class="card-img-top" alt="Gedung Rapat">
                    <div class="card-body">
                        <h5 class="card-title">Gedung Rapat</h5>
                        <p>Kapasitas: 50 orang. Cocok untuk rapat dan konferensi.</p>
                    </div>
                </div>
            </div>

            <!-- Gedung Seminar -->
            <div class="col-md-4 mb-4">
                <div class="card shadow" onclick="window.location='seminar.php'">
                    <img src="asset/gambar/gedung_seminar.jpg" class="card-img-top" alt="Gedung Seminar">
                    <div class="card-body">
                        <h5 class="card-title">Gedung Seminar</h5>
                        <p>Kapasitas: 500 orang. Ideal untuk seminar dan konferensi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>
