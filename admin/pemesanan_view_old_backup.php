<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Function to format currency
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Function to format date
function formatTanggal($tanggal) {
    return date('d F Y', strtotime($tanggal));
}

// Function to get payment status badge class
function getPaymentStatusBadgeClass($status) {
    switch($status) {
        case 'Lunas': return 'badge-success';
        case 'Belum Lunas': return 'badge-warning';
        default: return 'badge-secondary';
    }
}

$id = $_GET['id'] ?? 0;

// Handle status update
if ($_POST && isset($_POST['status_pembayaran'])) {
    try {
        $stmt = $pdo->prepare("UPDATE pembayaran SET status_pembayaran = ? WHERE id_pemesanan = ?");
        $stmt->execute([$_POST['status_pembayaran'], $id]);
        
        $success_message = "Status pembayaran berhasil diupdate!";
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Get booking details
try {
    $stmt = $pdo->prepare("
        SELECT 
            p.*,
            a.nama_acara,
            a.harga,
            a.lokasi,
            a.fasilitas,
            pen.tipe_penyewa,
            pen.nama_instansi,
            pen.nama_lengkap,
            pen.nik,
            pen.no_telepon,
            pen.email,
            pen.alamat,
            pay.status_pembayaran,
            pay.bukti_pembayaran,
            pay.tanggal_upload as tanggal_pembayaran
        FROM pemesanan p
        LEFT JOIN acara a ON p.id_acara = a.id_acara
        LEFT JOIN penyewa pen ON p.id_penyewa = pen.id_penyewa
        LEFT JOIN pembayaran pay ON p.id_pemesanan = pay.id_pemesanan
        WHERE p.id_pemesanan = ?
    ");
    $stmt->execute([$id]);
    $pemesanan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pemesanan) {
        throw new Exception("Data pemesanan tidak ditemukan");
    }
} catch (Exception $e) {
    $error_message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemesanan - Admin Sewa Gedung</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-header {
            text-align: center;
            padding: 0 2rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 2rem;
        }

        .sidebar-header h2 {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .nav-menu {
            list-style: none;
            padding: 0 1rem;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-item a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .nav-item a.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .nav-item a i {
            margin-right: 1rem;
            width: 20px;
            text-align: center;
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 0.75rem;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .mobile-overlay.active {
            display: block;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            min-height: 100vh;
        }

        .content-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .content-header h1 {
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .content-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
        }

        /* Alert Styles */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border-color: #22c55e;
            color: #16a34a;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border-color: #ef4444;
            color: #dc2626;
        }

        /* Detail Card */
        .detail-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .detail-title {
            color: white;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .detail-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.25rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .detail-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .detail-value {
            color: white;
            font-size: 1.1rem;
            font-weight: 500;
        }

        /* Badge Styles */
        .badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid #22c55e;
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid #f59e0b;
        }

        .badge-secondary {
            background: rgba(156, 163, 175, 0.2);
            color: #9ca3af;
            border: 1px solid #9ca3af;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            color: white;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.6);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
        }

        .form-control option {
            background: #1f2937;
            color: white;
        }

        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background: rgba(156, 163, 175, 0.2);
            color: #9ca3af;
            border: 1px solid rgba(156, 163, 175, 0.3);
        }

        .btn-secondary:hover {
            background: rgba(156, 163, 175, 0.3);
            color: white;
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        /* Image Styles */
        .bukti-payment {
            max-width: 300px;
            max-height: 200px;
            border-radius: 12px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .bukti-payment:hover {
            transform: scale(1.05);
            border-color: rgba(255, 255, 255, 0.4);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
        }

        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            border-radius: 12px;
            margin-top: 5%;
        }

        .close {
            position: absolute;
            top: 2rem;
            right: 3rem;
            color: white;
            font-size: 3rem;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #ccc;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .mobile-menu-btn {
                display: block;
            }

            .content-header h1 {
                font-size: 2rem;
            }

            .detail-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .btn-group {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .content-header {
                padding: 1.5rem;
            }

            .detail-card {
                padding: 1.5rem;
            }

            .content-header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Mobile Menu Button -->
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Mobile Overlay -->
        <div class="mobile-overlay" onclick="closeMobileMenu()"></div>

        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-building"></i> Admin Panel</h2>
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="dashboard.php">
                        <i class="fas fa-home"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="data_penyewa.php">
                        <i class="fas fa-users"></i>
                        Data Penyewa
                    </a>
                </li>
                <li class="nav-item">
                    <a href="data_pemesanan.php" class="active">
                        <i class="fas fa-calendar-check"></i>
                        Data Pemesanan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="riwayat_pemesanan.php">
                        <i class="fas fa-history"></i>
                        Riwayat Pemesanan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="laporan_penyewaan.php">
                        <i class="fas fa-chart-bar"></i>
                        Laporan Penyewaan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="account.php">
                        <i class="fas fa-user-cog"></i>
                        Akun Admin
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="content-header">
                <h1><i class="fas fa-eye"></i> Detail Pemesanan</h1>
                <p>Lihat dan kelola detail pemesanan pelanggan</p>
            </div>

            <!-- Alerts -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($pemesanan)): ?>
                <!-- Informasi Pemesanan -->
                <div class="detail-card">
                    <h2 class="detail-title"><i class="fas fa-calendar-alt"></i> Informasi Pemesanan</h2>
                    
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">ID Pemesanan</div>
                            <div class="detail-value">#<?= htmlspecialchars($pemesanan['id_pemesanan']) ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Tanggal Pemesanan</div>
                            <div class="detail-value"><?= formatTanggal($pemesanan['tanggal_pesan']) ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Tanggal Sewa</div>
                            <div class="detail-value"><?= formatTanggal($pemesanan['tanggal_sewa']) ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Tanggal Selesai</div>
                            <div class="detail-value"><?= formatTanggal($pemesanan['tanggal_selesai']) ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Durasi</div>
                            <div class="detail-value"><?= htmlspecialchars($pemesanan['durasi']) ?> hari</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Total Pembayaran</div>
                            <div class="detail-value"><?= formatRupiah($pemesanan['total']) ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Metode Pembayaran</div>
                            <div class="detail-value"><?= htmlspecialchars(str_replace('_', ' ', $pemesanan['metode_pembayaran'])) ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Tipe Pesanan</div>
                            <div class="detail-value"><?= ucfirst(htmlspecialchars($pemesanan['tipe_pesanan'])) ?></div>
                        </div>

                        <?php if ($pemesanan['kebutuhan_tambahan']): ?>
                            <div class="detail-item" style="grid-column: 1 / -1;">
                                <div class="detail-label">Kebutuhan Tambahan</div>
                                <div class="detail-value"><?= htmlspecialchars($pemesanan['kebutuhan_tambahan']) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Informasi Penyewa -->
                <div class="detail-card">
                    <h2 class="detail-title"><i class="fas fa-user"></i> Informasi Penyewa</h2>
                    
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">Tipe Penyewa</div>
                            <div class="detail-value"><?= ucfirst(htmlspecialchars($pemesanan['tipe_penyewa'])) ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Nama</div>
                            <div class="detail-value">
                                <?= $pemesanan['tipe_penyewa'] === 'instansi' 
                                    ? htmlspecialchars($pemesanan['nama_instansi']) 
                                    : htmlspecialchars($pemesanan['nama_lengkap']) ?>
                            </div>
                        </div>

                        <?php if ($pemesanan['tipe_penyewa'] === 'individu' && $pemesanan['nik']): ?>
                            <div class="detail-item">
                                <div class="detail-label">NIK</div>
                                <div class="detail-value"><?= htmlspecialchars($pemesanan['nik']) ?></div>
                            </div>
                        <?php endif; ?>

                        <div class="detail-item">
                            <div class="detail-label">Email</div>
                            <div class="detail-value"><?= htmlspecialchars($pemesanan['email']) ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">No. Telepon</div>
                            <div class="detail-value"><?= htmlspecialchars($pemesanan['no_telepon']) ?></div>
                        </div>

                        <div class="detail-item" style="grid-column: 1 / -1;">
                            <div class="detail-label">Alamat</div>
                            <div class="detail-value"><?= htmlspecialchars($pemesanan['alamat']) ?></div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Acara -->
                <div class="detail-card">
                    <h2 class="detail-title"><i class="fas fa-calendar-check"></i> Informasi Jenis Acara</h2>
                    
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">Nama Acara</div>
                            <div class="detail-value"><?= htmlspecialchars($pemesanan['nama_acara']) ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Harga Sewa</div>
                            <div class="detail-value"><?= formatRupiah($pemesanan['harga']) ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Lokasi</div>
                            <div class="detail-value"><?= htmlspecialchars($pemesanan['lokasi']) ?></div>
                        </div>

                        <?php if ($pemesanan['fasilitas']): ?>
                            <div class="detail-item" style="grid-column: 1 / -1;">
                                <div class="detail-label">Fasilitas</div>
                                <div class="detail-value"><?= htmlspecialchars($pemesanan['fasilitas']) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Informasi Pembayaran & Form Update -->
                <div class="detail-card">
                    <h2 class="detail-title"><i class="fas fa-credit-card"></i> Kelola Status Pembayaran</h2>
                    
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">Status Pembayaran Saat Ini</div>
                            <div class="detail-value">
                                <?php if ($pemesanan['status_pembayaran']): ?>
                                    <span class="badge <?= getPaymentStatusBadgeClass($pemesanan['status_pembayaran']) ?>">
                                        <?= ucfirst($pemesanan['status_pembayaran']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Belum Ada</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($pemesanan['tanggal_pembayaran']): ?>
                            <div class="detail-item">
                                <div class="detail-label">Tanggal Upload Bukti</div>
                                <div class="detail-value"><?= formatTanggal($pemesanan['tanggal_pembayaran']) ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if ($pemesanan['bukti_pembayaran']): ?>
                            <div class="detail-item" style="grid-column: 1 / -1;">
                                <div class="detail-label">Bukti Pembayaran</div>
                                <div class="detail-value">
                                    <img src="../uploads/<?= htmlspecialchars($pemesanan['bukti_pembayaran']) ?>" 
                                         alt="Bukti Pembayaran" class="bukti-payment" onclick="openModal(this.src)">
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Update Form -->
                    <form method="POST" action="" style="margin-top: 2rem;">
                        <div class="form-group">
                            <label for="status_pembayaran" class="form-label">
                                <i class="fas fa-money-check-alt"></i>
                                Update Status Pembayaran
                            </label>
                            <select id="status_pembayaran" name="status_pembayaran" class="form-control" required>
                                <option value="Belum Lunas" <?= ($pemesanan['status_pembayaran'] ?? '') === 'Belum Lunas' ? 'selected' : '' ?>>Belum Lunas</option>
                                <option value="Lunas" <?= ($pemesanan['status_pembayaran'] ?? '') === 'Lunas' ? 'selected' : '' ?>>Lunas</option>
                            </select>
                        </div>

                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Update Status
                            </button>
                            <a href="data_pemesanan.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Kembali ke Data Pemesanan
                            </a>
                        </div>
                    </form>
                </div>

            <?php else: ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Data pemesanan tidak ditemukan atau ID tidak valid.
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Modal for image preview -->
    <div id="imageModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <script>
        // Mobile Menu Functions
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
        
        // Close mobile menu when clicking on nav links
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-item a');
            navLinks.forEach(link => {
                link.addEventListener('click', closeMobileMenu);
            });
        });

        // Modal Functions
        function openModal(src) {
            document.getElementById('imageModal').style.display = 'block';
            document.getElementById('modalImage').src = src;
        }

        function closeModal() {
            document.getElementById('imageModal').style.display = 'none';
        }

        // Close modal when clicking outside the image
        window.onclick = function(event) {
            const modal = document.getElementById('imageModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Auto hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 300);
                }, 5000);
            });
        });
    </script>
</body>
</html>
