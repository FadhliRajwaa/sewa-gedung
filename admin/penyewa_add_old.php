<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipe_penyewa = $_POST['tipe_penyewa'] ?? '';
    $nama_instansi = $_POST['nama_instansi'] ?? null;
    $nama_lengkap = $_POST['nama_lengkap'] ?? null;
    $nik = $_POST['nik'] ?? null;
    $no_telepon = $_POST['no_telepon'] ?? '';
    $email = $_POST['email'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($tipe_penyewa) || empty($no_telepon) || empty($email) || empty($alamat) || empty($username) || empty($password)) {
        $error = 'Semua field yang wajib harus diisi';
    } else {
        try {
            // Check if email or username already exists
            $checkQuery = "SELECT id_penyewa FROM penyewa WHERE email = ? OR username = ?";
            $checkStmt = $pdo->prepare($checkQuery);
            $checkStmt->execute([$email, $username]);
            
            if ($checkStmt->fetch()) {
                $error = 'Email atau username sudah digunakan';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $query = "INSERT INTO penyewa (tipe_penyewa, nama_instansi, nama_lengkap, nik, no_telepon, email, alamat, username, password, email_terverifikasi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
                $stmt = $pdo->prepare($query);
                $result = $stmt->execute([$tipe_penyewa, $nama_instansi, $nama_lengkap, $nik, $no_telepon, $email, $alamat, $username, $hashedPassword]);
                
                if ($result) {
                    $success = 'Penyewa berhasil ditambahkan';
                    // Clear form
                    $tipe_penyewa = $nama_instansi = $nama_lengkap = $nik = $no_telepon = $email = $alamat = $username = $password = '';
                } else {
                    $error = 'Gagal menambahkan penyewa';
                }
            }
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Penyewa - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo h1 {
            color: white;
            font-size: 24px;
            font-weight: 700;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .nav-link i {
            margin-right: 12px;
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 20px;
        }

        .page-header {
            background: white;
            padding: 20px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            color: #8B4513;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-primary {
            background: #8B4513;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        /* Form Styles */
        .form-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .form-title {
            color: #8B4513;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #8B4513;
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }

        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            cursor: pointer;
        }

        .form-select:focus {
            outline: none;
            border-color: #8B4513;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .form-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
        }

        .conditional-field {
            display: none;
        }

        .conditional-field.show {
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .form-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <h1>Sewa Gedung</h1>
                <p style="color: rgba(255,255,255,0.7); font-size: 12px;">Admin Panel</p>
            </div>
            
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <i class="fas fa-home"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="data_penyewa.php" class="nav-link active">
                            <i class="fas fa-users"></i>
                            Data Penyewa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="data_pemesanan.php" class="nav-link">
                            <i class="fas fa-calendar-check"></i>
                            Data Pemesanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="riwayat_pemesanan.php" class="nav-link">
                            <i class="fas fa-history"></i>
                            Riwayat Pemesanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="laporan_penyewaan.php" class="nav-link">
                            <i class="fas fa-chart-bar"></i>
                            Laporan Penyewaan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="account.php" class="nav-link">
                            <i class="fas fa-user-cog"></i>
                            Akun Admin
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1>Tambah Penyewa</h1>
                <a href="data_penyewa.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            </div>

            <div class="form-section">
                <h2 class="form-title">Form Tambah Penyewa Baru</h2>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= $success ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label">Tipe Penyewa *</label>
                        <select name="tipe_penyewa" class="form-select" required onchange="toggleFields(this.value)">
                            <option value="">Pilih Tipe Penyewa</option>
                            <option value="individu" <?= (isset($tipe_penyewa) && $tipe_penyewa === 'individu') ? 'selected' : '' ?>>Individu</option>
                            <option value="instansi" <?= (isset($tipe_penyewa) && $tipe_penyewa === 'instansi') ? 'selected' : '' ?>>Instansi</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group conditional-field" id="namaLengkapField">
                            <label class="form-label">Nama Lengkap *</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($nama_lengkap ?? '') ?>">
                        </div>

                        <div class="form-group conditional-field" id="namaInstansiField">
                            <label class="form-label">Nama Instansi *</label>
                            <input type="text" name="nama_instansi" class="form-control" value="<?= htmlspecialchars($nama_instansi ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group conditional-field" id="nikField">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" class="form-control" value="<?= htmlspecialchars($nik ?? '') ?>" maxlength="16">
                        </div>

                        <div class="form-group">
                            <label class="form-label">No. Telepon *</label>
                            <input type="text" name="no_telepon" class="form-control" value="<?= htmlspecialchars($no_telepon ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat *</label>
                        <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($alamat ?? '') ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Username *</label>
                            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($username ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Simpan Penyewa
                        </button>
                        <a href="data_penyewa.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        function toggleFields(type) {
            const namaLengkapField = document.getElementById('namaLengkapField');
            const namaInstansiField = document.getElementById('namaInstansiField');
            const nikField = document.getElementById('nikField');
            
            // Hide all conditional fields first
            namaLengkapField.classList.remove('show');
            namaInstansiField.classList.remove('show');
            nikField.classList.remove('show');
            
            // Clear values
            namaLengkapField.querySelector('input').value = '';
            namaInstansiField.querySelector('input').value = '';
            nikField.querySelector('input').value = '';
            
            // Show relevant fields based on type
            if (type === 'individu') {
                namaLengkapField.classList.add('show');
                nikField.classList.add('show');
                namaLengkapField.querySelector('input').required = true;
                namaInstansiField.querySelector('input').required = false;
            } else if (type === 'instansi') {
                namaInstansiField.classList.add('show');
                namaInstansiField.querySelector('input').required = true;
                namaLengkapField.querySelector('input').required = false;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const tipeSelect = document.querySelector('select[name="tipe_penyewa"]');
            if (tipeSelect.value) {
                toggleFields(tipeSelect.value);
            }
        });
    </script>
</body>
</html>
