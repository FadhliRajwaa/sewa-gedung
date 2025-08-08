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
    <title>Riwayat Pemesanan - Admin</title>
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
            align-items: center;
            justify-content: center;
        }

        .mobile-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            backdrop-filter: blur(4px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .mobile-overlay.active {
            opacity: 1;
        }
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

        .table-actions {
            display: flex;
            gap: 12px;
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

        .btn-secondary {
            background: var(--gray-light);
            color: var(--gray);
            border: 1px solid var(--border);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
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

        .badge-secondary {
            background: rgba(100, 116, 139, 0.1);
            color: var(--gray);
        }

        /* Mobile Cards */
        .mobile-cards {
            display: none;
            padding: 0;
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
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: var(--white);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mobile-card-title {
            font-weight: 600;
            font-size: 16px;
            margin: 0;
        }

        .mobile-card-id {
            background: rgba(255, 255, 255, 0.2);
            color: var(--white);
            padding: 6px 12px;
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
            padding-bottom: 0;
        }

        .mobile-field-label {
            font-weight: 500;
            color: var(--gray);
            font-size: 14px;
            flex: 1;
        }

        .mobile-field-value {
            font-weight: 500;
            color: var(--dark);
            font-size: 14px;
            text-align: right;
            flex: 1;
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray);
        }

        .loading i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
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

        /* Responsive */
        @media (max-width: 1024px) {
            .main-content {
                padding: 24px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .table-actions {
                width: 100%;
                justify-content: flex-start;
            }
        }

        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: flex !important;
                align-items: center;
                justify-content: center;
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

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .mobile-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                backdrop-filter: blur(4px);
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
                padding: 20px 16px;
                margin-bottom: 20px;
            }

            .page-title {
                font-size: 24px;
            }

            .page-subtitle {
                font-size: 14px;
            }

            .table-container {
                margin-bottom: 20px;
            }

            .table-header {
                padding: 16px;
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .table-title {
                font-size: 18px;
            }

            .table-actions {
                width: 100%;
                justify-content: flex-start;
                flex-wrap: wrap;
                gap: 8px;
            }

            .btn {
                font-size: 12px;
                padding: 10px 16px;
                flex: 1;
                min-width: 120px;
                justify-content: center;
            }

            /* Hide desktop table on mobile */
            .table-responsive table {
                display: none !important;
            }

            /* Show mobile cards on mobile */
            .mobile-cards {
                display: block !important;
                padding: 16px 0;
            }

            .stat-card {
                padding: 20px;
                text-align: center;
            }

            .stat-number {
                font-size: 28px;
            }

            .stat-label {
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 12px;
                padding-top: 80px;
            }

            .page-header {
                padding: 16px 12px;
            }

            .page-title {
                font-size: 20px;
            }

            .stats-grid {
                gap: 12px;
            }

            .stat-card {
                padding: 16px;
            }

            .stat-number {
                font-size: 24px;
            }

            .mobile-cards {
                padding: 12px 0;
            }

            .mobile-card {
                margin-bottom: 12px;
            }

            .mobile-card-header {
                padding: 16px;
            }

            .mobile-card-body {
                padding: 16px;
            }

            .mobile-field {
                padding: 10px 0;
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .mobile-field-value {
                text-align: left;
            }

            .table-header {
                padding: 12px;
            }

            .table-title {
                font-size: 16px;
            }

            .btn {
                font-size: 11px;
                padding: 8px 12px;
                min-width: 100px;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" id="mobileMenuToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Mobile Overlay -->
        <div class="mobile-overlay" id="mobileOverlay"></div>

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
                    <a href="riwayat_pemesanan.php" class="nav-link active">
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
                <h1 class="page-title">Riwayat Pemesanan</h1>
                <p class="page-subtitle">Histori lengkap semua pemesanan gedung</p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="stat-number" id="totalRiwayat"><?php
                        try {
                            $total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM pemesanan");
                            echo $total_query ? mysqli_fetch_assoc($total_query)['total'] : '0';
                        } catch (Exception $e) {
                            echo '0';
                        }
                    ?></div>
                    <div class="stat-label">Total Riwayat</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-calendar-month"></i>
                    </div>
                    <div class="stat-number" id="bulanIni"><?php
                        try {
                            $tables_check = mysqli_query($conn, "SHOW TABLES LIKE 'pemesanan'");
                            if (mysqli_num_rows($tables_check) > 0) {
                                $columns_check = mysqli_query($conn, "SHOW COLUMNS FROM pemesanan");
                                $columns = [];
                                while ($col = mysqli_fetch_assoc($columns_check)) {
                                    $columns[] = $col['Field'];
                                }
                                $date_column = in_array('tanggal_acara', $columns) ? 'tanggal_acara' : 'tanggal_sewa';
                                $bulan_query = mysqli_query($conn, "SELECT COUNT(*) as bulan FROM pemesanan WHERE MONTH($date_column) = MONTH(CURDATE()) AND YEAR($date_column) = YEAR(CURDATE())");
                                echo $bulan_query ? mysqli_fetch_assoc($bulan_query)['bulan'] : '0';
                            } else {
                                echo '0';
                            }
                        } catch (Exception $e) {
                            echo '0';
                        }
                    ?></div>
                    <div class="stat-label">Bulan Ini</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-number" id="totalPendapatan"><?php
                        try {
                            $tables_check = mysqli_query($conn, "SHOW TABLES LIKE 'pemesanan'");
                            if (mysqli_num_rows($tables_check) > 0) {
                                $columns_check = mysqli_query($conn, "SHOW COLUMNS FROM pemesanan");
                                $columns = [];
                                while ($col = mysqli_fetch_assoc($columns_check)) {
                                    $columns[] = $col['Field'];
                                }
                                $cost_column = in_array('total_biaya', $columns) ? 'total_biaya' : 'total';
                                $pendapatan_query = mysqli_query($conn, "SELECT SUM($cost_column) as pendapatan FROM pemesanan");
                                $pendapatan = $pendapatan_query ? (mysqli_fetch_assoc($pendapatan_query)['pendapatan'] ?: 0) : 0;
                                echo 'Rp ' . number_format($pendapatan, 0, ',', '.');
                            } else {
                                echo 'Rp 0';
                            }
                        } catch (Exception $e) {
                            echo 'Rp 0';
                        }
                    ?></div>
                    <div class="stat-label">Total Pendapatan</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon accent">
                        <i class="fas fa-chart-simple"></i>
                    </div>
                    <div class="stat-number" id="rataRata">Rp 0</div>
                    <div class="stat-label">Rata-rata Transaksi</div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section" style="background: var(--white); padding: 24px 32px; border-radius: var(--radius-lg); margin-bottom: 24px; box-shadow: var(--shadow); border: 1px solid var(--border);">
                <div class="filter-header" style="margin-bottom: 20px;">
                    <h3 style="color: var(--dark); font-size: 18px; font-weight: 600;">Filter & Pencarian</h3>
                </div>
                <div class="filter-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: end;">
                    <div class="filter-group" style="display: flex; flex-direction: column; gap: 8px;">
                        <label class="filter-label" style="font-size: 14px; font-weight: 600; color: var(--dark);">Cari</label>
                        <input type="text" class="filter-input" id="searchInput" placeholder="Cari nama penyewa, acara, ID..." style="padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius); font-size: 14px; background: var(--white);">
                    </div>
                    <div class="filter-group" style="display: flex; flex-direction: column; gap: 8px;">
                        <label class="filter-label" style="font-size: 14px; font-weight: 600; color: var(--dark);">Tanggal Mulai</label>
                        <input type="date" class="filter-input" id="startDate" style="padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius); font-size: 14px; background: var(--white);">
                    </div>
                    <div class="filter-group" style="display: flex; flex-direction: column; gap: 8px;">
                        <label class="filter-label" style="font-size: 14px; font-weight: 600; color: var(--dark);">Tanggal Acara</label>
                        <input type="date" class="filter-input" id="eventDate" style="padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius); font-size: 14px; background: var(--white);">
                    </div>
                    <div class="filter-group" style="display: flex; flex-direction: column; gap: 8px;">
                        <label class="filter-label" style="font-size: 14px; font-weight: 600; color: var(--dark);">Tipe Penyewa</label>
                        <select class="filter-input" id="tipePenyewaFilter" style="padding: 12px 16px; border: 1px solid var(--border); border-radius: var(--radius); font-size: 14px; background: var(--white);">
                            <option value="">Semua Tipe</option>
                            <option value="umum">Umum</option>
                            <option value="instansi">Instansi</option>
                        </select>
                    </div>
                    <div class="filter-group" style="display: flex; flex-direction: column; gap: 8px;">
                        <label class="filter-label" style="font-size: 14px; font-weight: 600; color: var(--dark);">Aksi</label>
                        <button class="btn btn-primary" onclick="applyFilter()" style="padding: 12px 24px; border: none; border-radius: var(--radius); font-weight: 600; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: var(--white); cursor: pointer;">
                            <i class="fas fa-filter"></i>
                            Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">Riwayat Lengkap Pemesanan</h3>
                    <div class="table-actions">
                        <button class="btn btn-secondary" onclick="exportData()">
                            <i class="fas fa-download"></i>
                            Export
                        </button>
                        <button class="btn btn-primary" onclick="refreshData()">
                            <i class="fas fa-refresh"></i>
                            Refresh
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table id="riwayatTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Penyewa</th>
                                <th>ID Pemesanan</th>
                                <th>Tipe Penyewa</th>
                                <th>Nama Penyewa</th>
                                <th>Acara</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Total Biaya</th>
                                <th>Metode Pembayaran</th>
                                <th>Kebutuhan Tambahan</th>
                                <th>Status</th>
                                <th>Tanggal Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                // Enhanced query with proper column mapping and joins
                                $query = "SELECT 
                                            p.id_pemesanan,
                                            p.id_penyewa,
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
                                $no = 1;
                                
                                if ($result && mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $status_pembayaran = $row['status_pembayaran'] ?: 'Belum Lunas';
                                        $status_class = ($status_pembayaran == 'Lunas') ? 'badge-success' : 'badge-warning';
                                        $tipe_class = ($row['tipe_penyewa'] == 'instansi') ? 'badge-info' : 'badge-primary';
                                        $metode_pembayaran = $row['metode_pembayaran'] ?: 'Transfer Bank';
                                        $kebutuhan_tambahan = $row['kebutuhan_tambahan'] ?: '-';
                                        
                                        echo "<tr data-tipe='{$row['tipe_penyewa']}' class='filterable-row'>";
                                        echo "<td><strong>{$no}</strong></td>";
                                        echo "<td><strong>#{$row['id_penyewa']}</strong></td>";
                                        echo "<td><strong>#{$row['id_pemesanan']}</strong></td>";
                                        echo "<td><span class='badge {$tipe_class}'>" . ucfirst($row['tipe_penyewa']) . "</span></td>";
                                        echo "<td>";
                                        echo "<strong>{$row['nama_penyewa']}</strong>";
                                        echo "<br><small style='color: var(--gray);'>{$row['email_penyewa']}</small>";
                                        echo "</td>";
                                        echo "<td>";
                                        echo "<strong>{$row['nama_acara']}</strong>";
                                        echo "<br><small style='color: var(--gray);'>{$row['durasi']} hari</small>";
                                        echo "</td>";
                                        echo "<td>";
                                        echo "<strong>" . date('d M Y', strtotime($row['tanggal_sewa'])) . "</strong>";
                                        echo "</td>";
                                        echo "<td>";
                                        echo "<strong>" . date('d M Y', strtotime($row['tanggal_selesai'])) . "</strong>";
                                        echo "</td>";
                                        echo "<td><strong>Rp " . number_format($row['total'], 0, ',', '.') . "</strong></td>";
                                        echo "<td><span class='badge badge-secondary'>{$metode_pembayaran}</span></td>";
                                        echo "<td>" . (strlen($kebutuhan_tambahan) > 30 ? substr($kebutuhan_tambahan, 0, 30) . '...' : $kebutuhan_tambahan) . "</td>";
                                        echo "<td><span class='badge {$status_class}'>{$status_pembayaran}</span></td>";
                                        echo "<td>";
                                        echo "<strong>" . date('d M Y H:i', strtotime($row['tanggal_pesan'])) . "</strong>";
                                        echo "</td>";
                                        echo "</tr>";
                                        $no++;
                                    }
                                } else {
                                    echo "<tr>";
                                    echo "<td colspan='13' class='empty-state' style='text-align: center; padding: 60px; color: var(--gray);'>";
                                    echo "<div style='font-size: 64px; margin-bottom: 16px; color: var(--border);'><i class='fas fa-history'></i></div>";
                                    echo "<div>Belum ada riwayat pemesanan</div>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } catch (Exception $e) {
                                echo "<tr>";
                                echo "<td colspan='13' class='empty-state' style='text-align: center; padding: 60px; color: red;'>";
                                echo "<div style='font-size: 64px; margin-bottom: 16px;'><i class='fas fa-exclamation-triangle'></i></div>";
                                echo "<div>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                                    $columns = [];
                                    while ($col = mysqli_fetch_assoc($columns_check)) {
                                        $columns[] = $col['Field'];
                                    }
                                    
                                    // Build dynamic query based on available columns
                                    $select_fields = [];
                                    $select_fields[] = in_array('id', $columns) ? 'p.id' : 'p.id_pemesanan as id';
                                    $select_fields[] = "'Event' as nama_acara"; // Default value
                                    $select_fields[] = in_array('tanggal_acara', $columns) ? 'p.tanggal_acara' : 'p.tanggal_sewa as tanggal_acara';
                                    $select_fields[] = in_array('total_biaya', $columns) ? 'p.total_biaya' : 'p.total as total_biaya';
                                    $select_fields[] = "'Customer' as nama_penyewa"; // Default value
                                    $select_fields[] = "'customer@email.com' as email"; // Default value
                                    $select_fields[] = "'Belum Lunas' as status_pembayaran"; // Default value
                                    
                                    $query = "SELECT " . implode(', ', $select_fields) . " FROM pemesanan p ";
                                    
                                    // Check if penyewa table exists and join
                                    $penyewa_check = mysqli_query($conn, "SHOW TABLES LIKE 'penyewa'");
                                    if (mysqli_num_rows($penyewa_check) > 0) {
                                        $penyewa_columns_check = mysqli_query($conn, "SHOW COLUMNS FROM penyewa");
                                        $penyewa_columns = [];
                                        while ($col = mysqli_fetch_assoc($penyewa_columns_check)) {
                                            $penyewa_columns[] = $col['Field'];
                                        }
                                        
                                        if (in_array('id_penyewa', $columns)) {
                                            $query .= "LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa ";
                                            // Replace defaults with actual data
                                            $name_field = in_array('nama_lengkap', $penyewa_columns) ? 'py.nama_lengkap' : 
                                                         (in_array('nama_instansi', $penyewa_columns) ? 'py.nama_instansi' : "'Customer'");
                                            $email_field = in_array('email', $penyewa_columns) ? 'py.email' : "'customer@email.com'";
                                            
                                            $select_fields[4] = "COALESCE($name_field, 'Customer') as nama_penyewa";
                                            $select_fields[5] = "COALESCE($email_field, 'customer@email.com') as email";
                                            $query = "SELECT " . implode(', ', $select_fields) . " FROM pemesanan p LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa ";
                                        }
                                    }
                                    
                                    // Check if acara table exists for event names
                                    $acara_check = mysqli_query($conn, "SHOW TABLES LIKE 'acara'");
                                    if (mysqli_num_rows($acara_check) > 0 && in_array('id_acara', $columns)) {
                                        $query .= "LEFT JOIN acara a ON p.id_acara = a.id_acara ";
                                        $select_fields[1] = "COALESCE(a.nama_acara, 'Event') as nama_acara";
                                        // Rebuild query with acara join
                                        $from_pos = strpos($query, "FROM");
                                        $query = "SELECT " . implode(', ', $select_fields) . " " . substr($query, $from_pos);
                                    }
                                    
                                    // Check if pembayaran table exists for payment status
                                    $pembayaran_check = mysqli_query($conn, "SHOW TABLES LIKE 'pembayaran'");
                                    if (mysqli_num_rows($pembayaran_check) > 0) {
                                        $query .= "LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan ";
                                        $select_fields[6] = "COALESCE(pb.status_pembayaran, 'Belum Lunas') as status_pembayaran";
                                        // Rebuild query with pembayaran join
                                        $from_pos = strpos($query, "FROM");
                                        $query = "SELECT " . implode(', ', $select_fields) . " " . substr($query, $from_pos);
                                    }
                                    
                                    $query .= "ORDER BY ";
                                    if (in_array('tanggal_acara', $columns)) {
                                        $query .= 'p.tanggal_acara DESC';
                                    } else if (in_array('id', $columns)) {
                                        $query .= 'p.id DESC';
                                    } else {
                                        $query .= 'p.id_pemesanan DESC';
                                    }
                                    $query .= " LIMIT 50";
                                    
                                    $result = mysqli_query($conn, $query);
                                    
                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $status_class = 'badge-info';
                                            switch (strtolower($row['status_pembayaran'])) {
                                                case 'lunas':
                                                    $status_class = 'badge-success';
                                                    break;
                                                case 'belum lunas':
                                                    $status_class = 'badge-warning';
                                                    break;
                                                case 'pending':
                                                    $status_class = 'badge-secondary';
                                                    break;
                                            }
                                            
                                            echo "<tr>";
                                            echo "<td><strong>#" . htmlspecialchars($row['id']) . "</strong></td>";
                                            echo "<td>" . date('d/m/Y', strtotime($row['tanggal_acara'])) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nama_acara']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nama_penyewa']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                            echo "<td><strong>Rp " . number_format($row['total_biaya'], 0, ',', '.') . "</strong></td>";
                                            echo "<td><span class='badge {$status_class}'>" . htmlspecialchars($row['status_pembayaran']) . "</span></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='loading'><i class='fas fa-history'></i><br>Belum ada riwayat pemesanan</td></tr>";
                                    }
                                } else {
                                    // Sample data if table doesn't exist
                                    $sample_data = [
                                        [1, 'Pernikahan', '2024-12-25', 'Budi Santoso', 'budi@email.com', 5000000, 'Lunas'],
                                        [2, 'Seminar', '2024-12-20', 'PT. Teknologi', 'info@teknologi.com', 3000000, 'Belum Lunas'],
                                        [3, 'Rapat', '2024-12-15', 'CV. Maju', 'cv@maju.com', 2500000, 'Lunas']
                                    ];
                                    
                                    foreach ($sample_data as $data) {
                                        $status_class = $data[6] == 'Lunas' ? 'badge-success' : 'badge-warning';
                                        echo "<tr>";
                                        echo "<td><strong>#{$data[0]}</strong></td>";
                                        echo "<td>" . date('d/m/Y', strtotime($data[2])) . "</td>";
                                        echo "<td>{$data[1]}</td>";
                                        echo "<td>{$data[3]}</td>";
                                        echo "<td>{$data[4]}</td>";
                                        echo "<td><strong>Rp " . number_format($data[5], 0, ',', '.') . "</strong></td>";
                                        echo "<td><span class='badge {$status_class}'>{$data[6]}</span></td>";
                                        echo "</tr>";
                                    }
                                }
                            } catch (Exception $e) {
                                echo "<tr><td colspan='7' class='loading' style='color: red;'><i class='fas fa-exclamation-triangle'></i><br>Error loading data: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards (populated with server-side data) -->
                <div class="mobile-cards" id="mobileCards">
                    <?php
                    // Generate mobile cards for the same data
                    if (isset($result) && $result && mysqli_num_rows($result) > 0) {
                        mysqli_data_seek($result, 0); // Reset pointer to beginning
                        while ($row = mysqli_fetch_assoc($result)) {
                            $status_class = 'badge-info';
                            switch (strtolower($row['status_pembayaran'])) {
                                case 'lunas':
                                    $status_class = 'badge-success';
                                    break;
                                case 'belum lunas':
                                    $status_class = 'badge-warning';
                                    break;
                                case 'pending':
                                    $status_class = 'badge-secondary';
                                    break;
                            }
                            
                            echo '<div class="mobile-card">';
                            echo '<div class="mobile-card-header">';
                            echo '<div class="mobile-card-title">' . htmlspecialchars($row['nama_acara']) . '</div>';
                            echo '<div class="mobile-card-id">#' . htmlspecialchars($row['id']) . '</div>';
                            echo '</div>';
                            echo '<div class="mobile-card-body">';
                            echo '<div class="mobile-field">';
                            echo '<span class="mobile-field-label">Tanggal:</span>';
                            echo '<span class="mobile-field-value">' . date('d/m/Y', strtotime($row['tanggal_acara'])) . '</span>';
                            echo '</div>';
                            echo '<div class="mobile-field">';
                            echo '<span class="mobile-field-label">Penyewa:</span>';
                            echo '<span class="mobile-field-value">' . htmlspecialchars($row['nama_penyewa']) . '</span>';
                            echo '</div>';
                            echo '<div class="mobile-field">';
                            echo '<span class="mobile-field-label">Email:</span>';
                            echo '<span class="mobile-field-value">' . htmlspecialchars($row['email']) . '</span>';
                            echo '</div>';
                            echo '<div class="mobile-field">';
                            echo '<span class="mobile-field-label">Total:</span>';
                            echo '<span class="mobile-field-value"><strong>Rp ' . number_format($row['total_biaya'], 0, ',', '.') . '</strong></span>';
                            echo '</div>';
                            echo '<div class="mobile-field">';
                            echo '<span class="mobile-field-label">Status:</span>';
                            echo '<span class="badge ' . $status_class . '">' . htmlspecialchars($row['status_pembayaran']) . '</span>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } elseif (isset($sample_data)) {
                        // Show sample data for mobile as well
                        foreach ($sample_data as $data) {
                            $status_class = $data[6] == 'Lunas' ? 'badge-success' : 'badge-warning';
                            echo '<div class="mobile-card">';
                            echo '<div class="mobile-card-header">';
                            echo '<div class="mobile-card-title">' . $data[1] . '</div>';
                            echo '<div class="mobile-card-id">#' . $data[0] . '</div>';
                            echo '</div>';
                            echo '<div class="mobile-card-body">';
                            echo '<div class="mobile-field">';
                            echo '<span class="mobile-field-label">Tanggal:</span>';
                            echo '<span class="mobile-field-value">' . date('d/m/Y', strtotime($data[2])) . '</span>';
                            echo '</div>';
                            echo '<div class="mobile-field">';
                            echo '<span class="mobile-field-label">Penyewa:</span>';
                            echo '<span class="mobile-field-value">' . $data[3] . '</span>';
                            echo '</div>';
                            echo '<div class="mobile-field">';
                            echo '<span class="mobile-field-label">Email:</span>';
                            echo '<span class="mobile-field-value">' . $data[4] . '</span>';
                            echo '</div>';
                            echo '<div class="mobile-field">';
                            echo '<span class="mobile-field-label">Total:</span>';
                            echo '<span class="mobile-field-value"><strong>Rp ' . number_format($data[5], 0, ',', '.') . '</strong></span>';
                            echo '</div>';
                            echo '<div class="mobile-field">';
                            echo '<span class="mobile-field-label">Status:</span>';
                            echo '<span class="badge ' . $status_class . '">' . $data[6] . '</span>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="loading">';
                        echo '<i class="fas fa-history"></i><br>';
                        echo 'Belum ada riwayat pemesanan';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Global variables
        let allData = [];

        // Ensure all functions are defined before DOM ready
        function loadData() {
            console.log('Data already loaded on page load');
        }

        function loadStats() {
            console.log('Stats already loaded on page load');
        }

        function refreshData() {
            location.reload();
        }

        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.mobile-overlay');
            
            if (sidebar && overlay) {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }
        }

        function closeMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.mobile-overlay');
            
            if (sidebar && overlay) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
        }

        function exportData() {
            // Simple CSV export functionality
            const headers = ['ID', 'Tanggal Acara', 'Nama Acara', 'Penyewa', 'Email', 'Total Biaya', 'Status'];
            let csvContent = headers.join(',') + '\n';
            
            // Get data from table
            const table = document.querySelector('#riwayatTable');
            if (table) {
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length > 0) {
                        const rowData = Array.from(cells).map(cell => 
                            '"' + cell.textContent.trim().replace(/"/g, '""') + '"'
                        );
                        csvContent += rowData.join(',') + '\n';
                    }
                });
            }
            
            // Download CSV
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', 'riwayat_pemesanan_' + new Date().toISOString().split('T')[0] + '.csv');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }

        // Load data on page ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM ready, initializing page...');
        });

        function loadDataFallback() {
            // Get data from server-side rendering
            <?php
            try {
                // First, let's check what columns exist
                $tables_check = mysqli_query($conn, "SHOW TABLES LIKE 'pemesanan'");
                if (mysqli_num_rows($tables_check) > 0) {
                    $columns_check = mysqli_query($conn, "SHOW COLUMNS FROM pemesanan");
                    $columns = [];
                    while ($col = mysqli_fetch_assoc($columns_check)) {
                        $columns[] = $col['Field'];
                    }
                    
                    // Build query based on available columns
                    $query = "SELECT ";
                    
                    // ID field
                    if (in_array('id', $columns)) {
                        $query .= "p.id as id, ";
                    } elseif (in_array('id_pemesanan', $columns)) {
                        $query .= "p.id_pemesanan as id, ";
                    } else {
                        $query .= "1 as id, ";
                    }
                    
                    // Event name
                    if (in_array('nama_acara', $columns)) {
                        $query .= "p.nama_acara, ";
                    } else {
                        $query .= "'Event' as nama_acara, ";
                    }
                    
                    // Event date
                    if (in_array('tanggal_acara', $columns)) {
                        $query .= "p.tanggal_acara, ";
                    } elseif (in_array('tanggal_sewa', $columns)) {
                        $query .= "p.tanggal_sewa as tanggal_acara, ";
                    } else {
                        $query .= "CURDATE() as tanggal_acara, ";
                    }
                    
                    // Total cost
                    if (in_array('total_biaya', $columns)) {
                        $query .= "p.total_biaya, ";
                    } elseif (in_array('total', $columns)) {
                        $query .= "p.total as total_biaya, ";
                    } else {
                        $query .= "0 as total_biaya, ";
                    }
                    
                    // Status
                    $query .= "'Belum Lunas' as status_pembayaran, ";
                    
                    // Customer name and email
                    $query .= "'Customer' as nama_penyewa, ";
                    $query .= "'customer@email.com' as email, ";
                    
                    // Created date
                    if (in_array('created_at', $columns)) {
                        $query .= "p.created_at ";
                    } elseif (in_array('tanggal_pesan', $columns)) {
                        $query .= "p.tanggal_pesan as created_at ";
                    } else {
                        $query .= "NOW() as created_at ";
                    }
                    
                    $query .= "FROM pemesanan p ";
                    
                    // Add JOIN if penyewa table exists
                    $penyewa_check = mysqli_query($conn, "SHOW TABLES LIKE 'penyewa'");
                    if (mysqli_num_rows($penyewa_check) > 0) {
                        if (in_array('id_penyewa', $columns)) {
                            $query .= "LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa ";
                            $query = str_replace("'Customer' as nama_penyewa,", "COALESCE(py.nama_lengkap, py.nama_instansi, 'Customer') as nama_penyewa,", $query);
                            $query = str_replace("'customer@email.com' as email,", "COALESCE(py.email, 'customer@email.com') as email,", $query);
                        }
                    }
                    
                    // Add ORDER BY
                    $query .= "ORDER BY ";
                    if (in_array('tanggal_acara', $columns)) {
                        $query .= "p.tanggal_acara DESC";
                    } elseif (in_array('tanggal_sewa', $columns)) {
                        $query .= "p.tanggal_sewa DESC";
                    } elseif (in_array('id', $columns)) {
                        $query .= "p.id DESC";
                    } else {
                        $query .= "p.id_pemesanan DESC";
                    }
                    
                    $query .= " LIMIT 50";
                    
                    $result = mysqli_query($conn, $query);
                    $riwayat = [];
                    
                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $riwayat[] = [
                                'id' => $row['id'],
                                'nama_acara' => $row['nama_acara'] ?: 'Event',
                                'tanggal_acara' => $row['tanggal_acara'],
                                'total_biaya' => floatval($row['total_biaya'] ?: 0),
                                'status_pembayaran' => $row['status_pembayaran'] ?: 'Belum Lunas',
                                'nama_penyewa' => $row['nama_penyewa'] ?: 'Customer',
                                'email' => $row['email'] ?: 'N/A',
                                'created_at' => $row['created_at']
                            ];
                        }
                    }
                } else {
                    // No pemesanan table found, create sample data
                    $riwayat = [
                        [
                            'id' => 1,
                            'nama_acara' => 'Sample Event',
                            'tanggal_acara' => date('Y-m-d'),
                            'total_biaya' => 1000000,
                            'status_pembayaran' => 'Lunas',
                            'nama_penyewa' => 'Sample Customer',
                            'email' => 'sample@email.com',
                            'created_at' => date('Y-m-d H:i:s')
                        ]
                    ];
                }
                
                echo "displayData(" . json_encode($riwayat) . ");";
            } catch (Exception $e) {
                echo "console.error('Database error: " . addslashes($e->getMessage()) . "');";
                echo "displayError('Error loading data: Database connection issue');";
            }
            ?>
        }

        function loadStats() {
            console.log('Loading stats...');
            
            // Load stats directly from server-side
            loadStatsFallback();
        }

        function loadStatsFallback() {
            <?php
            try {
                // Check if pemesanan table exists
                $tables_check = mysqli_query($conn, "SHOW TABLES LIKE 'pemesanan'");
                
                if (mysqli_num_rows($tables_check) > 0) {
                    // Get column info
                    $columns_check = mysqli_query($conn, "SHOW COLUMNS FROM pemesanan");
                    $columns = [];
                    while ($col = mysqli_fetch_assoc($columns_check)) {
                        $columns[] = $col['Field'];
                    }
                    
                    // Total count
                    $total_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM pemesanan");
                    $total = $total_result ? mysqli_fetch_assoc($total_result)['total'] : 0;
                    
                    // This month count
                    $date_column = in_array('tanggal_acara', $columns) ? 'tanggal_acara' : 
                                  (in_array('tanggal_sewa', $columns) ? 'tanggal_sewa' : 'created_at');
                    
                    $bulan_query = "SELECT COUNT(*) as bulan FROM pemesanan WHERE MONTH($date_column) = MONTH(CURDATE()) AND YEAR($date_column) = YEAR(CURDATE())";
                    $bulan_result = mysqli_query($conn, $bulan_query);
                    $bulan = $bulan_result ? mysqli_fetch_assoc($bulan_result)['bulan'] : 0;
                    
                    // Total revenue
                    $cost_column = in_array('total_biaya', $columns) ? 'total_biaya' : 
                                  (in_array('total', $columns) ? 'total' : '0');
                    
                    $pendapatan_query = "SELECT SUM($cost_column) as pendapatan FROM pemesanan";
                    $pendapatan_result = mysqli_query($conn, $pendapatan_query);
                    $pendapatan = $pendapatan_result ? (mysqli_fetch_assoc($pendapatan_result)['pendapatan'] ?: 0) : 0;
                    
                    $rata_rata = $total > 0 ? $pendapatan / $total : 0;
                } else {
                    // Sample data if no table
                    $total = 10;
                    $bulan = 3;
                    $pendapatan = 5000000;
                    $rata_rata = 500000;
                }
                
                echo "
                document.getElementById('totalRiwayat').textContent = {$total};
                document.getElementById('bulanIni').textContent = {$bulan};
                document.getElementById('totalPendapatan').textContent = 
                    new Intl.NumberFormat('id-ID', { 
                        style: 'currency', 
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format({$pendapatan});
                document.getElementById('rataRata').textContent = 
                    new Intl.NumberFormat('id-ID', { 
                        style: 'currency', 
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format({$rata_rata});
                ";
            } catch (Exception $e) {
                echo "console.error('Stats error: " . addslashes($e->getMessage()) . "');";
                // Fallback stats
                echo "
                document.getElementById('totalRiwayat').textContent = '0';
                document.getElementById('bulanIni').textContent = '0';
                document.getElementById('totalPendapatan').textContent = 'Rp 0';
                document.getElementById('rataRata').textContent = 'Rp 0';
                ";
            }
            ?>
        }

        function displayData(data) {
            const tbody = document.querySelector('#riwayatTable tbody');
            const mobileContainer = document.querySelector('#mobileCards');
            
            tbody.innerHTML = '';
            mobileContainer.innerHTML = '';
            
            if (data.error) {
                displayError(data.error);
                return;
            }
            
            if (data.length === 0) {
                displayEmpty();
                return;
            }
            
            // Desktop table
            let tableHTML = '';
            data.forEach(riwayat => {
                const statusClass = getStatusClass(riwayat.status_pembayaran);
                const formattedTotal = new Intl.NumberFormat('id-ID', { 
                    style: 'currency', 
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(riwayat.total_biaya || 0);
                
                tableHTML += `
                    <tr>
                        <td><strong>#${riwayat.id}</strong></td>
                        <td>${formatDate(riwayat.tanggal_acara)}</td>
                        <td>${riwayat.nama_acara || 'N/A'}</td>
                        <td>${riwayat.nama_penyewa || 'N/A'}</td>
                        <td>${riwayat.email || 'N/A'}</td>
                        <td><strong>${formattedTotal}</strong></td>
                        <td>
                            <span class="badge ${statusClass}">
                                ${riwayat.status_pembayaran || 'Pending'}
                            </span>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = tableHTML;

            // Mobile cards
            let mobileHTML = '';
            data.forEach(riwayat => {
                const statusClass = getStatusClass(riwayat.status_pembayaran);
                const formattedTotal = new Intl.NumberFormat('id-ID', { 
                    style: 'currency', 
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(riwayat.total_biaya || 0);
                
                mobileHTML += `
                    <div class="mobile-card">
                        <div class="mobile-card-header">
                            <div class="mobile-card-title">${riwayat.nama_acara || 'N/A'}</div>
                            <div class="mobile-card-id">#${riwayat.id}</div>
                        </div>
                        <div class="mobile-card-body">
                            <div class="mobile-field">
                                <span class="mobile-field-label">Tanggal:</span>
                                <span class="mobile-field-value">${formatDate(riwayat.tanggal_acara)}</span>
                            </div>
                            <div class="mobile-field">
                                <span class="mobile-field-label">Penyewa:</span>
                                <span class="mobile-field-value">${riwayat.nama_penyewa || 'N/A'}</span>
                            </div>
                            <div class="mobile-field">
                                <span class="mobile-field-label">Email:</span>
                                <span class="mobile-field-value">${riwayat.email || 'N/A'}</span>
                            </div>
                            <div class="mobile-field">
                                <span class="mobile-field-label">Total:</span>
                                <span class="mobile-field-value"><strong>${formattedTotal}</strong></span>
                            </div>
                            <div class="mobile-field">
                                <span class="mobile-field-label">Status:</span>
                                <span class="badge ${statusClass}">
                                    ${riwayat.status_pembayaran || 'Pending'}
                                </span>
                            </div>
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
            const tbody = document.querySelector('#riwayatTable tbody');
            const mobileContainer = document.querySelector('#mobileCards');
            
            const errorHTML = `
                <tr><td colspan="7" class="loading">
                    <i class="fas fa-exclamation-triangle"></i><br>
                    ${message}
                </td></tr>
            `;
            
            tbody.innerHTML = errorHTML;
            mobileContainer.innerHTML = `<div class="loading">
                <i class="fas fa-exclamation-triangle"></i><br>
                ${message}
            </div>`;
        }

        function displayEmpty() {
            const tbody = document.querySelector('#riwayatTable tbody');
            const mobileContainer = document.querySelector('#mobileCards');
            
            const emptyHTML = `
                <tr><td colspan="7" class="loading">
                    <i class="fas fa-history"></i><br>
                    Belum ada riwayat pemesanan
                </td></tr>
            `;
            
            tbody.innerHTML = emptyHTML;
            mobileContainer.innerHTML = `<div class="loading">
                <i class="fas fa-history"></i><br>
                Belum ada riwayat pemesanan
            </div>`;
        }

        function refreshData() {
            location.reload();
        }

        function loadData() {
            console.log('Data already loaded on page load');
        }

        function loadStats() {
            console.log('Stats already loaded on page load');
        }

        function exportData() {
            // Simple CSV export
            if (allData.length === 0) {
                alert('Tidak ada data untuk di export');
                return;
            }
            
            let csv = 'ID,Tanggal Acara,Nama Acara,Penyewa,Email,Total Biaya,Status\n';
            allData.forEach(row => {
                csv += `${row.id},"${formatDate(row.tanggal_acara)}","${row.nama_acara || ''}","${row.nama_penyewa || ''}","${row.email || ''}","${row.total_biaya || 0}","${row.status_pembayaran || ''}"\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'riwayat_pemesanan_' + new Date().toISOString().split('T')[0] + '.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }

        function toggleMobileMenu() {
            console.log('Mobile menu toggle called');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.mobile-overlay');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (!sidebar || !overlay) {
                console.error('Mobile menu elements not found');
                return;
            }

            const isActive = sidebar.classList.contains('active');
            
            if (isActive) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                if (toggle) toggle.innerHTML = '<i class="fas fa-bars"></i>';
                document.body.style.overflow = 'auto';
                console.log('Mobile menu closed');
            } else {
                sidebar.classList.add('active');
                overlay.classList.add('active');
                if (toggle) toggle.innerHTML = '<i class="fas fa-times"></i>';
                document.body.style.overflow = 'hidden';
                console.log('Mobile menu opened');
            }
        }

        function closeMobileMenu() {
            console.log('Close mobile menu called');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.mobile-overlay');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (sidebar) sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            if (toggle) toggle.innerHTML = '<i class="fas fa-bars"></i>';
            document.body.style.overflow = 'auto';
        }

        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM ready - Initializing riwayat pemesanan...');
            
            // Setup mobile menu
            const mobileToggle = document.querySelector('.mobile-menu-toggle');
            const overlay = document.querySelector('.mobile-overlay');
            
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleMobileMenu();
                });
                
                mobileToggle.addEventListener('touchstart', function(e) {
                    e.preventDefault();
                    toggleMobileMenu();
                });
                
                console.log('Mobile toggle event listeners added');
            }
            
            if (overlay) {
                overlay.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeMobileMenu();
                });
                console.log('Overlay event listener added');
            }

            // Close mobile menu when clicking nav links
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        setTimeout(closeMobileMenu, 150);
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    closeMobileMenu();
                }
            });

            // Load stats
            loadStatsFallback();
            
            // Add filter event listeners
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    setTimeout(applyFilter, 300); // Debounce search
                });
            }
            
            const tipePenyewaFilter = document.getElementById('tipePenyewaFilter');
            if (tipePenyewaFilter) {
                tipePenyewaFilter.addEventListener('change', applyFilter);
            }
            
            console.log('Riwayat pemesanan page initialized');
        });

        function applyFilter() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const startDate = document.getElementById('startDate').value;
            const eventDate = document.getElementById('eventDate').value;
            const tipePenyewa = document.getElementById('tipePenyewaFilter').value;
            
            const rows = document.querySelectorAll('#riwayatTable tbody .filterable-row');
            let visibleCount = 0;
            
            rows.forEach(row => {
                let shouldShow = true;
                
                // Search filter
                if (searchInput) {
                    const rowText = row.textContent.toLowerCase();
                    shouldShow = rowText.includes(searchInput);
                }
                
                // Tipe penyewa filter
                if (tipePenyewa && shouldShow) {
                    const rowTipe = row.getAttribute('data-tipe');
                    shouldShow = rowTipe === tipePenyewa;
                }
                
                // Date filters can be added here for more complex filtering
                // For now, we'll keep it simple with search and tipe penyewa
                
                if (shouldShow) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update row numbers for visible rows
            let rowNumber = 1;
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const firstCell = row.querySelector('td:first-child strong');
                    if (firstCell) {
                        firstCell.textContent = rowNumber++;
                    }
                }
            });
            
            console.log(`Filter applied. Showing ${visibleCount} of ${rows.length} rows.`);
        }

        // Prevent mobile menu conflicts
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const isMenuOpen = sidebar && sidebar.classList.contains('active');
            
            if (isMenuOpen && !e.target.closest('.sidebar') && !e.target.closest('.mobile-menu-toggle')) {
                closeMobileMenu();
            }
        });

        // Keyboard support
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('sidebar');
                if (sidebar && sidebar.classList.contains('active')) {
                    closeMobileMenu();
                }
            }
        });
    </script>
</body>
</html>
