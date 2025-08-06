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
            text-decoration: none;
        }
        
        .btn-mulai:hover {
            background-color: #DAA520;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(184, 134, 11, 0.4);
            color: white;
            text-decoration: none;
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
            position: relative;
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
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="logoAU.png" alt="PT Aneka Usaha">
                <span>PT ANEKA USAHA</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#acara">Acara</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="panduan.php">Panduan Penyewaan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="kontak.php">Hubungi Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Akun</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1>PENYEWAAN GEDUNG SERBAGUNA<br>PT ANEKA USAHA KABUPATEN<br>PEMALANG (PERSERODA)</h1>
            <p>Kami memiliki semangat dalam menyediakan penyewaan<br>gedung yang aman dan nyaman</p>
            <a href="login.php" class="btn-mulai">Mulai Penyewaan</a>
        </div>
    </section>

    <!-- Pilihan Acara Section -->
    <section class="pilihan-acara" id="acara">
        <div class="container">
            <h2>PILIHAN ACARA</h2>
            <div class="row">
                <!-- Seminar Card -->
                <div class="col-lg-4 col-md-6">
                    <div class="acara-card">
                        <img src="asset/gambar/gedung_seminar.jpg" alt="Seminar">
                        <div class="acara-card-body">
                            <h3>Seminar</h3>
                            <p>Cocok untuk seminar dan konferensi.</p>
                            <div class="acara-details">
                                <p><strong>Harga Sewa Rp 5.000.000;</strong><br>
                                Kapasitas : 1.000 Orang<br>
                                Lokasi : Jl. Jenderal Sudirman No 1, Pemalang<br>
                                Fasilitas : Panggung Utama, Halaman Parkir Luas, Kipas Angin, Toilet<br>
                                Status : Tersedia</p>
                            </div>
                            <button class="btn btn-cek" onclick="location.href='seminar.php'">Cek Tanggal</button>
                        </div>
                    </div>
                </div>

                <!-- Pernikahan Card -->
                <div class="col-lg-4 col-md-6">
                    <div class="acara-card">
                        <img src="asset/gambar/gedung_pernikahan.jpg" alt="Pernikahan">
                        <div class="acara-card-body">
                            <h3>Pernikahan</h3>
                            <p>Fasilitas lengkap untuk acara Anda.</p>
                            <div class="acara-details">
                                <p><strong>Harga Sewa Rp 5.150.000;</strong><br>
                                Kapasitas : 1.000 Orang<br>
                                Lokasi : Jl. Jenderal Sudirman No 1, Pemalang<br>
                                Fasilitas : Panggung Utama, Halaman Parkir Luas, Kipas Angin, Toilet<br>
                                Status : Tersedia</p>
                            </div>
                            <button class="btn btn-cek" onclick="location.href='pernikahan.php'">Cek Tanggal</button>
                        </div>
                    </div>
                </div>

                <!-- Rapat Card -->
                <div class="col-lg-4 col-md-6">
                    <div class="acara-card">
                        <img src="asset/gambar/gedung_rapat.jpg" alt="Rapat">
                        <div class="acara-card-body">
                            <h3>Rapat</h3>
                            <p>Ideal untuk acara kecil dan intimate.</p>
                            <div class="acara-details">
                                <p><strong>Harga Sewa Rp 3.885.000;</strong><br>
                                Kapasitas : 20 Orang<br>
                                Lokasi : Jl. Jenderal Sudirman No 1, Pemalang<br>
                                Fasilitas : Panggung Utama, Halaman Parkir Luas, Maja, Kursi, AC, Toilet<br>
                                Status : Tersedia</p>
                            </div>
                            <button class="btn btn-cek" onclick="location.href='rapat.php'">Cek Tanggal</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Panduan Penyewaan Section -->
    <section class="panduan-section">
        <div class="container">
            <h2>PANDUAN PENYEWAAN</h2>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="panduan-step">
                        <div class="panduan-icon">
                            📝
                            <div class="step-number">1</div>
                        </div>
                        <h4>Registrasi akun terlebih dahulu</h4>
                        <p>atau sebagai umum atau instansi</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="panduan-step">
                        <div class="panduan-icon">
                            ✉️
                            <div class="step-number">2</div>
                        </div>
                        <h4>Verifikasi email anda dengan kode</h4>
                        <p>yang dikirim</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="panduan-step">
                        <div class="panduan-icon">
                            👤
                            <div class="step-number">3</div>
                        </div>
                        <h4>Login ke akun anda setelah verifikasi</h4>
                        <p>berhasil</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="panduan-step">
                        <div class="panduan-icon">
                            📋
                            <div class="step-number">4</div>
                        </div>
                        <h4>Pilih acara dan isi formulir</h4>
                        <p>pemesanan sesuai kebutuhan</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="panduan-step">
                        <div class="panduan-icon">
                            💳
                            <div class="step-number">5</div>
                        </div>
                        <h4>Lakukan pembayaran sesuai</h4>
                        <p>metode yang tersedia dan upload bukti pembayaran</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="panduan-step">
                        <div class="panduan-icon">
                            🧾
                            <div class="step-number">6</div>
                        </div>
                        <h4>Cetak nota sebagai bukti pemesanan</h4>
                        <p>gedung Anda</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center mb-3">
                        <img src="logoAU.png" alt="PT Aneka Usaha" width="50" class="mr-3">
                        <h5>PT ANEKA USAHA</h5>
                    </div>
                    <p>Menyediakan Penyewaan gedung secara online terpercaya ideal, aman dan nyaman. Memberikan pelayanan penyewaan seperti seminar, pernikahan dan acara-acara lainnya. Terletak di Jl. Jenderal Sudirman No 1, Pemalang, Jawa Tengah 52312</p>
                </div>
                <div class="col-lg-4">
                    <h5>Hubungi Kami</h5>
                    <p>📍 Jl. Jenderal Sudirman No 1,<br>Pemalang, Jawa Tengah 52312</p>
                    <p>📞 0285-22198-28</p>
                    <p>✉️ info@anekaperseroda.co.id</p>
                </div>
                <div class="col-lg-4">
                    <h5>MAPS</h5>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Company All rights Reserved</p>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
