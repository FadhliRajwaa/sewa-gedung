<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($booking_id <= 0) {
    header("Location: data_pemesanan.php");
    exit;
}

// Fetch booking details dengan query yang benar sesuai database SQL
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
        a.harga,
        a.kapasitas,
        a.lokasi,
        a.fasilitas,
        pb.status_pembayaran,
        pb.tanggal_upload,
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

// Get customer name
$customer_name = ($booking['tipe_penyewa'] == 'instansi') ? $booking['nama_instansi'] : $booking['nama_lengkap'];
$status_pembayaran = $booking['status_pembayaran'] ?: 'Belum Lunas';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemesanan #<?php echo $booking_id; ?> - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1f2937;
            --gray: #6b7280;
            --gray-light: #f9fafb;
            --white: #ffffff;
            --border: #e5e7eb;
            --shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
            --radius: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--gray-light);
            color: var(--dark);
            line-height: 1.6;
        }

        .navbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(229, 231, 235, 0.8);
            padding: 0.75rem 2rem;
        }

        .navbar.scrolled .navbar-brand {
            font-size: 1.3rem;
            transition: font-size 0.3s ease;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }

        .navbar-nav {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        /* Hamburger Menu Styles */
        .navbar-toggle {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 24px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            z-index: 1001;
            transition: transform 0.3s ease;
        }

        .navbar-toggle:hover {
            transform: scale(1.1);
        }

        .hamburger-line {
            width: 100%;
            height: 3px;
            background: var(--primary);
            border-radius: 2px;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            transform-origin: center;
        }

        /* Hamburger Animation */
        .navbar-toggle.active .hamburger-line:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
        }

        .navbar-toggle.active .hamburger-line:nth-child(2) {
            opacity: 0;
            transform: scale(0);
        }

        .navbar-toggle.active .hamburger-line:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        /* Mobile Menu Backdrop */
        .navbar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .navbar-backdrop.active {
            display: block;
            opacity: 1;
        }

        .nav-link {
            color: var(--gray);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            transition: all 0.2s;
        }

        .nav-link:hover {
            background: var(--gray-light);
            color: var(--dark);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .page-header {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .status-lunas { background: #d1fae5; color: #065f46; }
        .status-belum-lunas { background: #fef3c7; color: #92400e; }
        .status-pending { background: #dbeafe; color: #1e40af; }

        .grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .card-header {
            background: var(--primary);
            color: var(--white);
            padding: 1.5rem;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-body {
            padding: 2rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .info-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
            max-width: 100%;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            background: var(--gray);
            color: var(--white);
        }

        .btn-secondary:hover {
            background: var(--dark);
        }

        .btn-success {
            background: var(--success);
            color: var(--white);
        }

        .btn-warning {
            background: var(--warning);
            color: var(--white);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border);
            border-radius: var(--radius);
            font-size: 1rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .alert {
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .payment-proof {
            max-width: 100%;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .no-proof {
            text-align: center;
            padding: 2rem;
            color: var(--gray);
            font-style: italic;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
                flex-direction: row;
                justify-content: space-between;
                position: sticky;
                top: 0;
                z-index: 1000;
            }
            
            .navbar.scrolled {
                padding: 0.75rem 1rem;
                background: rgba(255, 255, 255, 0.98);
            }
            
            .navbar.scrolled .navbar-brand {
                font-size: 1.2rem;
            }
            
            .navbar-toggle {
                display: flex;
            }
            
            .navbar-nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--white);
                flex-direction: column;
                width: 100%;
                gap: 0;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                border-top: 1px solid var(--border);
                padding: 0.5rem 0;
                opacity: 0;
                transform: translateY(-20px);
                transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                z-index: 1000;
                border-radius: 0 0 12px 12px;
            }
            
            .navbar-nav.active {
                display: flex;
                opacity: 1;
                transform: translateY(0);
            }
            
            .nav-link {
                text-align: center;
                padding: 1.2rem 1.5rem;
                border-bottom: 1px solid var(--gray-light);
                margin: 0;
                transition: all 0.3s ease;
                font-weight: 500;
                position: relative;
                overflow: hidden;
            }
            
            .nav-link:last-child {
                border-bottom: none;
                border-radius: 0 0 12px 12px;
            }
            
            .nav-link:hover {
                background: var(--primary);
                color: var(--white);
                transform: translateX(8px);
            }
            
            .nav-link::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                width: 4px;
                height: 100%;
                background: var(--primary);
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .nav-link:hover::before {
                transform: translateX(0);
            }
            
            .container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .page-header {
                padding: 1.5rem;
                flex-direction: column;
                align-items: flex-start;
                text-align: left;
                gap: 1rem;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .card-body {
                padding: 1.5rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
                padding: 1rem;
                font-size: 1rem;
            }
            
            .payment-proof {
                max-width: 100%;
                height: auto;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 0 0.25rem;
            }
            
            .page-header {
                padding: 1rem;
            }
            
            .card {
                margin-bottom: 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .info-item {
                padding: 0.5rem 0;
                border-bottom: 1px solid var(--gray-light);
            }
            
            .info-item:last-child {
                border-bottom: none;
            }
            
            .info-label {
                font-size: 0.8rem;
                margin-bottom: 0.25rem;
            }
            
            .info-value {
                font-size: 0.95rem;
                word-wrap: break-word;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Backdrop -->
    <div class="navbar-backdrop" id="navbarBackdrop"></div>
    
    <!-- Navbar -->
    <nav class="navbar">
        <a href="dashboard.php" class="navbar-brand">
            <i class="fas fa-cog"></i> Admin Panel
        </a>
        
        <!-- Hamburger Menu Button -->
        <button class="navbar-toggle" id="navbarToggle">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
        
        <div class="navbar-nav" id="navbarNav">
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="data_pemesanan.php" class="nav-link">
                <i class="fas fa-calendar-check"></i> Data Pemesanan
            </a>
            <a href="logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-file-invoice"></i>
                    Detail Pemesanan #<?php echo $booking_id; ?>
                </h1>
                <p style="color: var(--gray); margin-top: 0.5rem;">
                    Tanggal Pesan: <?php echo date('d M Y H:i', strtotime($booking['tanggal_pesan'])); ?>
                </p>
            </div>
            <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $status_pembayaran)); ?>">
                <?php echo $status_pembayaran; ?>
            </span>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="grid">
            <!-- Main Content -->
            <div>
                <!-- Customer Information -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user"></i> Informasi Penyewa
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Nama <?php echo ($booking['tipe_penyewa'] == 'instansi') ? 'Instansi' : 'Penyewa'; ?></div>
                                <div class="info-value"><?php echo htmlspecialchars($customer_name); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Tipe Penyewa</div>
                                <div class="info-value">
                                    <span class="status-badge <?php echo $booking['tipe_penyewa'] == 'instansi' ? 'status-pending' : 'status-lunas'; ?>">
                                        <?php echo ($booking['tipe_penyewa'] == 'instansi') ? 'Instansi' : 'Umum'; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value" style="word-wrap: break-word; word-break: break-all; max-width: 250px;"><?php echo htmlspecialchars($booking['email']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">No. Telepon</div>
                                <div class="info-value"><?php echo htmlspecialchars($booking['no_telepon']); ?></div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Alamat</div>
                            <div class="info-value"><?php echo htmlspecialchars($booking['alamat']); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-calendar-check"></i> Detail Pemesanan
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Jenis Acara</div>
                                <div class="info-value"><?php echo htmlspecialchars($booking['jenis_acara']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Lokasi</div>
                                <div class="info-value"><?php echo htmlspecialchars($booking['lokasi']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Tanggal Sewa</div>
                                <div class="info-value"><?php echo date('d M Y', strtotime($booking['tanggal_sewa'])); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Tanggal Selesai</div>
                                <div class="info-value"><?php echo date('d M Y', strtotime($booking['tanggal_selesai'])); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Durasi</div>
                                <div class="info-value"><?php echo $booking['durasi']; ?> hari</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Harga Acara</div>
                                <div class="info-value">Rp <?php echo number_format($booking['harga'], 0, ',', '.'); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Total Biaya</div>
                                <div class="info-value" style="color: var(--primary); font-size: 1.2em;">
                                    Rp <?php echo number_format($booking['total'], 0, ',', '.'); ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Metode Pembayaran</div>
                                <div class="info-value"><?php echo str_replace('_', ' ', $booking['metode_pembayaran']); ?></div>
                            </div>
                        </div>
                        
                        <?php if ($booking['kebutuhan_tambahan']): ?>
                        <div class="info-item">
                            <div class="info-label">Kebutuhan Tambahan</div>
                            <div class="info-value"><?php echo htmlspecialchars($booking['kebutuhan_tambahan']); ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($booking['fasilitas']): ?>
                        <div class="info-item">
                            <div class="info-label">Fasilitas</div>
                            <div class="info-value"><?php echo htmlspecialchars($booking['fasilitas']); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Payment Proof -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-file-image"></i> Bukti Pembayaran
                    </div>
                    <div class="card-body">
                        <?php if ($booking['bukti_pembayaran']): ?>
                            <img src="../uploads/<?php echo $booking['bukti_pembayaran']; ?>" 
                                 alt="Bukti Pembayaran" class="payment-proof">
                            <p style="margin-top: 1rem; color: var(--gray); font-size: 0.9rem;">
                                Diupload: <?php echo $booking['tanggal_upload'] ? date('d M Y H:i', strtotime($booking['tanggal_upload'])) : 'N/A'; ?>
                            </p>
                        <?php else: ?>
                            <div class="no-proof">
                                <i class="fas fa-file-image" style="font-size: 3rem; color: var(--border); margin-bottom: 1rem;"></i>
                                <p>Belum ada bukti pembayaran</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="data_pemesanan.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Data Pemesanan
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Detail
            </button>
            <a href="mailto:<?php echo $booking['email']; ?>" class="btn btn-success">
                <i class="fas fa-envelope"></i> Kirim Email
            </a>
            <a href="pemesanan_edit.php?id=<?php echo $booking_id; ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Pemesanan
            </a>
        </div>
    </div>

    <script>
        // Hamburger Menu Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const navbarToggle = document.getElementById('navbarToggle');
            const navbarNav = document.getElementById('navbarNav');
            const navbarBackdrop = document.getElementById('navbarBackdrop');
            const navbar = document.querySelector('.navbar');
            
            // Sticky navbar scroll effect
            let lastScrollY = window.scrollY;
            
            function updateNavbar() {
                const currentScrollY = window.scrollY;
                
                if (currentScrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
                
                lastScrollY = currentScrollY;
            }
            
            // Listen for scroll events
            window.addEventListener('scroll', updateNavbar, { passive: true });
            
            // Initial check
            updateNavbar();
            
            function toggleMenu() {
                // Toggle active class on hamburger button
                navbarToggle.classList.toggle('active');
                
                // Toggle active class on navbar nav
                navbarNav.classList.toggle('active');
                
                // Toggle backdrop
                navbarBackdrop.classList.toggle('active');
                
                // Prevent body scroll when menu is open
                document.body.style.overflow = navbarNav.classList.contains('active') ? 'hidden' : '';
            }
            
            function closeMenu() {
                navbarToggle.classList.remove('active');
                navbarNav.classList.remove('active');
                navbarBackdrop.classList.remove('active');
                document.body.style.overflow = '';
            }
            
            navbarToggle.addEventListener('click', toggleMenu);
            
            // Close menu when clicking on backdrop
            navbarBackdrop.addEventListener('click', closeMenu);
            
            // Close menu when clicking on nav links
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(function(link) {
                link.addEventListener('click', closeMenu);
            });
            
            // Close menu on escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeMenu();
                }
            });
            
            // Close menu when window is resized to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    closeMenu();
                }
            });
        });
        
        // Auto refresh status every 30 seconds
        setInterval(function() {
            location.reload();
        }, 30000);

        // Print styles
        window.addEventListener('beforeprint', function() {
            document.querySelector('.navbar').style.display = 'none';
            document.querySelector('.action-buttons').style.display = 'none';
        });

        window.addEventListener('afterprint', function() {
            document.querySelector('.navbar').style.display = 'flex';
            document.querySelector('.action-buttons').style.display = 'flex';
        });
    </script>
</body>
</html>
