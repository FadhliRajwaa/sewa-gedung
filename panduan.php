<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan - PT. Aneka Usaha</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            line-height: 1.6;
        }

        /* Modern Navbar */
        .navbar {
            background: linear-gradient(135deg, #B8860B 0%, #DAA520 100%);
            backdrop-filter: blur(20px);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .nav-logo img {
            height: 45px;
            width: auto;
        }

        .nav-logo .logo-text {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 30px;
            align-items: center;
        }

        .nav-link {
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 25px;
        }

        .nav-link:hover {
            color: #F4E4BC !important;
            background: rgba(255, 255, 255, 0.2);
            text-decoration: none;
            transform: translateY(-2px);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.3);
            color: #F4E4BC !important;
        }

        .nav-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
            gap: 4px;
        }

        .nav-toggle span {
            width: 25px;
            height: 3px;
            background: #8B4513;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        /* Main Content */
        .main-content {
            margin-top: 100px;
            padding: 40px 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .panduan-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #666;
            font-size: 1.1rem;
        }

        /* Steps */
        .step {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border-left: 5px solid #8B4513;
            transition: all 0.3s ease;
        }

        .step.panduan-penyewa {
            background: #fff3cd;
            border-left: 5px solid #ffc107;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.2);
        }

        .step:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .step.panduan-penyewa:hover {
            box-shadow: 0 15px 30px rgba(255, 193, 7, 0.3);
        }

        .step-number {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .step-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .step-description {
            color: #666;
            line-height: 1.7;
        }

        .step-icon {
            float: right;
            font-size: 2rem;
            color: #8B4513;
            opacity: 0.3;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .nav-menu {
                position: fixed;
                left: -100%;
                top: 70px;
                flex-direction: column;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(20px);
                width: 100%;
                text-align: center;
                transition: 0.3s;
                box-shadow: 0 10px 27px rgba(0, 0, 0, 0.05);
                padding: 20px 0;
            }

            .nav-menu.active {
                left: 0;
            }

            .nav-toggle {
                display: flex;
            }

            .panduan-container {
                padding: 25px 20px;
                margin: 15px;
            }

            .page-title {
                font-size: 2rem;
            }

            .main-content {
                margin-top: 80px;
                padding: 20px 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Modern Navbar -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <img src="logo.png" alt="PT. Aneka Usaha" onerror="this.style.display='none'">
                <div class="logo-text">PT. ANEKA USAHA</div>
            </div>
            
            <ul class="nav-menu" id="navMenu">
                <?php if (isset($_SESSION['id_penyewa'])): ?>
                    <li><a href="dashboard_user.php" class="nav-link"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="gedung.php" class="nav-link"><i class="fas fa-building"></i> Pilih Acara</a></li>
                    <li><a href="acara_saya.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Acara Saya</a></li>
                    <li><a href="panduan.php" class="nav-link active"><i class="fas fa-book"></i> Panduan</a></li>
                    <li><a href="akun.php" class="nav-link"><i class="fas fa-user"></i> Akun</a></li>
                    <li><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php else: ?>
                    <li><a href="index.php" class="nav-link"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="gedung.php" class="nav-link"><i class="fas fa-building"></i> Gedung</a></li>
                    <li><a href="panduan.php" class="nav-link active"><i class="fas fa-book"></i> Panduan</a></li>
                    <li><a href="kontak.php" class="nav-link"><i class="fas fa-phone"></i> Kontak</a></li>
                    <li><a href="login.php" class="nav-link"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                <?php endif; ?>
            </ul>
            
            <div class="nav-toggle" id="navToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="panduan-container">
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-book-open"></i>
                        Panduan Sewa Gedung
                    </h1>
                    <p class="page-subtitle">
                        Ikuti langkah-langkah mudah untuk menyewa gedung acara Anda
                    </p>
                </div>

                <div class="step panduan-penyewa">
                    <i class="fas fa-user-plus step-icon"></i>
                    <div class="step-number">1</div>
                    <h3 class="step-title">Registrasi Akun Penyewa</h3>
                    <p class="step-description">
                        Daftarkan akun terlebih dahulu sebagai penyewa umum atau instansi. Isi data diri Anda dengan lengkap dan benar untuk memudahkan proses verifikasi.
                    </p>
                </div>

                <div class="step panduan-penyewa">
                    <i class="fas fa-envelope-circle-check step-icon"></i>
                    <div class="step-number">2</div>
                    <h3 class="step-title">Verifikasi Email Penyewa</h3>
                    <p class="step-description">
                        Verifikasi alamat email Anda dengan kode verifikasi yang dikirim. Pastikan email yang digunakan aktif untuk menerima notifikasi pemesanan.
                    </p>
                </div>

                <div class="step panduan-penyewa">
                    <i class="fas fa-sign-in-alt step-icon"></i>
                    <div class="step-number">3</div>
                    <h3 class="step-title">Login ke Dashboard Penyewa</h3>
                    <p class="step-description">
                        Masuk ke akun Anda menggunakan username dan password yang telah didaftarkan. Setelah login, Anda dapat mengakses dashboard user.
                    </p>
                </div>

                <div class="step panduan-penyewa">
                    <i class="fas fa-calendar-check step-icon"></i>
                    <div class="step-number">4</div>
                    <h3 class="step-title">Pilih dan Pesan Acara untuk Penyewa</h3>
                    <p class="step-description">
                        Pilih jenis acara yang sesuai (Pernikahan, Rapat, atau Seminar). Isi formulir pemesanan dengan detail tanggal, durasi, dan kebutuhan tambahan.
                    </p>
                </div>

                <div class="step panduan-penyewa">
                    <i class="fas fa-credit-card step-icon"></i>
                    <div class="step-number">5</div>
                    <h3 class="step-title">Pembayaran Penyewa</h3>
                    <p class="step-description">
                        Transfer pembayaran sesuai total biaya ke rekening yang tersedia. Upload bukti pembayaran untuk verifikasi oleh admin.
                    </p>
                </div>

                <div class="step panduan-penyewa">
                    <i class="fas fa-check-circle step-icon"></i>
                    <div class="step-number">6</div>
                    <h3 class="step-title">Konfirmasi & Cetak Nota Penyewa</h3>
                    <p class="step-description">
                        Setelah pembayaran dikonfirmasi lunas oleh admin, Anda dapat mencetak nota sebagai bukti sah pemesanan gedung acara.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Mobile navigation toggle
        const navToggle = document.getElementById('navToggle');
        const navMenu = document.getElementById('navMenu');

        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });

        // Close menu when clicking on a link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
            });
        });
    </script>
</body>
</html>
