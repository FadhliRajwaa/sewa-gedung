<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include '../config.php';
include '../includes/db.php';

// Query untuk mendapatkan informasi akun admin
$admin_id = $_SESSION['admin_id'];
$sql = "SELECT * FROM admin WHERE id_admin = '$admin_id' LIMIT 1";
$result = mysqli_query($conn, $sql);
$admin = mysqli_fetch_assoc($result);

// Proses update akun admin
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $admin['password'];

    // Query untuk memperbarui data akun admin
    $update_sql = "UPDATE admin SET nama_lengkap = '$nama_lengkap', email = '$email', username = '$username', password = '$password' WHERE id_admin = '$admin_id'";

    if (mysqli_query($conn, $update_sql)) {
        $message = "Data akun berhasil diperbarui!";
        $message_type = "success";
        // Refresh data admin
        $result = mysqli_query($conn, $sql);
        $admin = mysqli_fetch_assoc($result);
    } else {
        $message = "Error: " . mysqli_error($conn);
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Admin - Sewa Gedung</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            background: #2c3e50;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
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
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: #8B4513;
            border: none;
            color: white;
            padding: 12px 15px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3);
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle:hover {
            background: #A0522D;
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(139, 69, 19, 0.4);
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

        /* Profile Card */
        .profile-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .profile-header-section {
            text-align: center;
            margin-bottom: 3rem;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .profile-avatar i {
            font-size: 3rem;
            color: white;
        }

        .profile-title {
            color: white;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .profile-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
        }

        /* Form Styles */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

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

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
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
            justify-content: center;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.25rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .info-value {
            color: white;
            font-size: 1.1rem;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
                transition: transform 0.3s ease;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 10px;
                padding-top: 70px;
            }

            .content-header {
                padding: 15px;
            }

            .content-header h1 {
                font-size: 18px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .btn-group {
                flex-direction: column;
                gap: 10px;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .profile-card,
            .form-section {
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 8px;
                padding-top: 65px;
            }

            .content-header {
                padding: 12px;
            }

            .content-header h1 {
                font-size: 16px;
            }

            .profile-card,
            .form-section {
                padding: 12px;
                margin: 0 -8px;
                border-radius: 0;
            }

            .form-group input,
            .form-group textarea {
                padding: 10px;
                font-size: 14px;
            }

            .btn {
                padding: 10px 15px;
                font-size: 14px;
            }
            }

            .profile-card {
                padding: 1.5rem;
            }

            .content-header h1 {
                font-size: 1.8rem;
            }

            .profile-avatar {
                width: 100px;
                height: 100px;
            }

            .profile-avatar i {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Mobile Menu Button -->
        <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
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
                    <a href="data_pemesanan.php">
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
                    <a href="account.php" class="active">
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
                <h1><i class="fas fa-user-cog"></i> Akun Admin</h1>
                <p>Kelola informasi akun administrator sistem</p>
            </div>

            <!-- Alerts -->
            <?php if (isset($message)): ?>
                <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'danger' ?>">
                    <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <!-- Profile Card -->
            <div class="profile-card">
                <!-- Profile Header -->
                <div class="profile-header-section">
                    <div class="profile-avatar">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h2 class="profile-title">Administrator</h2>
                    <p class="profile-subtitle">Sistem Sewa Gedung PT Aneka</p>
                </div>

                <!-- Current Info Display -->
                <h3 style="color: white; margin-bottom: 1.5rem; font-size: 1.4rem;">
                    <i class="fas fa-info-circle"></i> Informasi Akun Saat Ini
                </h3>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">ID Admin</div>
                        <div class="info-value">#<?= htmlspecialchars($admin['id_admin']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Username</div>
                        <div class="info-value"><?= htmlspecialchars($admin['username']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Nama Lengkap</div>
                        <div class="info-value"><?= htmlspecialchars($admin['nama_lengkap']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($admin['email']) ?></div>
                    </div>
                </div>

                <!-- Update Form -->
                <h3 style="color: white; margin: 2rem 0 1.5rem; font-size: 1.4rem;">
                    <i class="fas fa-edit"></i> Update Informasi Akun
                </h3>
                
                <form method="POST" action="">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nama_lengkap" class="form-label">
                                <i class="fas fa-user"></i>
                                Nama Lengkap
                            </label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" 
                                   value="<?= htmlspecialchars($admin['nama_lengkap']) ?>" 
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="username" class="form-label">
                                <i class="fas fa-at"></i>
                                Username
                            </label>
                            <input type="text" id="username" name="username" 
                                   value="<?= htmlspecialchars($admin['username']) ?>" 
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i>
                                Email
                            </label>
                            <input type="email" id="email" name="email" 
                                   value="<?= htmlspecialchars($admin['email']) ?>" 
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i>
                                Password Baru (Opsional)
                            </label>
                            <input type="password" id="password" name="password" 
                                   placeholder="Kosongkan jika tidak ingin mengubah password" 
                                   class="form-control">
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Simpan Perubahan
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </main>
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
