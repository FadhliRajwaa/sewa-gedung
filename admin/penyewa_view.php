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
    <title>Detail Penyewa - Admin</title>
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

        .btn-warning {
            background: #ffc107;
            color: #212529;
        }

        .btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        /* Detail Section */
        .detail-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .detail-title {
            color: #8B4513;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .detail-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #8B4513;
        }

        .detail-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            color: #212529;
            font-size: 16px;
            word-break: break-word;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-secondary {
            background: #e2e3e5;
            color: #383d41;
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

        .actions-section {
            margin-top: 30px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
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
                <h1>Detail Penyewa</h1>
                <div>
                    <?php if ($penyewa): ?>
                        <a href="penyewa_edit.php?id=<?= $penyewa['id_penyewa'] ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i>
                            Edit
                        </a>
                    <?php endif; ?>
                    <a href="data_penyewa.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </a>
                </div>
            </div>

            <div class="detail-section">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= $error ?>
                    </div>
                <?php elseif ($penyewa): ?>
                    <h2 class="detail-title">Informasi Penyewa</h2>
                    
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">ID Penyewa</div>
                            <div class="detail-value"><?= htmlspecialchars($penyewa['id_penyewa']) ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Tipe Penyewa</div>
                            <div class="detail-value">
                                <span class="badge <?= $penyewa['tipe_penyewa'] === 'instansi' ? 'badge-info' : 'badge-secondary' ?>">
                                    <?= ucfirst($penyewa['tipe_penyewa']) ?>
                                </span>
                            </div>
                        </div>

                        <?php if ($penyewa['tipe_penyewa'] === 'instansi'): ?>
                            <div class="detail-item">
                                <div class="detail-label">Nama Instansi</div>
                                <div class="detail-value"><?= htmlspecialchars($penyewa['nama_instansi']) ?></div>
                            </div>
                        <?php else: ?>
                            <div class="detail-item">
                                <div class="detail-label">Nama Lengkap</div>
                                <div class="detail-value"><?= htmlspecialchars($penyewa['nama_lengkap']) ?></div>
                            </div>

                            <?php if ($penyewa['nik']): ?>
                                <div class="detail-item">
                                    <div class="detail-label">NIK</div>
                                    <div class="detail-value"><?= htmlspecialchars($penyewa['nik']) ?></div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="detail-item">
                            <div class="detail-label">Email</div>
                            <div class="detail-value"><?= htmlspecialchars($penyewa['email']) ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">No. Telepon</div>
                            <div class="detail-value"><?= htmlspecialchars($penyewa['no_telepon']) ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Username</div>
                            <div class="detail-value"><?= htmlspecialchars($penyewa['username']) ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Status Email</div>
                            <div class="detail-value">
                                <span class="badge <?= $penyewa['email_terverifikasi'] ? 'badge-success' : 'badge-warning' ?>">
                                    <?= $penyewa['email_terverifikasi'] ? 'Terverifikasi' : 'Belum Terverifikasi' ?>
                                </span>
                            </div>
                        </div>

                        <div class="detail-item" style="grid-column: 1 / -1;">
                            <div class="detail-label">Alamat</div>
                            <div class="detail-value"><?= nl2br(htmlspecialchars($penyewa['alamat'])) ?></div>
                        </div>
                    </div>

                    <div class="actions-section">
                        <a href="penyewa_edit.php?id=<?= $penyewa['id_penyewa'] ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i>
                            Edit Penyewa
                        </a>
                        <a href="data_penyewa.php" class="btn btn-secondary">
                            <i class="fas fa-list"></i>
                            Kembali ke Daftar
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
