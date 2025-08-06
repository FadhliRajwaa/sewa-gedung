<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['id_penyewa'])) {
    header('Location: login.php');
    exit;
}

$id_penyewa = $_SESSION['id_penyewa'];

// Get booking ID from URL
if (!isset($_GET['id'])) {
    header('Location: acara_saya.php');
    exit;
}

$id_pemesanan = $_GET['id'];

// Get detailed booking information
try {
    $query = "
        SELECT p.*, a.nama_acara, a.lokasi, a.fasilitas, a.kapasitas, a.harga, pb.bukti_pembayaran, pb.status_pembayaran, pb.tanggal_upload,
               pe.nama_lengkap AS nama_penyewa, pe.email, pe.no_telepon AS telepon, pe.alamat, pe.tipe_penyewa
        FROM pemesanan p
        JOIN acara a ON p.id_acara = a.id_acara
        JOIN penyewa pe ON p.id_penyewa = pe.id_penyewa
        LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
        WHERE p.id_pemesanan = ? AND p.id_penyewa = ?
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id_pemesanan, $id_penyewa]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        header('Location: acara_saya.php');
        exit;
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Acara - PT. Aneka Usaha</title>
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
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 25px;
        }

        .nav-link:hover {
            color: #8B4513;
            background: rgba(139, 69, 19, 0.1);
            text-decoration: none;
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

        .detail-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #8B4513;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            color: #A0522D;
            text-decoration: none;
            transform: translateX(-5px);
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #666;
            font-size: 1.1rem;
        }

        /* Detail Sections */
        .detail-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .detail-label {
            font-weight: 500;
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-weight: 600;
            color: #333;
            font-size: 1rem;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
            width: fit-content;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        /* Financial Summary */
        .financial-summary {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .financial-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .financial-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .financial-item {
            text-align: center;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .financial-amount {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .financial-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Description */
        .description-text {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-top: 15px;
            border: 1px solid #e9ecef;
            line-height: 1.8;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 69, 19, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-info {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
            color: white;
        }

        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(23, 162, 184, 0.3);
            color: white;
            text-decoration: none;
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -22px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #8B4513;
        }

        .timeline-content {
            background: white;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }

        .timeline-date {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 5px;
        }

        .timeline-text {
            font-weight: 500;
            color: #333;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .detail-container {
                padding: 25px 20px;
                margin: 15px;
            }

            .page-title {
                font-size: 1.8rem;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .financial-details {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .main-content {
                margin-top: 80px;
                padding: 20px 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <img src="logo.png" alt="PT. Aneka Usaha" onerror="this.style.display='none'">
                <div class="logo-text">PT. ANEKA USAHA</div>
            </div>
            
            <ul class="nav-menu">
                <li><a href="dashboard_user.php" class="nav-link"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="acara_saya.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Acara Saya</a></li>
                <li><a href="akun.php" class="nav-link"><i class="fas fa-user"></i> Akun</a></li>
                <li><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="detail-container">
                <a href="acara_saya.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Acara Saya
                </a>

                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-calendar-check"></i>
                        Detail Acara
                    </h1>
                    <p class="page-subtitle">
                        Informasi lengkap mengenai acara yang telah Anda pesan
                    </p>
                </div>

                <!-- Financial Summary -->
                <div class="financial-summary">
                    <h3 class="financial-title">
                        <i class="fas fa-chart-line"></i>
                        Ringkasan Keuangan
                    </h3>
                    <div class="financial-details">
                        <div class="financial-item">
                            <div class="financial-amount">Rp <?= number_format($booking['total'], 0, ',', '.') ?></div>
                            <div class="financial-label">Total Biaya</div>
                        </div>
                        <div class="financial-item">
                            <div class="financial-amount"><?= $booking['durasi'] ?> Hari</div>
                            <div class="financial-label">Durasi Sewa</div>
                        </div>
                        <div class="financial-item">
                            <div class="financial-amount">
                                <?php if ($booking['status_pembayaran']): ?>
                                    <span class="status-badge <?= $booking['status_pembayaran'] === 'Lunas' ? 'status-approved' : 'status-pending' ?>">
                                        <?= $booking['status_pembayaran'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge status-pending">Belum Bayar</span>
                                <?php endif; ?>
                            </div>
                            <div class="financial-label">Status Pembayaran</div>
                        </div>
                    </div>
                </div>

                <!-- Event Information -->
                <div class="detail-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Informasi Acara
                    </h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Nama Acara</span>
                            <span class="detail-value"><?= htmlspecialchars($booking['nama_acara']) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Lokasi Gedung</span>
                            <span class="detail-value"><?= htmlspecialchars($booking['lokasi']) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tanggal Mulai</span>
                            <span class="detail-value"><?= date('d M Y', strtotime($booking['tanggal_sewa'])) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tanggal Selesai</span>
                            <span class="detail-value"><?= date('d M Y', strtotime($booking['tanggal_selesai'])) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tipe Pesanan</span>
                            <span class="detail-value"><?= ucfirst($booking['tipe_pesanan']) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tanggal Pemesanan</span>
                            <span class="detail-value"><?= date('d M Y H:i', strtotime($booking['tanggal_pesan'])) ?></span>
                        </div>
                    </div>

                    <?php if (isset($booking['fasilitas']) && $booking['fasilitas']): ?>
                    <div class="description-text">
                        <strong>Fasilitas Acara:</strong><br>
                        <?= nl2br(htmlspecialchars($booking['fasilitas'])) ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($booking['kebutuhan_tambahan']) && $booking['kebutuhan_tambahan']): ?>
                    <div class="description-text">
                        <strong>Kebutuhan Tambahan:</strong><br>
                        <?= nl2br(htmlspecialchars($booking['kebutuhan_tambahan'])) ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Customer Information -->
                <div class="detail-section">
                    <h3 class="section-title">
                        <i class="fas fa-user"></i>
                        Informasi Penyewa
                    </h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Nama Lengkap</span>
                            <span class="detail-value"><?= htmlspecialchars($booking['nama_penyewa']) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email</span>
                            <span class="detail-value"><?= htmlspecialchars($booking['email']) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Telepon</span>
                            <span class="detail-value"><?= htmlspecialchars($booking['telepon']) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tipe Penyewa</span>
                            <span class="detail-value"><?= ucfirst($booking['tipe_penyewa']) ?></span>
                        </div>
                    </div>
                    
                    <?php if ($booking['alamat']): ?>
                    <div class="description-text">
                        <strong>Alamat:</strong><br>
                        <?= nl2br(htmlspecialchars($booking['alamat'])) ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Payment History -->
                <?php if ($booking['bukti_pembayaran'] || $booking['tanggal_upload']): ?>
                <div class="detail-section">
                    <h3 class="section-title">
                        <i class="fas fa-credit-card"></i>
                        Riwayat Pembayaran
                    </h3>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <div class="timeline-date"><?= date('d M Y H:i', strtotime($booking['tanggal_pesan'])) ?></div>
                                <div class="timeline-text">Pemesanan dibuat</div>
                            </div>
                        </div>
                        <?php if ($booking['tanggal_upload']): ?>
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <div class="timeline-date"><?= date('d M Y H:i', strtotime($booking['tanggal_upload'])) ?></div>
                                <div class="timeline-text">Bukti pembayaran diupload</div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($booking['status_pembayaran'] === 'Lunas'): ?>
                        <div class="timeline-item">
                            <div class="timeline-content">
                                <div class="timeline-date">Status Terkini</div>
                                <div class="timeline-text">Pembayaran dikonfirmasi lunas</div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <?php if (!$booking['status_pembayaran'] || $booking['status_pembayaran'] === 'Belum Lunas'): ?>
                        <a href="pembayaran.php?id=<?= $booking['id_pemesanan'] ?>" class="btn btn-primary">
                            <i class="fas fa-credit-card"></i>
                            <?= !$booking['status_pembayaran'] ? 'Lakukan Pembayaran' : 'Kelola Pembayaran' ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($booking['bukti_pembayaran']): ?>
                        <a href="uploads/<?= htmlspecialchars($booking['bukti_pembayaran']) ?>" target="_blank" class="btn btn-info">
                            <i class="fas fa-file-image"></i>
                            Lihat Bukti Pembayaran
                        </a>
                    <?php endif; ?>

                    <a href="acara_saya.php" class="btn btn-success">
                        <i class="fas fa-list"></i>
                        Kembali ke Daftar Acara
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
