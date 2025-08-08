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
    <title>Laporan Penyewaan - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            -webkit-overflow-scrolling: touch;
            border-radius: var(--radius);
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: var(--gray-light);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--gray);
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
            background: var(--white);
        }

        th, td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        th {
            background: var(--gray-light);
            font-weight: 600;
            color: var(--dark);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        tr:hover {
            background: rgba(99, 102, 241, 0.02);
        }

        tr:nth-child(even) {
            background: rgba(248, 250, 252, 0.5);
        }

        tr:nth-child(even):hover {
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

        /* Utility Classes */
        .text-center {
            text-align: center;
        }

        .text-red-500 {
            color: var(--danger);
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
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
                transition: all 0.3s ease;
            }

            .mobile-menu-toggle:hover {
                background: var(--gray-light);
                transform: scale(1.05);
            }

            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .mobile-overlay.active {
                display: block;
                opacity: 1;
            }

            .main-content {
                margin-left: 0;
                padding: 16px;
                padding-top: 80px;
                width: 100%;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .page-header {
                padding: 20px 16px;
                margin-bottom: 24px;
            }

            .page-title {
                font-size: 24px;
                line-height: 1.2;
            }

            .page-subtitle {
                font-size: 14px;
                margin-top: 8px;
            }

            .table-container {
                border-radius: 12px;
                overflow: hidden;
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

            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                border-radius: 0;
            }

            table {
                min-width: 600px;
                font-size: 14px;
            }

            th, td {
                padding: 12px 8px;
                white-space: nowrap;
            }

            th {
                font-size: 12px;
                position: sticky;
                top: 0;
                background: var(--gray-light);
                z-index: 10;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-number {
                font-size: 28px;
            }

            .stat-label {
                font-size: 13px;
            }

            .badge {
                font-size: 10px;
                padding: 4px 8px;
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
                text-align: center;
            }

            .stat-number {
                font-size: 24px;
            }

            .stat-icon {
                margin: 0 auto 12px;
            }

            .table-container {
                margin-bottom: 16px;
            }

            .table-header {
                padding: 12px;
            }

            .table-title {
                font-size: 16px;
            }

            table {
                min-width: 500px;
                font-size: 12px;
            }

            th, td {
                padding: 8px 6px;
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
                    <a href="laporan_penyewaan.php" class="nav-link active">
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
                <h1 class="page-title">Laporan Penyewaan</h1>
                <p class="page-subtitle">Analisis dan laporan komprehensif penyewaan gedung</p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-chart-line"></i>
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
                                echo 'Rp 10,000,000';
                            }
                        } catch (Exception $e) {
                            echo 'Rp 0';
                        }
                    ?></div>
                    <div class="stat-label">Total Pendapatan</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-number" id="totalPemesanan"><?php
                        try {
                            $total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM pemesanan");
                            echo $total_query ? mysqli_fetch_assoc($total_query)['total'] : '25';
                        } catch (Exception $e) {
                            echo '25';
                        }
                    ?></div>
                    <div class="stat-label">Total Pemesanan</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-chart-simple"></i>
                    </div>
                    <div class="stat-number" id="rataRataBulanan"><?php
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
                                $rata_rata = $pendapatan > 0 ? $pendapatan / 12 : 0;
                                echo 'Rp ' . number_format($rata_rata, 0, ',', '.');
                            } else {
                                echo 'Rp 850,000';
                            }
                        } catch (Exception $e) {
                            echo 'Rp 0';
                        }
                    ?></div>
                    <div class="stat-label">Rata-rata Bulanan</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon accent">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-number" id="tingkatOkupansi"><?php
                        try {
                            $tables_check = mysqli_query($conn, "SHOW TABLES LIKE 'pemesanan'");
                            if (mysqli_num_rows($tables_check) > 0) {
                                $total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM pemesanan");
                                $total = $total_query ? mysqli_fetch_assoc($total_query)['total'] : 0;
                                $okupansi = $total > 0 ? min(($total / 30) * 100, 100) : 0;
                                echo round($okupansi) . '%';
                            } else {
                                echo '15%';
                            }
                        } catch (Exception $e) {
                            echo '0%';
                        }
                    ?></div>
                    <div class="stat-label">Tingkat Okupansi</div>
                </div>
            </div>

            <!-- Summary Table -->
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">Ringkasan Laporan</h3>
                    <div class="table-actions">
                        <button class="btn btn-secondary" onclick="exportReport()">
                            <i class="fas fa-download"></i>
                            Export
                        </button>
                        <button class="btn btn-primary" onclick="refreshReport()">
                            <i class="fas fa-refresh"></i>
                            Refresh
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table id="reportTable">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th>Nama Acara</th>
                                <th>Tipe Penyewa</th>
                                <th>Jumlah Pemesanan</th>
                                <th>Total Pendapatan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Generate monthly report
                            try {
                                $tables_check = mysqli_query($conn, "SHOW TABLES LIKE 'pemesanan'");
                                
                                if (mysqli_num_rows($tables_check) > 0) {
                                    // Get column info
                                    $columns_check = mysqli_query($conn, "SHOW COLUMNS FROM pemesanan");
                                    $columns = [];
                                    while ($col = mysqli_fetch_assoc($columns_check)) {
                                        $columns[] = $col['Field'];
                                    }
                                    
                                    $date_column = in_array('tanggal_acara', $columns) ? 'tanggal_acara' : 
                                                  (in_array('tanggal_sewa', $columns) ? 'tanggal_sewa' : 'created_at');
                                    $cost_column = in_array('total_biaya', $columns) ? 'total_biaya' : 
                                                  (in_array('total', $columns) ? 'total' : '0');
                                    $event_column = in_array('jenis_acara', $columns) ? 'jenis_acara' : 
                                                   (in_array('acara', $columns) ? 'acara' : "'Umum'");
                                    
                                    $query = "SELECT 
                                        MONTHNAME($date_column) as bulan,
                                        COALESCE(a.nama_acara, 'Event') as nama_acara,
                                        CASE 
                                            WHEN py.tipe_penyewa = 'instansi' THEN 'Instansi'
                                            WHEN py.tipe_penyewa = 'umum' THEN 'Umum'
                                            ELSE 'Tidak Diketahui'
                                        END as tipe_penyewa,
                                        COUNT(*) as jumlah_pemesanan,
                                        SUM($cost_column) as total_pendapatan,
                                        'Active' as status
                                        FROM pemesanan p
                                        LEFT JOIN acara a ON p.id_acara = a.id_acara 
                                        LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa
                                        WHERE YEAR($date_column) = YEAR(CURDATE())
                                        GROUP BY MONTH($date_column), a.nama_acara, py.tipe_penyewa
                                        ORDER BY MONTH($date_column) DESC, a.nama_acara, py.tipe_penyewa";
                                    
                                    $result = mysqli_query($conn, $query);
                                    
                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $tipe_class = ($row['tipe_penyewa'] == 'Instansi') ? 'badge-info' : 'badge-primary';
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['bulan']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nama_acara']) . "</td>";
                                            echo "<td><span class='badge {$tipe_class}'>" . htmlspecialchars($row['tipe_penyewa']) . "</span></td>";
                                            echo "<td>" . $row['jumlah_pemesanan'] . "</td>";
                                            echo "<td>Rp " . number_format($row['total_pendapatan'], 0, ',', '.') . "</td>";
                                            echo "<td><span class='badge badge-success'>Active</span></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center'>Tidak ada data laporan</td></tr>";
                                    }
                                } else {
                                    // Sample data
                                    $sample_reports = [
                                        ['Januari', 'Pernikahan', 'Umum', 5, 50000000, 'Active'],
                                        ['Januari', 'Seminar', 'Instansi', 3, 21000000, 'Active'],
                                        ['Februari', 'Rapat', 'Instansi', 2, 10000000, 'Active'],
                                        ['Februari', 'Pernikahan', 'Umum', 4, 40000000, 'Active']
                                    ];
                                    
                                    foreach ($sample_reports as $report) {
                                        $tipe_class = ($report[2] == 'Instansi') ? 'badge-info' : 'badge-primary';
                                        echo "<tr>";
                                        echo "<td>{$report[0]}</td>";
                                        echo "<td>{$report[1]}</td>";
                                        echo "<td><span class='badge {$tipe_class}'>{$report[2]}</span></td>";
                                        echo "<td>{$report[3]}</td>";
                                        echo "<td>Rp " . number_format($report[4], 0, ',', '.') . "</td>";
                                        echo "<td><span class='badge badge-success'>{$report[5]}</span></td>";
                                        echo "</tr>";
                                    }
                                }
                            } catch (Exception $e) {
                                echo "<tr><td colspan='6' class='text-center text-red-500'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Detailed Bookings Table -->
            <div class="table-container" style="margin-top: 2rem;">
                <div class="table-header">
                    <h3 class="table-title">Detail Laporan Penyewaan</h3>
                    <div class="table-actions">
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <select id="filterLaporan" class="filter-input" onchange="toggleFilterColumn()">
                                <option value="bulan">Filter Bulan</option>
                                <option value="tahun">Filter Tahun</option>
                            </select>
                            <button class="btn btn-primary" onclick="location.reload()">
                                <i class="fas fa-refresh"></i>
                                Refresh
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th id="periodColumn">Bulan</th>
                                <th>Nama Penyewa</th>
                                <th>Tipe Penyewa</th>
                                <th>Nama Acara</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Total Pendapatan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                // Query untuk detail laporan penyewaan
                                $query = "SELECT 
                                            p.id_pemesanan,
                                            p.tanggal_sewa,
                                            p.tanggal_selesai,
                                            p.total,
                                            CASE 
                                                WHEN py.tipe_penyewa = 'instansi' THEN py.nama_instansi
                                                ELSE py.nama_lengkap
                                            END as nama_penyewa,
                                            py.tipe_penyewa,
                                            a.nama_acara,
                                            pb.status_pembayaran,
                                            MONTHNAME(p.tanggal_sewa) as bulan,
                                            YEAR(p.tanggal_sewa) as tahun
                                          FROM pemesanan p
                                          LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa
                                          LEFT JOIN acara a ON p.id_acara = a.id_acara
                                          LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
                                          ORDER BY p.tanggal_sewa DESC";
                                
                                $result = mysqli_query($conn, $query);
                                
                                if ($result && mysqli_num_rows($result) > 0) {
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $status_pembayaran = $row['status_pembayaran'] ?: 'Belum Lunas';
                                        $status_class = ($status_pembayaran == 'Lunas') ? 'badge-success' : 'badge-warning';
                                        $tipe_class = ($row['tipe_penyewa'] == 'instansi') ? 'badge-info' : 'badge-warning';
                                        
                                        echo "<tr>";
                                        echo "<td>{$no}</td>";
                                        echo "<td class='period-cell'>";
                                        echo "<span class='bulan-data'>{$row['bulan']}</span>";
                                        echo "<span class='tahun-data' style='display:none;'>{$row['tahun']}</span>";
                                        echo "</td>";
                                        echo "<td><strong>{$row['nama_penyewa']}</strong></td>";
                                        echo "<td><span class='badge {$tipe_class}'>" . ucfirst($row['tipe_penyewa']) . "</span></td>";
                                        echo "<td><strong>{$row['nama_acara']}</strong></td>";
                                        echo "<td>" . date('d M Y', strtotime($row['tanggal_sewa'])) . "</td>";
                                        echo "<td>" . date('d M Y', strtotime($row['tanggal_selesai'])) . "</td>";
                                        echo "<td><strong>Rp " . number_format($row['total'], 0, ',', '.') . "</strong></td>";
                                        echo "<td><span class='badge {$status_class}'>{$status_pembayaran}</span></td>";
                                        echo "</tr>";
                                        $no++;
                                    }
                                } else {
                                    echo "<tr>";
                                    echo "<td colspan='9' style='text-align: center; padding: 40px;'>";
                                    echo "<div style='color: var(--gray);'>";
                                    echo "<i class='fas fa-chart-bar' style='font-size: 48px; margin-bottom: 16px;'></i>";
                                    echo "<div>Belum ada data laporan penyewaan</div>";
                                    echo "</div>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } catch (Exception $e) {
                                echo "<tr><td colspan='9' class='text-center text-red-500'>Error loading data: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Global variables
        let mobileMenuOpen = false;

        // Utility functions
        function toggleMobileMenu() {
            console.log('toggleMobileMenu called');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.mobile-overlay');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (!sidebar || !overlay || !toggle) {
                console.error('Mobile menu elements not found:', {sidebar, overlay, toggle});
                return;
            }

            mobileMenuOpen = !mobileMenuOpen;
            console.log('Mobile menu open:', mobileMenuOpen);
            
            if (mobileMenuOpen) {
                sidebar.classList.add('active');
                overlay.classList.add('active');
                toggle.innerHTML = '<i class="fas fa-times"></i>';
                document.body.style.overflow = 'hidden'; // Prevent background scroll
                console.log('Mobile menu opened');
            } else {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                toggle.innerHTML = '<i class="fas fa-bars"></i>';
                document.body.style.overflow = 'auto';
                console.log('Mobile menu closed');
            }
        }

        function closeMobileMenu() {
            console.log('closeMobileMenu called');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.mobile-overlay');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (!sidebar || !overlay) {
                console.error('Mobile menu elements not found for close');
                return;
            }

            mobileMenuOpen = false;
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            if (toggle) {
                toggle.innerHTML = '<i class="fas fa-bars"></i>';
            }
            document.body.style.overflow = 'auto';
            console.log('Mobile menu force closed');
        }

        // Report functions
        function loadStats() {
            console.log('Stats loaded from server-side rendering');
        }

        function loadReport() {
            console.log('Report loaded from server-side rendering');
        }

        function refreshReport() {
            showLoading();
            setTimeout(() => {
                location.reload();
            }, 500);
        }

        function exportReport() {
            // Simple export functionality
            const table = document.querySelector('#reportTable');
            if (!table) {
                alert('Tidak ada data untuk di-export');
                return;
            }
            
            alert('Fitur export sedang dalam pengembangan.\nAnda dapat menggunakan Print untuk mencetak laporan.');
        }

        function showLoading() {
            const tables = document.querySelectorAll('.table-container');
            tables.forEach(table => {
                const tbody = table.querySelector('tbody');
                if (tbody) {
                    tbody.innerHTML = '<tr><td colspan="100%" class="text-center"><div class="spinner"></div> Loading...</td></tr>';
                }
            });
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM ready - Initializing laporan penyewaan...');
            
            // Initialize mobile menu with multiple selectors
            const mobileToggle = document.querySelector('.mobile-menu-toggle') || document.getElementById('mobileMenuToggle');
            const overlay = document.querySelector('.mobile-overlay');
            const sidebar = document.getElementById('sidebar');
            
            console.log('Elements found:', {
                mobileToggle: !!mobileToggle,
                overlay: !!overlay, 
                sidebar: !!sidebar
            });
            
            if (mobileToggle) {
                // Remove any existing listeners
                mobileToggle.removeAttribute('onclick');
                
                // Add click event listener
                mobileToggle.addEventListener('click', function(e) {
                    console.log('Mobile toggle clicked!');
                    e.preventDefault();
                    e.stopPropagation();
                    toggleMobileMenu();
                });
                
                // Also add touch event for mobile
                mobileToggle.addEventListener('touchstart', function(e) {
                    console.log('Mobile toggle touched!');
                    e.preventDefault();
                    toggleMobileMenu();
                });
                
                console.log('Mobile toggle event listeners added');
            } else {
                console.error('Mobile toggle button not found!');
            }
            
            if (overlay) {
                overlay.addEventListener('click', function(e) {
                    console.log('Overlay clicked');
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

            // Initialize stats with fallback
            loadStatsFallback();
            
            console.log('Laporan penyewaan initialized successfully');
        });

        // Stats fallback function
        function loadStatsFallback() {
            <?php
            try {
                // Check if tables exist
                $tables_check = mysqli_query($conn, "SHOW TABLES LIKE 'pemesanan'");
                
                if (mysqli_num_rows($tables_check) > 0) {
                    // Get column info
                    $columns_check = mysqli_query($conn, "SHOW COLUMNS FROM pemesanan");
                    $columns = [];
                    while ($col = mysqli_fetch_assoc($columns_check)) {
                        $columns[] = $col['Field'];
                    }
                    
                    // Determine column names
                    $cost_column = in_array('total_biaya', $columns) ? 'total_biaya' : 
                                  (in_array('total', $columns) ? 'total' : '0');
                    $date_column = in_array('tanggal_acara', $columns) ? 'tanggal_acara' : 
                                  (in_array('tanggal_sewa', $columns) ? 'tanggal_sewa' : 'created_at');
                    
                    // Calculate stats
                    $pendapatan_result = mysqli_query($conn, "SELECT SUM($cost_column) as pendapatan FROM pemesanan");
                    $pemesanan_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM pemesanan");
                    $bulan_result = mysqli_query($conn, "SELECT COUNT(*) as bulan FROM pemesanan WHERE MONTH($date_column) = MONTH(CURDATE()) AND YEAR($date_column) = YEAR(CURDATE())");
                    
                    $pendapatan = $pendapatan_result ? (mysqli_fetch_assoc($pendapatan_result)['pendapatan'] ?: 0) : 0;
                    $total_pemesanan = $pemesanan_result ? mysqli_fetch_assoc($pemesanan_result)['total'] : 0;
                    $bulan_ini = $bulan_result ? mysqli_fetch_assoc($bulan_result)['bulan'] : 0;
                    
                    $rata_rata = $total_pemesanan > 0 ? $pendapatan / 12 : 0;
                    $okupansi = $total_pemesanan > 0 ? ($bulan_ini / 30) * 100 : 0;
                } else {
                    // Sample data
                    $pendapatan = 10000000;
                    $total_pemesanan = 25;
                    $rata_rata = 850000;
                    $okupansi = 15;
                }
                
                echo "
                try {
                    const totalPendapatan = document.getElementById('totalPendapatan');
                    const totalPemesanan = document.getElementById('totalPemesanan');
                    const rataRataBulanan = document.getElementById('rataRataBulanan');
                    const tingkatOkupansi = document.getElementById('tingkatOkupansi');
                    
                    if (totalPendapatan) {
                        totalPendapatan.textContent = 
                            new Intl.NumberFormat('id-ID', { 
                                style: 'currency', 
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format({$pendapatan});
                    }
                    
                    if (totalPemesanan) {
                        totalPemesanan.textContent = {$total_pemesanan};
                    }
                    
                    if (rataRataBulanan) {
                        rataRataBulanan.textContent = 
                            new Intl.NumberFormat('id-ID', { 
                                style: 'currency', 
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format({$rata_rata});
                    }
                    
                    if (tingkatOkupansi) {
                        tingkatOkupansi.textContent = Math.round({$okupansi}) + '%';
                    }
                } catch (error) {
                    console.error('Error updating stats:', error);
                }
                ";
            } catch (Exception $e) {
                echo "console.error('Stats error: " . addslashes($e->getMessage()) . "');";
            }
            ?>
        }

        // Prevent default behaviors
        document.addEventListener('click', function(e) {
            // Close mobile menu when clicking outside
            if (mobileMenuOpen && !e.target.closest('.sidebar') && !e.target.closest('.mobile-menu-toggle')) {
                closeMobileMenu();
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileMenuOpen) {
                closeMobileMenu();
            }
        });
    </script>
    <script>
        function toggleFilterColumn() {
            const filterSelect = document.getElementById('filterLaporan');
            const periodColumn = document.getElementById('periodColumn');
            const bulanData = document.querySelectorAll('.bulan-data');
            const tahunData = document.querySelectorAll('.tahun-data');
            
            if (filterSelect.value === 'tahun') {
                periodColumn.textContent = 'Tahun';
                bulanData.forEach(el => el.style.display = 'none');
                tahunData.forEach(el => el.style.display = 'inline');
            } else {
                periodColumn.textContent = 'Bulan';
                bulanData.forEach(el => el.style.display = 'inline');
                tahunData.forEach(el => el.style.display = 'none');
            }
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

        // Initialize chart
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart');
            if (ctx && typeof Chart !== 'undefined') {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Pendapatan (Rp)',
                            data: [12000000, 15000000, 8000000, 20000000, 18000000, 25000000],
                            backgroundColor: 'rgba(99, 102, 241, 0.8)',
                            borderColor: 'rgba(99, 102, 241, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>