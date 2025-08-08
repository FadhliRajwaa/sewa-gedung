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
    <title>Dashboard Penyewa - PT. Aneka Usaha</title>
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
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: linear-gradient(135deg, #B8860B 0%, #DAA520 100%);
            padding: 0.8rem 0;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
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
            line-height: 1.2;
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
            position: relative;
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
            background: #ffffff;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .nav-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .nav-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .nav-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        /* Main Content */
        .main-content {
            margin-top: 100px;
            padding: 40px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Hero Section */
        .hero-section {
            text-align: center;
            margin-bottom: 60px;
            padding: 40px 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 15px;
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .hero-subtitle {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .user-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 5px;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        /* Feature Cards */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(139, 69, 19, 0.1) 0%, rgba(160, 82, 45, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: white;
            box-shadow: 0 10px 25px rgba(139, 69, 19, 0.3);
        }

        .feature-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .feature-description {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .feature-btn {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(139, 69, 19, 0.3);
        }

        .feature-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 69, 19, 0.4);
            color: white;
            text-decoration: none;
        }

        /* Footer */
        .footer {
            margin-top: 80px;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(20px);
            color: white;
            text-align: center;
            padding: 30px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .nav-menu {
                position: fixed;
                top: 100%;
                left: 0;
                width: 100%;
                background: linear-gradient(135deg, #B8860B 0%, #DAA520 100%);
                backdrop-filter: blur(20px);
                flex-direction: column;
                gap: 0;
                padding: 20px 0;
                transform: translateY(-100%);
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            }

            .nav-menu.active {
                transform: translateY(0);
                opacity: 1;
                visibility: visible;
            }

            .nav-menu li {
                width: 100%;
                text-align: center;
                padding: 10px 0;
            }
            
            .nav-menu .nav-link {
                color: #ffffff !important;
                font-weight: 500;
            }
            
            .nav-menu .nav-link:hover {
                color: #F4E4BC !important;
                background: rgba(255, 255, 255, 0.2);
            }

            .nav-toggle {
                display: flex;
            }

            .hero-title {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .feature-card {
                padding: 25px 20px;
            }

            .main-content {
                margin-top: 80px;
                padding: 20px 15px;
            }

            .user-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 1.6rem;
            }

            .feature-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }

            .user-stats {
                grid-template-columns: 1fr;
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
                <li><a href="dashboard_user.php" class="nav-link active"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="gedung.php" class="nav-link"><i class="fas fa-building"></i> Pilih Acara</a></li>
                <li><a href="acara_saya.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Acara Saya</a></li>
                <li><a href="panduan.php" class="nav-link"><i class="fas fa-book"></i> Panduan</a></li>
                <li><a href="akun.php" class="nav-link"><i class="fas fa-user"></i> Akun</a></li>
                <li><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
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
            <!-- Hero Section -->
            <div class="hero-section">
                <h1 class="hero-title">
                    <i class="fas fa-star"></i>
                    Selamat Datang, <?= htmlspecialchars($nama) ?>!
                </h1>
                <p class="hero-subtitle">
                    Kelola penyewaan gedung dan acara Anda dengan mudah melalui dashboard modern kami
                </p>
                
                <!-- User Stats -->
                <div class="user-stats">
                    <div class="stat-card">
                        <div class="stat-number" id="totalBookings">0</div>
                        <div class="stat-label">Total Pesanan</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="activeBookings">0</div>
                        <div class="stat-label">Acara Aktif</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number" id="completedBookings">0</div>
                        <div class="stat-label">Acara Selesai</div>
                    </div>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="features-grid">
                <!-- Sewa Gedung -->
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 class="feature-title">Sewa Gedung</h3>
                    <p class="feature-description">
                        Temukan berbagai pilihan gedung untuk acara pernikahan, seminar, dan rapat dengan fasilitas lengkap
                    </p>
                    <a href="gedung.php" class="feature-btn">
                        <i class="fas fa-search"></i> Lihat Gedung
                    </a>
                </div>

                <!-- Acara Saya -->
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="feature-title">Acara Saya</h3>
                    <p class="feature-description">
                        Pantau status pembayaran, jadwal acara, dan kelola semua detail pesanan Anda dalam satu tempat
                    </p>
                    <a href="acara_saya.php" class="feature-btn">
                        <i class="fas fa-eye"></i> Lihat Acara
                    </a>
                </div>

                <!-- Kelola Akun -->
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <h3 class="feature-title">Kelola Akun</h3>
                    <p class="feature-description">
                        Perbarui informasi profil, ubah password, dan kelola pengaturan akun Anda dengan aman
                    </p>
                    <a href="akun.php" class="feature-btn">
                        <i class="fas fa-cog"></i> Kelola Akun
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?= date('Y') ?> PT. Aneka Usaha Perseroda. All rights reserved.</p>
    </footer>

    <script>
        // Mobile Navigation Toggle
        const navToggle = document.getElementById('navToggle');
        const navMenu = document.getElementById('navMenu');

        navToggle.addEventListener('click', () => {
            navToggle.classList.toggle('active');
            navMenu.classList.toggle('active');
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navToggle.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Animate statistics on page load
        function animateStats() {
            const stats = [
                { element: 'totalBookings', target: 0 },
                { element: 'activeBookings', target: 0 },
                { element: 'completedBookings', target: 0 }
            ];

            // You can fetch real data from your database here
            // For now, using placeholder values
            fetch('get_user_stats.php')
                .then(response => response.json())
                .then(data => {
                    stats[0].target = data.total || 0;
                    stats[1].target = data.active || 0;
                    stats[2].target = data.completed || 0;
                    
                    stats.forEach(stat => {
                        animateNumber(stat.element, stat.target);
                    });
                })
                .catch(() => {
                    // Fallback animation with 0 values
                    stats.forEach(stat => {
                        animateNumber(stat.element, stat.target);
                    });
                });
        }

        function animateNumber(elementId, target) {
            const element = document.getElementById(elementId);
            let current = 0;
            const increment = target / 30;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current);
            }, 50);
        }

        // Initialize animations when page loads
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(animateStats, 500);
        });
    </script>
</body>
</html>
