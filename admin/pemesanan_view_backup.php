<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Get booking ID from URL
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$booking_id) {
    header("Location: data_pemesanan.php");
    exit;
}

// Fetch booking details
$query = "
    SELECT 
        p.*,
        py.nama_lengkap,
        py.nama_instansi,
        py.tipe_penyewa,
        py.email,
        py.no_telepon,
        py.alamat,
        a.nama_acara as jenis_acara,
        a.harga_sewa,
        pb.status_pembayaran,
        pb.tanggal_pembayaran,
        pb.bukti_pembayaran
    FROM pemesanan p
    LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa
    LEFT JOIN acara a ON p.id_acara = a.id_acara
    LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
    WHERE p.id_pemesanan = $booking_id
";

$result = mysqli_query($conn, $query);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    header("Location: data_pemesanan.php");
    exit;
}

// Handle status updates
if ($_POST && isset($_POST['update_status'])) {
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    $update_query = "UPDATE pembayaran SET status_pembayaran = '$new_status' WHERE id_pemesanan = $booking_id";
    
    if (!mysqli_query($conn, $update_query)) {
        // If no payment record exists, create one
        $insert_query = "INSERT INTO pembayaran (id_pemesanan, status_pembayaran, tanggal_pembayaran) 
                        VALUES ($booking_id, '$new_status', NOW())";
        mysqli_query($conn, $insert_query);
    }
    
    // Refresh page
    header("Location: pemesanan_view.php?id=$booking_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemesanan #<?php echo $booking['id_pemesanan']; ?> - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --accent: #06b6d4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --gray: #64748b;
            --gray-light: #f1f5f9;
            --white: #ffffff;
            --border: #e2e8f0;
            --shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
            --radius: 12px;
            --radius-lg: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, var(--gray-light) 0%, #e0e7ff 100%);
            color: var(--dark);
            line-height: 1.6;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Modern Sidebar */
        .sidebar {
            width: 280px;
            background: var(--white);
            border-right: 1px solid var(--border);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-lg);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 24px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: var(--white);
        }

        .sidebar-header h1 {
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 0;
            overflow-y: auto;
        }

        .nav-section {
            padding: 0 16px;
            margin-bottom: 24px;
        }

        .nav-section-title {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gray);
            margin-bottom: 8px;
            padding: 0 12px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--dark);
            text-decoration: none;
            border-radius: var(--radius);
            margin-bottom: 4px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background: var(--gray-light);
            color: var(--primary);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: var(--white);
            box-shadow: var(--shadow);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 18px;
        }

        /* Mobile Menu */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: var(--white);
            border: none;
            width: 48px;
            height: 48px;
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            color: var(--dark);
            font-size: 18px;
            cursor: pointer;
        }

        .mobile-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            backdrop-filter: blur(4px);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 32px;
            min-height: 100vh;
        }

        .page-header {
            background: var(--white);
            padding: 24px 32px;
            border-radius: var(--radius-lg);
            margin-bottom: 32px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title-section h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .page-subtitle {
            color: var(--gray);
            font-size: 16px;
        }

        .back-button {
            padding: 12px 24px;
            background: var(--gray-light);
            color: var(--gray);
            text-decoration: none;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .back-button:hover {
            background: var(--primary);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Detail Grid */
        .detail-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 32px;
            margin-bottom: 32px;
        }

        .detail-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .card-header {
            padding: 24px 32px;
            background: var(--gray-light);
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-body {
            padding: 32px;
        }

        .detail-section {
            margin-bottom: 32px;
        }

        .detail-section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-grid-items {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .detail-item {
            background: var(--gray-light);
            padding: 16px 20px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }

        .detail-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gray);
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
        }

        .detail-value.large {
            font-size: 24px;
            font-weight: 800;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.lunas {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .status-badge.pending {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .status-badge.belum-lunas {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        /* Forms */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid var(--border);
            border-radius: var(--radius);
            font-size: 16px;
            font-weight: 500;
            background: var(--white);
            color: var(--dark);
            transition: all 0.2s ease;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .btn {
            padding: 16px 32px;
            border: none;
            border-radius: var(--radius);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 16px;
            width: 100%;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, #16a34a 100%);
            color: var(--white);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
            color: var(--white);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Image */
        .payment-proof {
            width: 100%;
            max-width: 400px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .payment-proof:hover {
            transform: scale(1.05);
        }

        /* Timeline */
        .timeline {
            position: relative;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--border);
        }

        .timeline-item {
            position: relative;
            padding-left: 60px;
            margin-bottom: 24px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 11px;
            top: 8px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--primary);
            border: 3px solid var(--white);
            box-shadow: 0 0 0 3px var(--border);
        }

        .timeline-content {
            background: var(--gray-light);
            padding: 16px 20px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }

        .timeline-title {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .timeline-date {
            font-size: 14px;
            color: var(--gray);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .detail-grid {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .main-content {
                padding: 24px;
            }
        }

        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .mobile-overlay.active {
                display: block;
            }

            .main-content {
                margin-left: 0;
                padding: 16px;
                padding-top: 80px;
            }

            .page-header {
                padding: 20px;
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }

            .detail-grid-items {
                grid-template-columns: 1fr;
            }

            .card-body {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Mobile Overlay -->
        <div class="mobile-overlay" onclick="closeMobileMenu()"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h1><i class="fas fa-building"></i> Admin Panel</h1>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <a href="dashboard.php" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <a href="data_penyewa.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Data Penyewa</span>
                    </a>
                    <a href="data_pemesanan.php" class="nav-link active">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Data Pemesanan</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Reports</div>
                    <a href="riwayat_pemesanan.php" class="nav-link">
                        <i class="fas fa-history"></i>
                        <span>Riwayat Pemesanan</span>
                    </a>
                    <a href="laporan_penyewaan.php" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Laporan Penyewaan</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Settings</div>
                    <a href="akun_admin.php" class="nav-link">
                        <i class="fas fa-user-cog"></i>
                        <span>Akun Admin</span>
                    </a>
                    <a href="logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <div class="page-title-section">
                    <h1>Detail Pemesanan #<?php echo $booking['id_pemesanan']; ?></h1>
                    <p class="page-subtitle">Informasi lengkap pemesanan gedung</p>
                </div>
                <a href="data_pemesanan.php" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            </div>

            <!-- Detail Grid -->
            <div class="detail-grid">
                <!-- Main Details -->
                <div class="detail-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            Informasi Pemesanan
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Booking Details -->
                        <div class="detail-section">
                            <h4 class="section-title">
                                <i class="fas fa-calendar"></i>
                                Detail Acara
                            </h4>
                            <div class="detail-grid-items">
                                <div class="detail-item">
                                    <div class="detail-label">Nama Acara</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($booking['nama_acara']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Jenis Acara</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($booking['jenis_acara']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Tanggal Acara</div>
                                    <div class="detail-value"><?php echo date('d M Y', strtotime($booking['tanggal_sewa'])); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Waktu</div>
                                    <div class="detail-value"><?php echo date('H:i', strtotime($booking['waktu_mulai'])) . ' - ' . date('H:i', strtotime($booking['waktu_selesai'])); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Durasi</div>
                                    <div class="detail-value"><?php echo $booking['durasi_sewa']; ?> jam</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Jumlah Tamu</div>
                                    <div class="detail-value"><?php echo $booking['jumlah_tamu']; ?> orang</div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Details -->
                        <div class="detail-section">
                            <h4 class="section-title">
                                <i class="fas fa-user"></i>
                                Data Penyewa
                            </h4>
                            <div class="detail-grid-items">
                                <div class="detail-item">
                                    <div class="detail-label">
                                        <?php echo $booking['tipe_penyewa'] === 'instansi' ? 'Nama Instansi' : 'Nama Lengkap'; ?>
                                    </div>
                                    <div class="detail-value">
                                        <?php echo htmlspecialchars($booking['tipe_penyewa'] === 'instansi' ? $booking['nama_instansi'] : $booking['nama_lengkap']); ?>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Tipe Penyewa</div>
                                    <div class="detail-value"><?php echo ucfirst($booking['tipe_penyewa']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Email</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($booking['email']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">No. Telepon</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($booking['no_telepon']); ?></div>
                                </div>
                            </div>
                            
                            <?php if ($booking['alamat']): ?>
                            <div class="detail-item" style="margin-top: 16px;">
                                <div class="detail-label">Alamat</div>
                                <div class="detail-value"><?php echo htmlspecialchars($booking['alamat']); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Financial Details -->
                        <div class="detail-section">
                            <h4 class="section-title">
                                <i class="fas fa-money-bill-wave"></i>
                                Rincian Biaya
                            </h4>
                            <div class="detail-grid-items">
                                <div class="detail-item">
                                    <div class="detail-label">Harga Sewa per Jam</div>
                                    <div class="detail-value">Rp <?php echo number_format($booking['harga_sewa'], 0, ',', '.'); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Durasi</div>
                                    <div class="detail-value"><?php echo $booking['durasi_sewa']; ?> jam</div>
                                </div>
                                <div class="detail-item" style="grid-column: 1 / -1;">
                                    <div class="detail-label">Total Biaya</div>
                                    <div class="detail-value large">Rp <?php echo number_format($booking['total'], 0, ',', '.'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status & Actions -->
                <div>
                    <!-- Status Card -->
                    <div class="detail-card" style="margin-bottom: 24px;">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-credit-card"></i>
                                Status Pembayaran
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="detail-item" style="text-align: center; margin-bottom: 24px;">
                                <div class="detail-label">Status Saat Ini</div>
                                <div class="detail-value">
                                    <?php
                                    $status = $booking['status_pembayaran'] ?: 'Belum Lunas';
                                    $status_class = strtolower(str_replace(' ', '-', $status));
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <i class="fas fa-<?php echo $status === 'Lunas' ? 'check-circle' : ($status === 'Pending' ? 'clock' : 'times-circle'); ?>"></i>
                                        <?php echo $status; ?>
                                    </span>
                                </div>
                            </div>

                            <?php if ($booking['tanggal_pembayaran']): ?>
                            <div class="detail-item" style="margin-bottom: 24px;">
                                <div class="detail-label">Tanggal Pembayaran</div>
                                <div class="detail-value"><?php echo date('d M Y H:i', strtotime($booking['tanggal_pembayaran'])); ?></div>
                            </div>
                            <?php endif; ?>

                            <!-- Update Status Form -->
                            <form method="POST">
                                <div class="form-group">
                                    <label class="form-label">Update Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="">Pilih Status</option>
                                        <option value="Pending" <?php echo $status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Lunas" <?php echo $status === 'Lunas' ? 'selected' : ''; ?>>Lunas</option>
                                        <option value="Belum Lunas" <?php echo $status === 'Belum Lunas' ? 'selected' : ''; ?>>Belum Lunas</option>
                                    </select>
                                </div>
                                <button type="submit" name="update_status" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Update Status
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Payment Proof -->
                    <?php if ($booking['bukti_pembayaran']): ?>
                    <div class="detail-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-receipt"></i>
                                Bukti Pembayaran
                            </h3>
                        </div>
                        <div class="card-body" style="text-align: center;">
                            <img src="../uploads/<?php echo $booking['bukti_pembayaran']; ?>" 
                                 alt="Bukti Pembayaran" class="payment-proof"
                                 onclick="window.open(this.src, '_blank')">
                            <p style="margin-top: 16px; color: var(--gray); font-size: 14px;">
                                Klik gambar untuk memperbesar
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Timeline -->
                    <div class="detail-card" style="margin-top: 24px;">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history"></i>
                                Timeline
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-content">
                                        <div class="timeline-title">Pemesanan Dibuat</div>
                                        <div class="timeline-date"><?php echo date('d M Y H:i', strtotime($booking['tanggal_pesan'])); ?></div>
                                    </div>
                                </div>
                                
                                <?php if ($booking['tanggal_pembayaran']): ?>
                                <div class="timeline-item">
                                    <div class="timeline-content">
                                        <div class="timeline-title">Pembayaran Diproses</div>
                                        <div class="timeline-date"><?php echo date('d M Y H:i', strtotime($booking['tanggal_pembayaran'])); ?></div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="timeline-item">
                                    <div class="timeline-content">
                                        <div class="timeline-title">Jadwal Acara</div>
                                        <div class="timeline-date"><?php echo date('d M Y', strtotime($booking['tanggal_sewa'])); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.mobile-overlay');
            
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        function closeMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.mobile-overlay');
            
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }
    </script>
</body>
</html>
