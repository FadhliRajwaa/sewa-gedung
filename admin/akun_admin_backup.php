<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];
$message = '';
$message_type = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['update_profile'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        
        $query = "UPDATE admin SET username = '$username', email = '$email' WHERE id_admin = '$admin_id'";
        if (mysqli_query($conn, $query)) {
            $message = 'Profil berhasil diperbarui!';
            $message_type = 'success';
            $_SESSION['admin_username'] = $username;
        } else {
            $message = 'Gagal memperbarui profil: ' . mysqli_error($conn);
            $message_type = 'error';
        }
    }
    
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Verify current password
        $check_query = "SELECT password FROM admin WHERE id_admin = '$admin_id'";
        $check_result = mysqli_query($conn, $check_query);
        $admin_data = mysqli_fetch_assoc($check_result);
        
        if (password_verify($current_password, $admin_data['password'])) {
            if ($new_password === $confirm_password) {
                if (strlen($new_password) >= 6) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_query = "UPDATE admin SET password = '$hashed_password' WHERE id_admin = '$admin_id'";
                    
                    if (mysqli_query($conn, $update_query)) {
                        $message = 'Password berhasil diubah!';
                        $message_type = 'success';
                    } else {
                        $message = 'Gagal mengubah password: ' . mysqli_error($conn);
                        $message_type = 'error';
                    }
                } else {
                    $message = 'Password baru minimal 6 karakter!';
                    $message_type = 'error';
                }
            } else {
                $message = 'Password baru dan konfirmasi tidak cocok!';
                $message_type = 'error';
            }
        } else {
            $message = 'Password lama tidak benar!';
            $message_type = 'error';
        }
    }
}

// Get current admin data
$admin_query = "SELECT * FROM admin WHERE id_admin = '$admin_id'";
$admin_result = mysqli_query($conn, $admin_query);
$admin = mysqli_fetch_assoc($admin_result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Admin - Admin Panel</title>
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
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .page-subtitle {
            color: var(--gray);
            font-size: 16px;
        }

        /* Profile Section */
        .profile-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-bottom: 32px;
        }

        .profile-card {
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

        .form-input {
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

        .form-input:focus {
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

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
            color: var(--white);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Avatar Section */
        .avatar-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 48px;
            color: var(--white);
            box-shadow: var(--shadow-lg);
        }

        .avatar-name {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .avatar-role {
            color: var(--gray);
            font-size: 16px;
        }

        /* Alert */
        .alert {
            padding: 16px 20px;
            border-radius: var(--radius);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--white);
            padding: 24px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            text-align: center;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--white);
            margin: 0 auto 16px;
        }

        .stat-icon.primary { background: linear-gradient(135deg, var(--primary), var(--secondary)); }
        .stat-icon.success { background: linear-gradient(135deg, var(--success), #16a34a); }
        .stat-icon.warning { background: linear-gradient(135deg, var(--warning), #ea580c); }

        .stat-number {
            font-size: 28px;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--gray);
            font-size: 14px;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .main-content {
                padding: 24px;
            }

            .profile-section {
                grid-template-columns: 1fr;
                gap: 24px;
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
            }

            .page-title {
                font-size: 24px;
            }

            .card-body {
                padding: 24px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
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
                    <a href="data_pemesanan.php" class="nav-link">
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
                    <a href="akun_admin.php" class="nav-link active">
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
                <h1 class="page-title">Akun Admin</h1>
                <p class="page-subtitle">Kelola profil dan keamanan akun administrator</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Avatar Section -->
            <div class="profile-card">
                <div class="card-body">
                    <div class="avatar-section">
                        <div class="avatar">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="avatar-name"><?php echo htmlspecialchars($admin['username']); ?></div>
                        <div class="avatar-role">Administrator</div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-number" id="loginToday">0</div>
                    <div class="stat-label">Login Hari Ini</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number" id="lastLogin">
                        <?php 
                        if ($admin['last_login']) {
                            echo date('H:i', strtotime($admin['last_login']));
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </div>
                    <div class="stat-label">Login Terakhir</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="stat-number">
                        <?php 
                        $days_ago = floor((time() - strtotime($admin['created_at'])) / (60 * 60 * 24));
                        echo $days_ago;
                        ?>
                    </div>
                    <div class="stat-label">Hari Sebagai Admin</div>
                </div>
            </div>

            <!-- Profile Section -->
            <div class="profile-section">
                <!-- Update Profile -->
                <div class="profile-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-edit"></i>
                            Update Profil
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-input" 
                                       value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-input" 
                                       value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="profile-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-lock"></i>
                            Ubah Password
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label class="form-label">Password Lama</label>
                                <input type="password" name="current_password" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="new_password" class="form-input" 
                                       minlength="6" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="confirm_password" class="form-input" 
                                       minlength="6" required>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-danger">
                                <i class="fas fa-key"></i>
                                Ubah Password
                            </button>
                        </form>
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

        // Load login stats
        document.addEventListener('DOMContentLoaded', function() {
            // You can add AJAX calls here to load real-time stats
            console.log('Admin account page loaded');
        });
    </script>
</body>
</html>
