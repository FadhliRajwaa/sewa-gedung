<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$penyewa = null;
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipe_penyewa = $_POST['tipe_penyewa'] ?? '';
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $nama_instansi = $_POST['nama_instansi'] ?? '';
    $nik = $_POST['nik'] ?? '';
    $email = $_POST['email'] ?? '';
    $no_telepon = $_POST['no_telepon'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $username = $_POST['username'] ?? '';
    $email_terverifikasi = isset($_POST['email_terverifikasi']) ? 1 : 0;
    
    try {
        // Check if email/username exists for other users
        $checkQuery = "SELECT id_penyewa FROM penyewa WHERE (email = ? OR username = ?) AND id_penyewa != ?";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute([$email, $username, $id]);
        
        if ($checkStmt->rowCount() > 0) {
            $error = 'Email atau username sudah digunakan oleh penyewa lain';
        } else {
            $updateQuery = "UPDATE penyewa SET 
                            tipe_penyewa = ?, 
                            nama_lengkap = ?, 
                            nama_instansi = ?, 
                            nik = ?, 
                            email = ?, 
                            no_telepon = ?, 
                            alamat = ?, 
                            username = ?, 
                            email_terverifikasi = ?, 
                            updated_at = NOW() 
                            WHERE id_penyewa = ?";
            
            $stmt = $pdo->prepare($updateQuery);
            $stmt->execute([
                $tipe_penyewa,
                $nama_lengkap,
                $nama_instansi,
                $nik,
                $email,
                $no_telepon,
                $alamat,
                $username,
                $email_terverifikasi,
                $id
            ]);
            
            $success = 'Data penyewa berhasil diperbarui!';
        }
    } catch (PDOException $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Fetch penyewa data
if ($id) {
    try {
        $query = "SELECT * FROM penyewa WHERE id_penyewa = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        $penyewa = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$penyewa) {
            $error = 'Data penyewa tidak ditemukan';
        }
    } catch (PDOException $e) {
        $error = 'Error: ' . $e->getMessage();
    }
} else {
    $error = 'ID penyewa tidak valid';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Penyewa - Admin</title>
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
            min-height: 100vh;
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

        .sidebar-header i {
            font-size: 24px;
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
            position: relative;
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

        /* Mobile Menu Toggle */
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
            transition: all 0.2s ease;
        }

        .mobile-menu-toggle:hover {
            background: var(--gray-light);
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
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-subtitle {
            color: var(--gray);
            font-size: 16px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            font-size: 14px;
            color: var(--gray);
        }

        .breadcrumb a {
            color: var(--primary);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        /* Cards */
        .card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, var(--gray-light) 0%, #ffffff 100%);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-body {
            padding: 24px;
        }

        /* Form Styles */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 6px;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: var(--radius);
            font-size: 14px;
            font-family: inherit;
            background: var(--white);
            transition: all 0.2s ease;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 8px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }

        .checkbox-group label {
            font-size: 14px;
            color: var(--dark);
            cursor: pointer;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: var(--radius);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
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

        .btn-success:hover {
            background: #059669;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 24px;
        }

        /* Alert Messages */
        .alert {
            padding: 16px 20px;
            border-radius: var(--radius);
            margin-bottom: 24px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-instansi {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-umum {
            background: #d1fae5;
            color: #065f46;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .main-content {
                padding: 24px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
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
                margin-bottom: 24px;
            }

            .page-title {
                font-size: 24px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 12px;
                padding-top: 72px;
            }

            .page-header {
                padding: 16px;
            }

            .card-body {
                padding: 16px;
            }

            .page-title {
                font-size: 20px;
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
                    <a href="data_penyewa.php" class="nav-link active">
                        <i class="fas fa-users"></i>
                        <span>Data Penyewa</span>
                    </a>
                    <a href="data_pemesanan.php" class="nav-link">
                        <i class="fas fa-calendar-check"></i>
                        <span>Data Pemesanan</span>
                    </a>
                    <a href="gedung.php" class="nav-link">
                        <i class="fas fa-building"></i>
                        <span>Data Gedung</span>
                    </a>
                    <a href="acara.php" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Data Acara</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Reports</div>
                    <a href="laporan.php" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Laporan</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Settings</div>
                    <a href="akun.php" class="nav-link">
                        <i class="fas fa-user-cog"></i>
                        <span>Pengaturan Akun</span>
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
            <div class="page-header">
                <div class="breadcrumb">
                    <a href="dashboard.php">Dashboard</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="data_penyewa.php">Data Penyewa</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Edit Penyewa</span>
                </div>
                <h1 class="page-title">
                    <i class="fas fa-user-edit"></i>
                    Edit Penyewa
                </h1>
                <p class="page-subtitle">Edit informasi data penyewa</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if ($penyewa): ?>
                <!-- Edit Form Card -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fas fa-user-edit"></i>
                            Edit Informasi Penyewa
                        </h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="tipe_penyewa" class="form-label">Tipe Penyewa</label>
                                    <select name="tipe_penyewa" id="tipe_penyewa" class="form-select" required onchange="toggleInstansiField()">
                                        <option value="">Pilih Tipe Penyewa</option>
                                        <option value="umum" <?= $penyewa['tipe_penyewa'] === 'umum' ? 'selected' : '' ?>>Umum</option>
                                        <option value="instansi" <?= $penyewa['tipe_penyewa'] === 'instansi' ? 'selected' : '' ?>>Instansi</option>
                                    </select>
                                </div>

                                <div class="form-group" id="nama_instansi_group" style="<?= $penyewa['tipe_penyewa'] !== 'instansi' ? 'display: none;' : '' ?>">
                                    <label for="nama_instansi" class="form-label">Nama Instansi</label>
                                    <input type="text" name="nama_instansi" id="nama_instansi" class="form-input" 
                                           value="<?= htmlspecialchars($penyewa['nama_instansi'] ?? '') ?>" placeholder="Masukkan nama instansi">
                                </div>

                                <div class="form-group">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-input" 
                                           value="<?= htmlspecialchars($penyewa['nama_lengkap'] ?? '') ?>" placeholder="Masukkan nama lengkap" required>
                                </div>

                                <div class="form-group">
                                    <label for="nik" class="form-label">NIK</label>
                                    <input type="text" name="nik" id="nik" class="form-input" 
                                           value="<?= htmlspecialchars($penyewa['nik'] ?? '') ?>" placeholder="Masukkan NIK">
                                </div>

                                <div class="form-group">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-input" 
                                           value="<?= htmlspecialchars($penyewa['email'] ?? '') ?>" placeholder="Masukkan email" required>
                                </div>

                                <div class="form-group">
                                    <label for="no_telepon" class="form-label">No. Telepon</label>
                                    <input type="tel" name="no_telepon" id="no_telepon" class="form-input" 
                                           value="<?= htmlspecialchars($penyewa['no_telepon'] ?? '') ?>" placeholder="Masukkan nomor telepon">
                                </div>

                                <div class="form-group">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" name="username" id="username" class="form-input" 
                                           value="<?= htmlspecialchars($penyewa['username'] ?? '') ?>" placeholder="Masukkan username" required>
                                </div>

                                <div class="form-group full-width">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea name="alamat" id="alamat" class="form-textarea" placeholder="Masukkan alamat lengkap"><?= htmlspecialchars($penyewa['alamat'] ?? '') ?></textarea>
                                </div>

                                <div class="form-group full-width">
                                    <div class="checkbox-group">
                                        <input type="checkbox" name="email_terverifikasi" id="email_terverifikasi" 
                                               <?= isset($penyewa['email_terverifikasi']) && $penyewa['email_terverifikasi'] ? 'checked' : '' ?>>
                                        <label for="email_terverifikasi">Email sudah terverifikasi</label>
                                    </div>
                                </div>
                            </div>

                            <div class="action-buttons">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i>
                                    Simpan Perubahan
                                </button>
                                <a href="penyewa_view.php?id=<?= $penyewa['id_penyewa'] ?>" class="btn btn-secondary">
                                    <i class="fas fa-eye"></i>
                                    Lihat Detail
                                </a>
                                <a href="data_penyewa.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i>
                                    Kembali ke Data Penyewa
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="action-buttons">
                    <a href="data_penyewa.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Data Penyewa
                    </a>
                </div>
            <?php endif; ?>
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

        function toggleInstansiField() {
            const tipePenyewa = document.getElementById('tipe_penyewa').value;
            const namaInstansiGroup = document.getElementById('nama_instansi_group');
            
            if (tipePenyewa === 'instansi') {
                namaInstansiGroup.style.display = 'block';
                document.getElementById('nama_instansi').required = true;
            } else {
                namaInstansiGroup.style.display = 'none';
                document.getElementById('nama_instansi').required = false;
                document.getElementById('nama_instansi').value = '';
            }
        }

        // Close mobile menu when window is resized to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeMobileMenu();
            }
        });

        // Initialize form state
        document.addEventListener('DOMContentLoaded', function() {
            toggleInstansiField();
        });
    </script>
</body>
</html>
