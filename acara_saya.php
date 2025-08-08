<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['id_penyewa'])) {
    header('Location: login.php');
    exit;
}

$id_penyewa = $_SESSION['id_penyewa'];
$nama = $_SESSION['nama_lengkap'];

try {
    $query = "
        SELECT p.id_pemesanan, a.nama_acara, a.lokasi, a.kapasitas, a.harga, 
               p.tanggal_sewa, p.tanggal_selesai, p.durasi, p.total, p.tipe_pesanan,
               pb.status_pembayaran, pb.bukti_pembayaran, pb.tanggal_upload,
               CASE 
                   WHEN p.tanggal_sewa > CURDATE() THEN 'upcoming'
                   WHEN p.tanggal_sewa <= CURDATE() AND p.tanggal_selesai >= CURDATE() THEN 'ongoing'
                   ELSE 'completed'
               END as status_acara
        FROM pemesanan p
        JOIN acara a ON p.id_acara = a.id_acara
        LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
        WHERE p.id_penyewa = ?
        ORDER BY p.tanggal_sewa DESC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id_penyewa]);
    $acara_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal mengambil data acara: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acara Saya - PT. Aneka Usaha</title>
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

        /* Modern Navbar - Same as dashboard */
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
            margin-bottom: 40px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 10px;
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .page-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
        }

        /* Event Cards */
        .events-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .event-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .event-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .event-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .event-status {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-upcoming {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
        }

        .status-ongoing {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .status-completed {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
        }

        .payment-pending {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: #333;
        }

        .payment-approved {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .payment-rejected {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .payment-none {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
        }

        .event-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-icon {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.9rem;
        }

        .detail-content h4 {
            font-size: 0.9rem;
            color: #666;
            margin: 0;
            font-weight: 500;
        }

        .detail-content p {
            font-size: 1rem;
            color: #333;
            margin: 0;
            font-weight: 600;
        }

        .event-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }

        .empty-state {
            text-align: center;
            padding: 60px 30px;
            color: #666;
        }

        .empty-icon {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }

        .empty-description {
            font-size: 1rem;
            margin-bottom: 30px;
        }

        .footer {
            margin-top: 60px;
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

            .page-title {
                font-size: 2rem;
            }

            .event-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .event-details {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .event-actions {
                flex-direction: column;
                gap: 10px;
            }

            .btn {
                justify-content: center;
                width: 100%;
            }

            .main-content {
                margin-top: 80px;
                padding: 20px 15px;
            }

            .events-container {
                padding: 20px;
            }

            .event-card {
                padding: 20px;
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
                <li><a href="dashboard_user.php" class="nav-link"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="gedung.php" class="nav-link"><i class="fas fa-building"></i> Pilih Acara</a></li>
                <li><a href="acara_saya.php" class="nav-link active"><i class="fas fa-calendar-alt"></i> Acara Saya</a></li>
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
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-calendar-check"></i>
                    Acara Saya
                </h1>
                <p class="page-subtitle">
                    Kelola dan pantau status semua acara yang telah Anda pesan
                </p>
            </div>

            <!-- Events Container -->
            <div class="events-container">
                <?php if (count($acara_list) > 0): ?>
                    <?php foreach ($acara_list as $acara): ?>
                        <div class="event-card">
                            <!-- Event Header -->
                            <div class="event-header">
                                <h3 class="event-title"><?= htmlspecialchars($acara['nama_acara']) ?></h3>
                                <div class="event-status">
                                    <!-- Event Status -->
                                    <?php
                                    $statusClass = 'status-' . $acara['status_acara'];
                                    $statusText = '';
                                    switch($acara['status_acara']) {
                                        case 'upcoming':
                                            $statusText = 'Akan Datang';
                                            break;
                                        case 'ongoing':
                                            $statusText = 'Sedang Berlangsung';
                                            break;
                                        case 'completed':
                                            $statusText = 'Selesai';
                                            break;
                                    }
                                    ?>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <i class="fas fa-calendar"></i> <?= $statusText ?>
                                    </span>

                                    <!-- Payment Status -->
                                    <?php
                                    $paymentStatus = $acara['status_pembayaran'];
                                    $paymentClass = 'payment-none';
                                    $paymentText = 'Belum Bayar';
                                    $paymentIcon = 'fas fa-times-circle';

                                    if ($paymentStatus) {
                                        switch(strtolower($paymentStatus)) {
                                            case 'belum lunas':
                                                $paymentClass = 'payment-pending';
                                                $paymentText = 'Menunggu Verifikasi';
                                                $paymentIcon = 'fas fa-clock';
                                                break;
                                            case 'lunas':
                                                $paymentClass = 'payment-approved';
                                                $paymentText = 'Pembayaran Lunas';
                                                $paymentIcon = 'fas fa-check-circle';
                                                break;
                                        }
                                    }
                                    ?>
                                    <span class="status-badge <?= $paymentClass ?>">
                                        <i class="<?= $paymentIcon ?>"></i> <?= $paymentText ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Event Details -->
                            <div class="event-details">
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="detail-content">
                                        <h4>Lokasi</h4>
                                        <p><?= htmlspecialchars($acara['lokasi']) ?></p>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="detail-content">
                                        <h4>Kapasitas</h4>
                                        <p><?= htmlspecialchars($acara['kapasitas']) ?> orang</p>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                    <div class="detail-content">
                                        <h4>Tanggal Acara</h4>
                                        <p><?= date('d M Y', strtotime($acara['tanggal_sewa'])) ?> - <?= date('d M Y', strtotime($acara['tanggal_selesai'])) ?></p>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="detail-content">
                                        <h4>Durasi</h4>
                                        <p><?= $acara['durasi'] ?> hari</p>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="detail-content">
                                        <h4>Total Biaya</h4>
                                        <p>Rp <?= number_format($acara['total'], 0, ',', '.') ?></p>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <div class="detail-content">
                                        <h4>Tipe Pesanan</h4>
                                        <p><?= ucfirst($acara['tipe_pesanan']) ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Event Actions -->
                            <div class="event-actions">
                                <?php if (!$paymentStatus || $paymentStatus === 'Belum Lunas'): ?>
                                    <a href="pembayaran.php?id=<?= $acara['id_pemesanan'] ?>" class="btn btn-primary">
                                        <i class="fas fa-credit-card"></i>
                                        <?= !$paymentStatus ? 'Lakukan Pembayaran' : 'Upload Bukti Pembayaran' ?>
                                    </a>
                                <?php endif; ?>

                                <?php if ($acara['bukti_pembayaran']): ?>
                                    <a href="uploads/<?= htmlspecialchars($acara['bukti_pembayaran']) ?>" target="_blank" class="btn btn-info">
                                        <i class="fas fa-eye"></i>
                                        Lihat Bukti Pembayaran
                                    </a>
                                <?php endif; ?>

                                <a href="cetak_nota.php?id=<?= $acara['id_pemesanan'] ?>" target="_blank" class="btn btn-warning">
                                    <i class="fas fa-print"></i>
                                    Cetak Nota
                                </a>

                                <a href="detail_acara.php?id=<?= $acara['id_pemesanan'] ?>" class="btn btn-success">
                                    <i class="fas fa-info-circle"></i>
                                    Detail Lengkap
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <h3 class="empty-title">Belum Ada Acara</h3>
                        <p class="empty-description">
                            Anda belum memiliki acara yang terdaftar. Mulai dengan menyewa gedung untuk acara Anda.
                        </p>
                        <a href="gedung.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Sewa Gedung Sekarang
                        </a>
                    </div>
                <?php endif; ?>
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

        // Auto-refresh payment status every 30 seconds
        setInterval(() => {
            // You can add AJAX call here to refresh payment status
            // without reloading the entire page
        }, 30000);
    </script>
</body>
</html>
