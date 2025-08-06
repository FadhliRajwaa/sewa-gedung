<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Aneka Usaha - Penyewaan Gedung Serbaguna</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        
        /* Header */
        .navbar {
            background-color: #B8860B !important;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            display: flex;
            align-items: center;
        }
        
        .navbar-brand img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }
        
        .navbar-brand span {
            color: white;
            font-weight: 700;
            font-size: 18px;
        }
        
        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
            margin: 0 15px;
            transition: color 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover {
            color: #FFD700 !important;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('asset/gambar/bg.jpg');
            background-size: cover;
            background-position: center;
            height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }
        
        .hero-content h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        
        .btn-mulai {
            background-color: #B8860B;
            border: none;
            color: white;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        
        .btn-mulai:hover {
            background-color: #DAA520;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(184, 134, 11, 0.4);
        }
        
        /* Pilihan Acara Section */
        .pilihan-acara {
            padding: 80px 0;
            background-color: #B8860B;
        }
        
        .pilihan-acara h2 {
            text-align: center;
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 50px;
        }
        
        .acara-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 30px;
        }
        
        .acara-card:hover {
            transform: translateY(-10px);
        }
        
        .acara-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .acara-card-body {
            padding: 25px;
        }
        
        .acara-card h3 {
            color: #B8860B;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .acara-card p {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .acara-details {
            font-size: 14px;
            color: #888;
            margin-bottom: 20px;
        }
        
        .btn-cek {
            background-color: #B8860B;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-cek:hover {
            background-color: #DAA520;
            transform: translateY(-2px);
        }
        
        /* Panduan Section */
        .panduan-section {
            padding: 80px 0;
            background-color: #B8860B;
        }
        
        .panduan-section h2 {
            text-align: center;
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 50px;
        }
        
        .panduan-step {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .panduan-icon {
            width: 80px;
            height: 80px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
            color: #B8860B;
        }
        
        .step-number {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: #DAA520;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }
        
        .panduan-step h4 {
            color: white;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .panduan-step p {
            color: #f0f0f0;
            font-size: 14px;
        }
        
        /* Footer */
        .footer {
            background-color: #2c3e50;
            color: white;
            padding: 50px 0 20px;
        }
        
        .footer h5 {
            color: #DAA520;
            margin-bottom: 20px;
            font-weight: 700;
        }
        
        .footer p, .footer a {
            color: #bdc3c7;
            text-decoration: none;
            line-height: 1.8;
        }
        
        .footer a:hover {
            color: #DAA520;
        }
        
        .footer-bottom {
            border-top: 1px solid #34495e;
            margin-top: 30px;
            padding-top: 20px;
            text-align: center;
        }
    </style>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
        }
        header {
            background-color: #8C4B12; /* Dark gold color */
            color: white;
            padding: 20px 0;
        }
        .header-logo {
            width: 150px; /* Larger size for the logo */
            height: auto;
            margin-right: 15px;
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav {
            text-align: right; /* Align the nav to the right */
        }
        nav a {
            color: white;
            margin: 0 15px;
            transition: color 0.3s;
        }
        nav a:hover {
            color: #F6A800; /* Light gold on hover */
        }
        /* Stylish Section with Background Image for "Mulai Penyewaan" Button */
        .start-rental-section {
            background-image: url('asset/gambar/bg.jpg'); /* Replace with your image path */
            background-size: cover;
            background-position: center;
            height: 500px;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .start-rental-section h2 {
            font-size: 2.5rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }
        .btn-start {
            background-color: #F6A800; /* Light gold */
            color: white;
            font-size: 1.25rem;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
            margin-top: 20px;
        }
        .btn-start:hover {
            background-color: #8C4B12; /* Dark gold */
        }
        main {
            padding: 40px 0;
        }
        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card img {
            border-radius: 15px 15px 0 0;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            background-color: #343a40;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <div>
                <img src="logo.png" alt="Logo PT. Aneka Usaha" class="header-logo">
            </div>
            <nav>
                <a href="index.php">Home</a> |
                <a href="acara.php">Acara</a> |
                <a href="panduan.php">Panduan Cara Sewa</a> |
                <a href="kontak.php">Kontak</a> |
                <?php if (!empty($_SESSION['id_penyewa'])): ?>
                    <a href="akun.php">Akun</a> |
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a> |
                    <a href="register.php">Daftar</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Stylish Section with Background Image and "Mulai Penyewaan" Button -->
    <section class="start-rental-section">
        <div>
            <h2>Penyewaan Gedung Serba Guna PT. Aneka Usaha Kabupaten Pemalang (PERSERODA)</h2>
            <a href="sewa.php" class="btn btn-start">Mulai Penyewaan</a>
        </div>
    </section>

    <main>
        <div class="container">
            <h2 class="text-center mb-4">Selamat Datang di Layanan Sewa Gedung</h2>
            <p class="text-center mb-5">Temukan solusi terbaik untuk keperluan acara Anda dengan fasilitas gedung dari PT. Aneka Usaha.</p>
            
            <div class="row">
                <!-- Contoh Kartu Produk -->
                <div class="col-md-4">
                    <div class="card shadow">
                        <img src="asset/gambar/gedung1.jpg" class="card-img-top" alt="Gedung 1">
                        <div class="card-body">
                            <h5 class="card-title">Gedung A</h5>
                            <p class="card-text">Kapasitas: 200 orang. Fasilitas lengkap untuk acara Anda.</p>
                            <a href="sewa.php?gedung=A" class="btn btn-primary">Sewa Sekarang</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow">
                        <img src="asset/gambar/gedung2.jpg" class="card-img-top" alt="Gedung 2">
                        <div class="card-body">
                            <h5 class="card-title">Gedung B</h5>
                            <p class="card-text">Kapasitas: 300 orang. Cocok untuk seminar dan konferensi.</p>
                            <a href="sewa.php?gedung=B" class="btn btn-primary">Sewa Sekarang</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow">
                        <img src="asset/gambar/gedung3.jpg" class="card-img-top" alt="Gedung 3">
                        <div class="card-body">
                            <h5 class="card-title">Gedung C</h5>
                            <p class="card-text">Kapasitas: 150 orang. Ideal untuk acara kecil dan intimate.</p>
                            <a href="sewa.php?gedung=C" class="btn btn-primary">Sewa Sekarang</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> PT. Aneka Usaha Perseroda</p>
    </footer>
</body>
</html>
