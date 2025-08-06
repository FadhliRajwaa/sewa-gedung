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
        $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
        
        $query = "UPDATE admin SET username = '$username', email = '$email', nama_lengkap = '$nama_lengkap' WHERE id_admin = '$admin_id'";
        if (mysqli_query($conn, $query)) {
            $message = 'Profil berhasil diperbarui!';
            $message_type = 'success';
            $_SESSION['admin_username'] = $username;
        } else {
            $message = 'Gagal memperbarui profil!';
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
                        $message = 'Gagal mengubah password!';
                        $message_type = 'error';
                    }
                } else {
                    $message = 'Password minimal 6 karakter!';
                    $message_type = 'error';
                }
            } else {
                $message = 'Konfirmasi password tidak cocok!';
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
    <title>Akun Admin - Sewa Gedung</title>
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
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .page-header {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            text-align: center;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--gray);
        }

        .card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-header {
            background: var(--primary);
            color: var(--white);
            padding: 1.5rem;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .card-body {
            padding: 2rem;
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

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
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

        .admin-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            padding: 1.5rem;
            border-radius: var(--radius);
            text-align: center;
        }

        .info-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .info-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }
            
            .container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }
            
            .page-header,
            .card-body {
                padding: 1.5rem;
            }
            
            .admin-info {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="dashboard.php" class="navbar-brand">
            <i class="fas fa-cog"></i> Admin Panel
        </a>
        <div class="navbar-nav">
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-user-cog"></i> Pengaturan Akun
            </h1>
            <p class="page-subtitle">Kelola informasi dan keamanan akun admin Anda</p>
        </div>

        <!-- Alert Messages -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Admin Info Cards -->
        <div class="admin-info">
            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="info-value"><?php echo htmlspecialchars($admin['username']); ?></div>
                <div class="info-label">Username</div>
            </div>
            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="info-value"><?php echo htmlspecialchars($admin['email']); ?></div>
                <div class="info-label">Email</div>
            </div>
            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="info-value"><?php echo date('d M Y', strtotime($admin['created_at'])); ?></div>
                <div class="info-label">Bergabung</div>
            </div>
        </div>

        <!-- Update Profile Form -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-edit"></i> Perbarui Profil
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" 
                               value="<?php echo htmlspecialchars($admin['nama_lengkap']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" 
                               value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <!-- Change Password Form -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-key"></i> Ubah Password
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Password Lama</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="new_password" class="form-control" required>
                        <small style="color: var(--gray);">Minimal 6 karakter</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">
                        <i class="fas fa-lock"></i> Ubah Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Back to Dashboard -->
        <div style="text-align: center; margin-top: 2rem;">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>
