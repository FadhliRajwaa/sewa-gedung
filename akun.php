<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['id_penyewa'])) {
    header('Location: login.php');
    exit;
}

$id_penyewa = $_SESSION['id_penyewa'];
$success = "";
$error = "";

// Fetch user data from the database
try {
    $stmt = $pdo->prepare("SELECT * FROM penyewa WHERE id_penyewa = ?");
    $stmt->execute([$id_penyewa]);
    $penyewa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$penyewa) {
        $error = "Data pengguna tidak ditemukan.";
    }
} catch (PDOException $e) {
    $error = "Gagal mengambil data pengguna.";
}

// Update user data if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = htmlspecialchars($_POST['nama_lengkap']);
    $no_telepon = htmlspecialchars($_POST['no_telepon']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $password = $_POST['password'];

    try {
        if (!empty($password)) {
            $stmt = $pdo->prepare("UPDATE penyewa SET nama_lengkap = ?, no_telepon = ?, alamat = ?, password = ? WHERE id_penyewa = ?");
            $stmt->execute([$nama_lengkap, $no_telepon, $alamat, password_hash($password, PASSWORD_BCRYPT), $id_penyewa]);
        } else {
            $stmt = $pdo->prepare("UPDATE penyewa SET nama_lengkap = ?, no_telepon = ?, alamat = ? WHERE id_penyewa = ?");
            $stmt->execute([$nama_lengkap, $no_telepon, $alamat, $id_penyewa]);
        }

        $_SESSION['nama_lengkap'] = $nama_lengkap;
        $success = "Data akun berhasil diperbarui.";
        
        // Refresh data
        $stmt = $pdo->prepare("SELECT * FROM penyewa WHERE id_penyewa = ?");
        $stmt->execute([$id_penyewa]);
        $penyewa = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Gagal memperbarui data akun.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya - PT. Aneka Usaha</title>
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
            max-width: 800px;
            margin: 0 auto;
        }

        .account-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
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

        /* Profile Section */
        .profile-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: 700;
        }

        .profile-info h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .profile-info p {
            color: #666;
            margin: 0;
        }

        /* Form Styles */
        .form-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-input:focus {
            outline: none;
            border-color: #8B4513;
            background: white;
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }

        .form-input:disabled {
            background: #e9ecef;
            cursor: not-allowed;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
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

        .btn-secondary {
            background: #6c757d;
            color: white;
            margin-left: 10px;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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

            .account-container {
                padding: 25px 20px;
                margin: 15px;
            }

            .page-title {
                font-size: 1.8rem;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
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
                <li><a href="dashboard_user.php" class="nav-link"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="acara_saya.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Acara Saya</a></li>
                <li><a href="panduan.php" class="nav-link"><i class="fas fa-book"></i> Panduan</a></li>
                <li><a href="akun.php" class="nav-link active"><i class="fas fa-user"></i> Akun</a></li>
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
            <div class="account-container">
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-user-circle"></i>
                        Akun Saya
                    </h1>
                    <p class="page-subtitle">
                        Kelola informasi profil dan data pribadi Anda
                    </p>
                </div>

                <!-- Alert Messages -->
                <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $success ?>
                </div>
                <?php elseif ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
                <?php endif; ?>

                <!-- Profile Section -->
                <div class="profile-section">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <?= strtoupper(substr($penyewa['nama_lengkap'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="profile-info">
                            <h3><?= htmlspecialchars($penyewa['nama_lengkap'] ?? 'Nama Pengguna') ?></h3>
                            <p><?= htmlspecialchars($penyewa['email'] ?? '') ?></p>
                            <p>Tipe: <?= ucfirst($penyewa['tipe_penyewa'] ?? 'Umum') ?></p>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div style="text-align: center; padding: 15px; background: white; border-radius: 10px;">
                            <div style="font-size: 1.2rem; font-weight: 600; color: #333;">NIK</div>
                            <div style="color: #666;"><?= htmlspecialchars($penyewa['nik'] ?? '-') ?></div>
                        </div>
                        <div style="text-align: center; padding: 15px; background: white; border-radius: 10px;">
                            <div style="font-size: 1.2rem; font-weight: 600; color: #333;">Username</div>
                            <div style="color: #666;"><?= htmlspecialchars($penyewa['username'] ?? '-') ?></div>
                        </div>
                        <div style="text-align: center; padding: 15px; background: white; border-radius: 10px;">
                            <div style="font-size: 1.2rem; font-weight: 600; color: #333;">Email Status</div>
                            <div style="color: <?= $penyewa['email_terverifikasi'] ? '#28a745' : '#dc3545' ?>;">
                                <?= $penyewa['email_terverifikasi'] ? 'Terverifikasi' : 'Belum Verifikasi' ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Form Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-edit"></i>
                        Edit Profil
                    </h3>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-input" value="<?= htmlspecialchars($penyewa['nama_lengkap'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telepon" class="form-input" value="<?= htmlspecialchars($penyewa['no_telepon'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-input" rows="4" style="resize: vertical;"><?= htmlspecialchars($penyewa['alamat'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Password Baru (opsional)</label>
                            <input type="password" name="password" class="form-input" placeholder="Kosongkan jika tidak ingin mengubah password">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email (tidak dapat diubah)</label>
                            <input type="email" class="form-input" value="<?= htmlspecialchars($penyewa['email'] ?? '') ?>" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Username (tidak dapat diubah)</label>
                            <input type="text" class="form-input" value="<?= htmlspecialchars($penyewa['username'] ?? '') ?>" disabled>
                        </div>
                        
                        <div style="text-align: center; margin-top: 30px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Simpan Perubahan
                            </button>
                            <a href="dashboard_user.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Kembali ke Dashboard
                            </a>
                        </div>
                    </form>
                </div>
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
    </script>
</body>
</html>
