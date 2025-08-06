<?php
session_start();
require_once 'config.php';

// Get all events from database
try {
    $query = "SELECT * FROM acara WHERE status = 'tersedia' ORDER BY id_acara";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Gedung - PT. Aneka Usaha</title>
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
            background: rgba(255, 255, 255, 0.95);
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
            color: #8B4513;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 30px;
            align-items: center;
        }

        .nav-link {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 25px;
        }

        .nav-link:hover {
            color: #8B4513;
            background: rgba(139, 69, 19, 0.1);
            text-decoration: none;
            transform: translateY(-2px);
        }

        .nav-link.active {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
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
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 15px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .page-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Events Grid */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .event-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .event-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
        }

        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
        }

        .event-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .event-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .event-info h3 {
            font-size: 1.4rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .event-info p {
            color: #666;
            font-size: 0.9rem;
        }

        .event-details {
            margin-bottom: 25px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            padding: 8px 0;
        }

        .detail-icon {
            color: #8B4513;
            font-size: 1rem;
            width: 20px;
        }

        .detail-text {
            color: #666;
            font-size: 0.95rem;
        }

        .price-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .price-label {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .price-amount {
            font-size: 1.8rem;
            font-weight: 700;
            color: #8B4513;
        }

        .facilities {
            margin-bottom: 25px;
        }

        .facilities-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .facilities-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .facility-tag {
            background: rgba(139, 69, 19, 0.1);
            color: #8B4513;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .btn-book {
            width: 100%;
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 69, 19, 0.3);
            color: white;
            text-decoration: none;
        }

        /* Status Badge */
        .status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #28a745;
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
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

            .events-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .event-card {
                padding: 20px;
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
                    <li><a href="acara_saya.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Acara Saya</a></li>
                    <li><a href="gedung.php" class="nav-link active"><i class="fas fa-building"></i> Gedung</a></li>
                    <li><a href="panduan.php" class="nav-link"><i class="fas fa-book"></i> Panduan</a></li>
                    <li><a href="akun.php" class="nav-link"><i class="fas fa-user"></i> Akun</a></li>
                    <li><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php else: ?>
                    <li><a href="index.php" class="nav-link"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="gedung.php" class="nav-link active"><i class="fas fa-building"></i> Gedung</a></li>
                    <li><a href="panduan.php" class="nav-link"><i class="fas fa-book"></i> Panduan</a></li>
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
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-building"></i>
                    Pilih Gedung Acara
                </h1>
                <p class="page-subtitle">
                    Temukan gedung yang sempurna untuk acara Anda. Kami menyediakan berbagai pilihan gedung dengan fasilitas lengkap dan harga terjangkau.
                </p>
            </div>

            <!-- Events Grid -->
            <div class="events-grid">
                <?php foreach ($events as $event): ?>
                <div class="event-card">
                    <div class="status-badge">Tersedia</div>
                    
                    <div class="event-header">
                        <div class="event-icon">
                            <?php
                            $iconMap = [
                                'Acara A' => 'fa-heart',
                                'Acara B' => 'fa-users',
                                'Pernikahan' => 'fa-heart',
                                'Rapat' => 'fa-users',
                                'Seminar' => 'fa-graduation-cap'
                            ];
                            $icon = $iconMap[$event['nama_acara']] ?? 'fa-calendar';
                            ?>
                            <i class="fas <?= $icon ?>"></i>
                        </div>
                        <div class="event-info">
                            <h3><?= htmlspecialchars($event['nama_acara']) ?></h3>
                            <p>Kapasitas <?= $event['kapasitas'] ?> orang</p>
                        </div>
                    </div>

                    <div class="event-details">
                        <div class="detail-item">
                            <i class="fas fa-map-marker-alt detail-icon"></i>
                            <span class="detail-text"><?= htmlspecialchars($event['lokasi']) ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-users detail-icon"></i>
                            <span class="detail-text">Kapasitas: <?= $event['kapasitas'] ?> orang</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-check-circle detail-icon"></i>
                            <span class="detail-text">Status: <?= ucfirst($event['status']) ?></span>
                        </div>
                    </div>

                    <div class="price-section">
                        <div class="price-label">Harga per hari</div>
                        <div class="price-amount">Rp <?= number_format($event['harga'], 0, ',', '.') ?></div>
                    </div>

                    <?php if ($event['fasilitas']): ?>
                    <div class="facilities">
                        <div class="facilities-title">
                            <i class="fas fa-star"></i>
                            Fasilitas
                        </div>
                        <div class="facilities-list">
                            <?php
                            $facilities = explode(', ', $event['fasilitas']);
                            foreach ($facilities as $facility): ?>
                                <span class="facility-tag"><?= htmlspecialchars(trim($facility)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['id_penyewa'])): ?>
                        <a href="sewa.php?gedung=<?= $event['id_acara'] ?>" class="btn-book">
                            <i class="fas fa-calendar-plus"></i>
                            Pesan Sekarang
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn-book">
                            <i class="fas fa-sign-in-alt"></i>
                            Login untuk Memesan
                        </a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
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

        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all event cards
        document.querySelectorAll('.event-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>
