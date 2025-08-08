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
                            email_terverifikasi = ?
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
            
            $success = 'Data penyewa berhasil diperbarui';
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
            font-size: 14px;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
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

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #8B4513;
            box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25);
        }

        .form-select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            cursor: pointer;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin: 0;
        }

        .form-check-label {
            margin: 0;
            cursor: pointer;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .form-actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        /* Dynamic form fields based on type */
        .type-specific {
            display: none;
        }

        .type-specific.active {
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

            .form-grid {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .form-actions {
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
                <h1>Edit Penyewa</h1>
                <a href="data_penyewa.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            </div>

            <div class="form-section">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= $success ?>
                    </div>
                <?php endif; ?>

                <?php if ($penyewa): ?>
                    <h2 class="form-title">Edit Data Penyewa</h2>
                    
                    <form method="POST" id="editForm">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="tipe_penyewa" class="form-label">Tipe Penyewa *</label>
                                <select id="tipe_penyewa" name="tipe_penyewa" class="form-select" required onchange="toggleTypeFields()">
                                    <option value="umum" <?= $penyewa['tipe_penyewa'] === 'umum' ? 'selected' : '' ?>>Umum</option>
                                    <option value="instansi" <?= $penyewa['tipe_penyewa'] === 'instansi' ? 'selected' : '' ?>>Instansi</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="username" class="form-label">Username *</label>
                                <input type="text" id="username" name="username" class="form-control" 
                                       value="<?= htmlspecialchars($penyewa['username']) ?>" required>
                            </div>

                            <!-- Umum fields -->
                            <div class="form-group type-specific umum-fields <?= $penyewa['tipe_penyewa'] === 'umum' ? 'active' : '' ?>">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap *</label>
                                <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" 
                                       value="<?= htmlspecialchars($penyewa['nama_lengkap']) ?>">
                            </div>

                            <div class="form-group type-specific umum-fields <?= $penyewa['tipe_penyewa'] === 'umum' ? 'active' : '' ?>">
                                <label for="nik" class="form-label">NIK</label>
                                <input type="text" id="nik" name="nik" class="form-control" 
                                       value="<?= htmlspecialchars($penyewa['nik']) ?>">
                            </div>

                            <!-- Instansi fields -->
                            <div class="form-group type-specific instansi-fields <?= $penyewa['tipe_penyewa'] === 'instansi' ? 'active' : '' ?>">
                                <label for="nama_instansi" class="form-label">Nama Instansi *</label>
                                <input type="text" id="nama_instansi" name="nama_instansi" class="form-control" 
                                       value="<?= htmlspecialchars($penyewa['nama_instansi']) ?>">
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       value="<?= htmlspecialchars($penyewa['email']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="no_telepon" class="form-label">No. Telepon *</label>
                                <input type="tel" id="no_telepon" name="no_telepon" class="form-control" 
                                       value="<?= htmlspecialchars($penyewa['no_telepon']) ?>" required>
                            </div>

                            <div class="form-group full-width">
                                <label for="alamat" class="form-label">Alamat *</label>
                                <textarea id="alamat" name="alamat" class="form-control" required><?= htmlspecialchars($penyewa['alamat']) ?></textarea>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" id="email_terverifikasi" name="email_terverifikasi" 
                                           class="form-check-input" <?= $penyewa['email_terverifikasi'] ? 'checked' : '' ?>>
                                    <label for="email_terverifikasi" class="form-check-label">Email Terverifikasi</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Simpan Perubahan
                            </button>
                            <a href="penyewa_view.php?id=<?= $penyewa['id_penyewa'] ?>" class="btn btn-secondary">
                                <i class="fas fa-eye"></i>
                                Lihat Detail
                            </a>
                            <a href="data_penyewa.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                Batal
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function toggleTypeFields() {
            const tipeSelect = document.getElementById('tipe_penyewa');
            const umumFields = document.querySelectorAll('.umum-fields');
            const instansiFields = document.querySelectorAll('.instansi-fields');
            
            if (tipeSelect.value === 'umum') {
                umumFields.forEach(field => {
                    field.classList.add('active');
                    field.style.display = 'block';
                    const input = field.querySelector('input');
                    if (input && input.id === 'nama_lengkap') {
                        input.required = true;
                    }
                });
                instansiFields.forEach(field => {
                    field.classList.remove('active');
                    field.style.display = 'none';
                    const input = field.querySelector('input');
                    if (input) {
                        input.required = false;
                    }
                });
            } else {
                instansiFields.forEach(field => {
                    field.classList.add('active');
                    field.style.display = 'block';
                    const input = field.querySelector('input');
                    if (input && input.id === 'nama_instansi') {
                        input.required = true;
                    }
                });
                umumFields.forEach(field => {
                    field.classList.remove('active');
                    field.style.display = 'none';
                    const input = field.querySelector('input');
                    if (input) {
                        input.required = false;
                    }
                });
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleTypeFields();
        });
    </script>
</body>
</html>
