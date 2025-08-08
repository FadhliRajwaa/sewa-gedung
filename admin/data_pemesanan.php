<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pemesanan - Admin</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark);
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: var(--radius);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-sm {
            padding: 8px 12px;
            font-size: 12px;
        }

        .btn-info {
            background: linear-gradient(135deg, var(--accent) 0%, #0891b2 100%);
            color: var(--white);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning) 0%, #ea580c 100%);
            color: var(--white);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
            color: var(--white);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--white);
            padding: 24px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
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
            margin-bottom: 16px;
        }

        .stat-icon.primary { background: linear-gradient(135deg, var(--primary), var(--secondary)); }
        .stat-icon.success { background: linear-gradient(135deg, var(--success), #16a34a); }
        .stat-icon.warning { background: linear-gradient(135deg, var(--warning), #ea580c); }
        .stat-icon.accent { background: linear-gradient(135deg, var(--accent), #0891b2); }

        .stat-number {
            font-size: 32px;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--gray);
            font-size: 14px;
            font-weight: 500;
        }

        /* Filter Section */
        .filter-section {
            background: var(--white);
            padding: 24px 32px;
            border-radius: var(--radius-lg);
            margin-bottom: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--dark);
        }

        .filter-input {
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 14px;
            background: var(--white);
        }

        .filter-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        /* Table Section */
        .table-container {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .table-header {
            padding: 24px 32px;
            background: var(--gray-light);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--dark);
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        th, td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        th {
            background: var(--gray-light);
            font-weight: 600;
            color: var(--dark);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background: rgba(99, 102, 241, 0.02);
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .badge-info {
            background: rgba(6, 182, 212, 0.1);
            color: var(--accent);
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .badge-secondary {
            background: rgba(100, 116, 139, 0.1);
            color: var(--gray);
        }

        .empty-state {
            text-align: center;
            padding: 60px;
            color: var(--gray);
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 16px;
            color: var(--border);
        }

        /* Mobile Cards */
        .mobile-cards {
            display: none;
        }

        .mobile-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            margin-bottom: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .mobile-card-header {
            padding: 20px;
            background: var(--gray-light);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mobile-card-title {
            font-weight: 600;
            color: var(--dark);
            font-size: 16px;
        }

        .mobile-card-id {
            background: var(--primary);
            color: var(--white);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .mobile-card-body {
            padding: 20px;
        }

        .mobile-field {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .mobile-field:last-child {
            border-bottom: none;
        }

        .mobile-field-label {
            font-weight: 500;
            color: var(--gray);
            font-size: 14px;
        }

        .mobile-field-value {
            font-weight: 500;
            color: var(--dark);
            font-size: 14px;
        }

        .mobile-actions {
            padding: 20px;
            background: var(--gray-light);
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        /* Action Buttons */
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border: none;
            border-radius: var(--radius);
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            margin: 0 2px;
            min-width: 36px;
            height: 36px;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-detail {
            background: linear-gradient(135deg, var(--accent) 0%, #0891b2 100%);
            color: var(--white);
        }

        .btn-edit {
            background: linear-gradient(135deg, var(--warning) 0%, #ea580c 100%);
            color: var(--white);
        }

        .btn-delete {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
            color: var(--white);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .main-content {
                padding: 24px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
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

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .page-header {
                padding: 20px;
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }

            .page-title {
                font-size: 24px;
            }

            .filter-section {
                padding: 20px;
            }

            .filter-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .table-header {
                padding: 20px;
                flex-direction: column;
                gap: 16px;
            }

            .table-responsive table {
                display: none;
            }

            .mobile-cards {
                display: block;
                padding: 16px;
            }
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 40px;
            color: var(--gray);
        }

        .spinner {
            display: inline-block;
            width: 24px;
            height: 24px;
            border: 3px solid var(--border);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
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
                    <a href="data_pemesanan.php" class="nav-link active">
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
                    <a href="akun_admin.php" class="nav-link">
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
                <h1 class="page-title">Data Pemesanan</h1>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-number" id="totalPemesanan">0</div>
                    <div class="stat-label">Total Pemesanan</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number" id="confirmedCount">0</div>
                    <div class="stat-label">Terkonfirmasi</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number" id="pendingCount">0</div>
                    <div class="stat-label">Menunggu</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon accent">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-number" id="totalRevenue">Rp 0</div>
                    <div class="stat-label">Total Pendapatan</div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label class="filter-label">Cari</label>
                        <input type="text" class="filter-input" id="searchInput" placeholder="Cari nama penyewa, acara, dll..." oninput="performSearch()">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Tanggal Mulai</label>
                        <input type="date" class="filter-input" id="startDate" onchange="applyFilter()">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Tanggal Selesai</label>
                        <input type="date" class="filter-input" id="endDate" onchange="applyFilter()">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Status</label>
                        <select class="filter-input" id="statusFilter" onchange="applyFilter()">
                            <option value="">Semua Status</option>
                            <option value="Belum Lunas">Belum Lunas</option>
                            <option value="Lunas">Lunas</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Aksi</label>
                        <button class="btn btn-primary" onclick="resetFilter()">
                            <i class="fas fa-refresh"></i>
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">Daftar Pemesanan</h3>
                </div>
                
                <div class="table-responsive">
                    <table id="pemesananTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Penyewa</th>
                                <th>Acara</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Total Biaya</th>
                                <th>Kebutuhan Tambahan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query data pemesanan sesuai struktur database SQL dengan JOIN
                            $query = "SELECT 
                                        p.id_pemesanan,
                                        p.tanggal_sewa,
                                        p.tanggal_selesai,
                                        p.durasi,
                                        p.total,
                                        p.metode_pembayaran,
                                        p.tanggal_pesan,
                                        p.tipe_pesanan,
                                        p.kebutuhan_tambahan,
                                        CASE 
                                            WHEN py.tipe_penyewa = 'instansi' THEN py.nama_instansi
                                            ELSE py.nama_lengkap
                                        END as nama_penyewa,
                                        py.email as email_penyewa,
                                        py.tipe_penyewa,
                                        a.nama_acara,
                                        a.harga as harga_acara,
                                        pb.status_pembayaran,
                                        pb.bukti_pembayaran,
                                        pb.tanggal_upload
                                      FROM pemesanan p
                                      LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa
                                      LEFT JOIN acara a ON p.id_acara = a.id_acara
                                      LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
                                      ORDER BY p.tanggal_pesan DESC";
                            
                            $result = mysqli_query($conn, $query);
                            
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $status_pembayaran = $row['status_pembayaran'] ?: 'Belum Lunas';
                                    $status_class = ($status_pembayaran == 'Lunas') ? 'badge-success' : 'badge-warning';
                                    $tipe_class = ($row['tipe_penyewa'] == 'instansi') ? 'badge-info' : 'badge-primary';
                                    
                                    echo "<tr>";
                                    echo "<td><strong>#{$row['id_pemesanan']}</strong></td>";
                                    echo "<td>";
                                    echo "<strong>{$row['nama_penyewa']}</strong>";
                                    echo "<br><small>{$row['email_penyewa']}</small>";
                                    echo "<br><span class='badge {$tipe_class}'>" . ucfirst($row['tipe_penyewa']) . "</span>";
                                    echo "</td>";
                                    echo "<td>";
                                    echo "<strong>{$row['nama_acara']}</strong>";
                                    echo "<br><small>{$row['durasi']} hari</small>";
                                    echo "</td>";
                                    echo "<td data-date='{$row['tanggal_sewa']}'>";
                                    echo "<strong>" . date('d M Y', strtotime($row['tanggal_sewa'])) . "</strong>";
                                    echo "</td>";
                                    echo "<td data-date='{$row['tanggal_selesai']}'>";
                                    echo "<strong>" . date('d M Y', strtotime($row['tanggal_selesai'])) . "</strong>";
                                    echo "</td>";
                                    echo "<td><strong>Rp " . number_format($row['total'], 0, ',', '.') . "</strong></td>";
                                    echo "<td>";
                                    if (!empty($row['kebutuhan_tambahan'])) {
                                        $kebutuhan = htmlspecialchars($row['kebutuhan_tambahan']);
                                        if (strlen($kebutuhan) > 50) {
                                            echo "<span title='{$kebutuhan}'>" . substr($kebutuhan, 0, 47) . "...</span>";
                                        } else {
                                            echo $kebutuhan;
                                        }
                                    } else {
                                        echo "<span style='color: #999; font-style: italic;'>Tidak ada</span>";
                                    }
                                    echo "</td>";
                                    echo "<td><span class='badge {$status_class}'>{$status_pembayaran}</span></td>";
                                    echo "<td>";
                                    echo "<button class='btn-action btn-detail' onclick='viewDetail({$row['id_pemesanan']})' title='Detail'>";
                                    echo "<i class='fas fa-eye'></i>";
                                    echo "</button>";
                                    echo "<button class='btn-action btn-edit' onclick='editPemesanan({$row['id_pemesanan']})' title='Edit Pemesanan'>";
                                    echo "<i class='fas fa-edit'></i>";
                                    echo "</button>";
                                    echo "<button class='btn-action btn-print' onclick='cetakNota({$row['id_pemesanan']})' title='Cetak Nota'>";
                                    echo "<i class='fas fa-print'></i>";
                                    echo "</button>";
                                    echo "<button class='btn-action btn-delete' onclick='deletePemesanan({$row['id_pemesanan']})' title='Hapus'>";
                                    echo "<i class='fas fa-trash'></i>";
                                    echo "</button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr>";
                                echo "<td colspan='9' class='empty-state'>";
                                echo "<div class='empty-icon'><i class='fas fa-calendar-times'></i></div>";
                                echo "<div class='empty-text'>Belum ada data pemesanan</div>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="mobile-cards" id="mobileCards">
                    <div class="loading">
                        <div class="spinner"></div>
                        <p>Memuat data...</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let allData = [];
        let filteredData = [];

        // Load data on page ready
        document.addEventListener('DOMContentLoaded', function() {
            loadData();
            loadStats();
        });

        function loadData() {
            // For now, use the PHP data directly instead of AJAX
            const tableRows = document.querySelectorAll('#pemesananTable tbody tr');
            allData = [];
            
            tableRows.forEach(row => {
                const cells = row.cells;
                if (cells.length > 1) {
                    const idMatch = cells[0].textContent.match(/#(\d+)/);
                    const id = idMatch ? idMatch[1] : '';
                    
                    // Extract raw date values for proper filtering
                    const tanggalSewaRaw = cells[3].getAttribute('data-date') || cells[3].textContent;
                    const tanggalSelesaiRaw = cells[4].getAttribute('data-date') || cells[4].textContent;
                    
                    allData.push({
                        id_pemesanan: id,
                        nama_penyewa: cells[1].querySelector('strong')?.textContent || '',
                        email_penyewa: cells[1].querySelector('small')?.textContent || '',
                        tipe_penyewa: cells[1].querySelector('.badge')?.textContent || '',
                        nama_acara: cells[2].querySelector('strong')?.textContent || '',
                        durasi: cells[2].querySelector('small')?.textContent || '',
                        tanggal_sewa: cells[3].querySelector('strong')?.textContent || '',
                        tanggal_sewa_raw: tanggalSewaRaw,
                        tanggal_selesai: cells[4].querySelector('strong')?.textContent || '',
                        tanggal_selesai_raw: tanggalSelesaiRaw,
                        total: cells[5].textContent.replace(/[^\d]/g, ''),
                        status_pembayaran: cells[6].querySelector('.badge')?.textContent || ''
                    });
                }
            });
            
            filteredData = allData;
            updateMobileCards();
        }

        function editPemesanan(id) {
            window.open(`pemesanan_edit.php?id=${id}`, '_blank');
        }

        function loadStats() {
            // Calculate stats from PHP data
            const rows = document.querySelectorAll('#pemesananTable tbody tr');
            let total = 0;
            let confirmed = 0;
            let pending = 0;
            let revenue = 0;
            
            rows.forEach(row => {
                const cells = row.cells;
                if (cells.length > 1) {
                    total++;
                    
                    const status = cells[6].querySelector('.badge')?.textContent || '';
                    if (status === 'Lunas') {
                        confirmed++;
                        // Extract revenue
                        const totalText = cells[5].textContent;
                        const amount = parseInt(totalText.replace(/[^\d]/g, '')) || 0;
                        revenue += amount;
                    } else {
                        pending++;
                    }
                }
            });
            
            document.getElementById('totalPemesanan').textContent = total;
            document.getElementById('confirmedCount').textContent = confirmed;
            document.getElementById('pendingCount').textContent = pending;
            document.getElementById('totalRevenue').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(revenue);
        }

        function updateMobileCards() {
            const mobileContainer = document.querySelector('#mobileCards');
            
            if (filteredData.length === 0) {
                mobileContainer.innerHTML = `<div class="empty-state">
                    <i class="fas fa-calendar-alt"></i><br>
                    Belum ada data pemesanan
                </div>`;
                return;
            }
            
            let mobileHTML = '';
            filteredData.forEach(pemesanan => {
                const statusClass = getStatusClass(pemesanan.status_pembayaran);
                const formattedTotal = new Intl.NumberFormat('id-ID', { 
                    style: 'currency', 
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(parseInt(pemesanan.total) || 0);
                
                mobileHTML += `
                    <div class="mobile-card">
                        <div class="mobile-card-header">
                            <div class="mobile-card-title">${pemesanan.nama_acara || 'N/A'}</div>
                            <div class="mobile-card-id">#${pemesanan.id_pemesanan}</div>
                        </div>
                        <div class="mobile-card-body">
                            <div class="mobile-field">
                                <span class="mobile-field-label">Penyewa:</span>
                                <span class="mobile-field-value">${pemesanan.nama_penyewa || 'N/A'}</span>
                            </div>
                            <div class="mobile-field">
                                <span class="mobile-field-label">Email:</span>
                                <span class="mobile-field-value">${pemesanan.email_penyewa || 'N/A'}</span>
                            </div>
                            <div class="mobile-field">
                                <span class="mobile-field-label">Tipe:</span>
                                <span class="mobile-field-value">${pemesanan.tipe_penyewa || 'N/A'}</span>
                            </div>
                            <div class="mobile-field">
                                <span class="mobile-field-label">Tanggal:</span>
                                <span class="mobile-field-value">${pemesanan.tanggal_sewa || 'N/A'}</span>
                            </div>
                            <div class="mobile-field">
                                <span class="mobile-field-label">Total:</span>
                                <span class="mobile-field-value"><strong>${formattedTotal}</strong></span>
                            </div>
                            <div class="mobile-field">
                                <span class="mobile-field-label">Kebutuhan Tambahan:</span>
                                <span class="mobile-field-value">${pemesanan.kebutuhan_tambahan ? (pemesanan.kebutuhan_tambahan.length > 50 ? pemesanan.kebutuhan_tambahan.substring(0, 50) + '...' : pemesanan.kebutuhan_tambahan) : 'Tidak ada'}</span>
                            </div>
                            <div class="mobile-field">
                                <span class="mobile-field-label">Status:</span>
                                <span class="badge ${statusClass}">
                                    ${pemesanan.status_pembayaran || 'Pending'}
                                </span>
                            </div>
                        </div>
                        <div class="mobile-actions">
                            <button class="btn-action btn-detail" onclick="viewDetail(${pemesanan.id_pemesanan})" title="Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn-action btn-edit" onclick="editPemesanan(${pemesanan.id_pemesanan})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action btn-print" onclick="cetakNota(${pemesanan.id_pemesanan})" title="Cetak">
                                <i class="fas fa-print"></i>
                            </button>
                            <button class="btn-action btn-delete" onclick="deletePemesanan(${pemesanan.id_pemesanan})" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            mobileContainer.innerHTML = mobileHTML;
        }

        function getStatusClass(status) {
            const statusClasses = {
                'Lunas': 'badge-success',
                'Belum Lunas': 'badge-warning',
                'Pending': 'badge-secondary',
                'Confirmed': 'badge-info',
                'Completed': 'badge-success',
                'Cancelled': 'badge-danger'
            };
            return statusClasses[status] || 'badge-secondary';
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function displayError(message = 'Error loading data') {
            const tbody = document.querySelector('#pemesananTable tbody');
            const mobileContainer = document.querySelector('#mobileCards');
            
            const errorHTML = `
                <tr><td colspan="7" class="empty-state">
                    <i class="fas fa-exclamation-triangle"></i><br>
                    ${message}
                </td></tr>
            `;
            
            tbody.innerHTML = errorHTML;
            mobileContainer.innerHTML = `<div class="empty-state">
                <i class="fas fa-exclamation-triangle"></i><br>
                ${message}
            </div>`;
        }

        function displayEmpty() {
            const tbody = document.querySelector('#pemesananTable tbody');
            const mobileContainer = document.querySelector('#mobileCards');
            
            const emptyHTML = `
                <tr><td colspan="7" class="empty-state">
                    <i class="fas fa-calendar-alt"></i><br>
                    Belum ada data pemesanan
                </td></tr>
            `;
            
            tbody.innerHTML = emptyHTML;
            mobileContainer.innerHTML = `<div class="empty-state">
                <i class="fas fa-calendar-alt"></i><br>
                Belum ada data pemesanan
            </div>`;
        }

        function applyFilter() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const status = document.getElementById('statusFilter').value;
            
            filteredData = allData.filter(item => {
                let searchMatch = true;
                let dateMatch = true;
                let statusMatch = true;
                
                // Search filter
                if (searchInput) {
                    searchMatch = (
                        item.nama_penyewa?.toLowerCase().includes(searchInput) ||
                        item.nama_acara?.toLowerCase().includes(searchInput) ||
                        item.email_penyewa?.toLowerCase().includes(searchInput) ||
                        item.id_pemesanan?.toString().includes(searchInput)
                    );
                }
                
                // Date filter
                if (startDate && item.tanggal_sewa) {
                    const itemDate = new Date(item.tanggal_sewa_raw || item.tanggal_sewa);
                    const filterDate = new Date(startDate);
                    dateMatch = itemDate >= filterDate;
                }
                
                if (endDate && dateMatch && item.tanggal_selesai) {
                    const itemDate = new Date(item.tanggal_selesai_raw || item.tanggal_selesai);
                    const filterDate = new Date(endDate);
                    dateMatch = itemDate <= filterDate;
                }
                
                // Status filter
                if (status) {
                    statusMatch = item.status_pembayaran === status;
                }
                
                return searchMatch && dateMatch && statusMatch;
            });
            
            updateTableView();
            updateMobileCards();
        }

        function resetFilter() {
            document.getElementById('searchInput').value = '';
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            document.getElementById('statusFilter').value = '';
            
            filteredData = allData;
            updateTableView();
            updateMobileCards();
        }

        function performSearch() {
            setTimeout(applyFilter, 300); // Debounce search
        }

        function updateTableView() {
            const tbody = document.querySelector('#pemesananTable tbody');
            
            if (filteredData.length === 0) {
                tbody.innerHTML = `
                    <tr><td colspan="8" class="empty-state">
                        <i class="fas fa-calendar-alt"></i><br>
                        Tidak ada data yang sesuai dengan filter
                    </td></tr>
                `;
                return;
            }
            
            let tableHTML = '';
            filteredData.forEach(item => {
                const statusClass = getStatusClass(item.status_pembayaran);
                const tipeClass = (item.tipe_penyewa === 'instansi') ? 'badge-info' : 'badge-primary';
                const formattedTotal = new Intl.NumberFormat('id-ID', { 
                    style: 'currency', 
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(parseInt(item.total) || 0);
                
                tableHTML += `
                    <tr>
                        <td><strong>#${item.id_pemesanan}</strong></td>
                        <td>
                            <strong>${item.nama_penyewa}</strong><br>
                            <small>${item.email_penyewa}</small><br>
                            <span class="badge ${tipeClass}">${item.tipe_penyewa}</span>
                        </td>
                        <td>
                            <strong>${item.nama_acara}</strong><br>
                            <small>${item.durasi}</small>
                        </td>
                        <td><strong>${item.tanggal_sewa}</strong></td>
                        <td><strong>${item.tanggal_selesai}</strong></td>
                        <td><strong>${formattedTotal}</strong></td>
                        <td>${item.kebutuhan_tambahan ? (item.kebutuhan_tambahan.length > 30 ? item.kebutuhan_tambahan.substring(0, 30) + '...' : item.kebutuhan_tambahan) : 'Tidak ada'}</td>
                        <td><span class="badge ${statusClass}">${item.status_pembayaran}</span></td>
                        <td>
                            <button class="btn-action btn-detail" onclick="viewDetail(${item.id_pemesanan})" title="Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn-action btn-edit" onclick="editPemesanan(${item.id_pemesanan})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action btn-print" onclick="cetakNota(${item.id_pemesanan})" title="Cetak">
                                <i class="fas fa-print"></i>
                            </button>
                            <button class="btn-action btn-delete" onclick="deletePemesanan(${item.id_pemesanan})" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = tableHTML;
        }

        function viewDetail(id) {
            window.open(`pemesanan_view.php?id=${id}`, '_blank');
        }

        function cetakNota(id) {
            window.open(`../cetak_nota.php?id=${id}`, '_blank');
        }

        function deletePemesanan(id) {
            if (confirm('Apakah Anda yakin ingin menghapus pemesanan ini?')) {
                fetch('ajax/delete_pemesanan.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pemesanan berhasil dihapus');
                        loadData();
                        loadStats();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus data');
                });
            }
        }

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

        // Load data when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadData();
            loadStats();
        });
    </script>
</body>
</html>
