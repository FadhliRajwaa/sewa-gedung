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

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: all 0.2s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--white);
        }

        .card-icon.primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }

        .card-icon.success {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
        }

        .card-icon.warning {
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
        }

        .card-icon.info {
            background: linear-gradient(135deg, var(--accent) 0%, #0891b2 100%);
        }

        .card-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-value {
            font-size: 32px;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .card-description {
            font-size: 14px;
            color: var(--gray);
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

        /* Chart Container */
        .chart-container {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 32px;
            height: 400px; /* Fixed height */
        }

        .chart-header {
            margin-bottom: 24px;
        }

        .chart-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .chart-description {
            font-size: 14px;
            color: var(--gray);
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
            width: 100%;
        }

        #monthlyChart {
            max-height: 300px !important;
            max-width: 100% !important;
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
            padding: 16px 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
        }

        th {
            background: var(--gray-light);
            font-weight: 600;
            color: var(--dark);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background: rgba(99, 102, 241, 0.02);
        }

        .badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 11px;
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

        .badge-primary {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
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

        /* Responsive */
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
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }

            .page-title {
                font-size: 24px;
            }

            .cards-grid {
                grid-template-columns: 1fr;
                gap: 16px;
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

            table {
                font-size: 12px;
            }

            th, td {
                padding: 12px 8px;
            }

            .chart-container {
                height: 300px;
                padding: 16px;
            }

            .chart-wrapper {
                height: 200px;
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
                <button class="btn btn-primary" onclick="exportData()">
                    <i class="fas fa-download"></i>
                    Export Laporan
                </button>
            </div>

            <?php
            // Ambil statistik
            $stats = [];
            
            // Total pemesanan
            $total_pemesanan = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM pemesanan"))[0];
            
            // Total pendapatan
            $total_pendapatan = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(total), 0) FROM pemesanan"))[0];
            
            // Pemesanan bulan ini
            $bulan_ini = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM pemesanan WHERE MONTH(tanggal_pesan) = MONTH(NOW()) AND YEAR(tanggal_pesan) = YEAR(NOW())"))[0];
            
            // Pemesanan lunas
            $pemesanan_lunas = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM pembayaran WHERE status_pembayaran = 'Lunas'"))[0];
            ?>

            <!-- Statistics Cards -->
            <div class="cards-grid">
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon primary">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <div class="card-title">Total Pemesanan</div>
                        </div>
                    </div>
                    <div class="card-value"><?= $total_pemesanan ?></div>
                    <div class="card-description">Semua pemesanan</div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-icon success">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <div class="card-title">Total Pendapatan</div>
                        </div>
                    </div>
                    <div class="card-value">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
                    <div class="card-description">Pendapatan keseluruhan</div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-icon warning">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <div class="card-title">Bulan Ini</div>
                        </div>
                    </div>
                    <div class="card-value"><?= $bulan_ini ?></div>
                    <div class="card-description">Pemesanan bulan ini</div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-icon info">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div class="card-title">Lunas</div>
                        </div>
                    </div>
                    <div class="card-value"><?= $pemesanan_lunas ?></div>
                    <div class="card-description">Pembayaran lunas</div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label class="filter-label">Tanggal Mulai</label>
                        <input type="date" class="filter-input" id="startDate" onchange="applyFilter()">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Tanggal Akhir</label>
                        <input type="date" class="filter-input" id="endDate" onchange="applyFilter()">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Acara</label>
                        <select class="filter-input" id="acaraFilter" onchange="applyFilter()">
                            <option value="">Semua Acara</option>
                            <?php
                            $acara_query = mysqli_query($conn, "SELECT DISTINCT nama_acara FROM acara ORDER BY nama_acara");
                            while($acara = mysqli_fetch_assoc($acara_query)) {
                                echo "<option value='{$acara['nama_acara']}'>{$acara['nama_acara']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Status Pembayaran</label>
                        <select class="filter-input" id="statusFilter" onchange="applyFilter()">
                            <option value="">Semua Status</option>
                            <option value="Lunas">Lunas</option>
                            <option value="Belum Lunas">Belum Lunas</option>
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

            <!-- Chart Container -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">Grafik Pemesanan Bulanan</h3>
                    <p class="chart-description">Statistik pemesanan per bulan tahun ini</p>
                </div>
                <div class="chart-wrapper">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <!-- Table Container -->
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">Data Laporan Penyewaan</h3>
                </div>
                
                <div class="table-responsive">
                    <table id="laporanTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Pemesanan</th>
                                <th>Nama Penyewa</th>
                                <th>Acara</th>
                                <th>Tanggal Sewa</th>
                                <th>Durasi</th>
                                <th>Total Biaya</th>
                                <th>Status Pembayaran</th>
                                <th>Tanggal Pesan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query laporan penyewaan
                            $query = "SELECT 
                                        p.id_pemesanan,
                                        p.tanggal_sewa,
                                        p.tanggal_selesai,
                                        p.durasi,
                                        p.total,
                                        p.tanggal_pesan,
                                        CASE 
                                            WHEN py.tipe_penyewa = 'instansi' THEN py.nama_instansi
                                            ELSE py.nama_lengkap
                                        END as nama_penyewa,
                                        a.nama_acara,
                                        COALESCE(pb.status_pembayaran, 'Belum Lunas') as status_pembayaran
                                      FROM pemesanan p
                                      LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa
                                      LEFT JOIN acara a ON p.id_acara = a.id_acara
                                      LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
                                      ORDER BY p.tanggal_pesan DESC";
                            
                            $result = mysqli_query($conn, $query);
                            $no = 1;
                            
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $status_class = ($row['status_pembayaran'] == 'Lunas') ? 'badge-success' : 'badge-warning';
                                    
                                    echo "<tr data-tanggal='{$row['tanggal_sewa']}' data-acara='{$row['nama_acara']}' data-status='{$row['status_pembayaran']}'>";
                                    echo "<td><strong>$no</strong></td>";
                                    echo "<td><strong>#{$row['id_pemesanan']}</strong></td>";
                                    echo "<td><strong>{$row['nama_penyewa']}</strong></td>";
                                    echo "<td>{$row['nama_acara']}</td>";
                                    echo "<td>" . date('d M Y', strtotime($row['tanggal_sewa'])) . "</td>";
                                    echo "<td>{$row['durasi']} hari</td>";
                                    echo "<td><strong>Rp " . number_format($row['total'], 0, ',', '.') . "</strong></td>";
                                    echo "<td><span class='badge {$status_class}'>{$row['status_pembayaran']}</span></td>";
                                    echo "<td>" . date('d M Y H:i', strtotime($row['tanggal_pesan'])) . "</td>";
                                    echo "</tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr>";
                                echo "<td colspan='9' class='empty-state'>";
                                echo "<div class='empty-icon'><i class='fas fa-chart-bar'></i></div>";
                                echo "<div class='empty-text'>Belum ada data laporan</div>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        let allData = [];
        let filteredData = [];
        let chartInitialized = false;

        // Load data on page ready
        document.addEventListener('DOMContentLoaded', function() {
            if (!chartInitialized) {
                loadData();
                setTimeout(initChart, 100); // Small delay to ensure DOM is ready
                chartInitialized = true;
            }
        });

        function loadData() {
            const tableRows = document.querySelectorAll('#laporanTable tbody tr');
            allData = [];
            
            tableRows.forEach(row => {
                const cells = row.cells;
                if (cells.length > 1) {
                    allData.push({
                        no: cells[0].textContent.trim(),
                        id_pemesanan: cells[1].textContent.trim(),
                        nama_penyewa: cells[2].textContent.trim(),
                        nama_acara: cells[3].textContent.trim(),
                        tanggal_sewa: cells[4].textContent.trim(),
                        tanggal_sewa_raw: row.getAttribute('data-tanggal'),
                        durasi: cells[5].textContent.trim(),
                        total: cells[6].textContent.trim(),
                        status: cells[7].textContent.trim(),
                        tanggal_pesan: cells[8].textContent.trim()
                    });
                }
            });
            
            filteredData = allData;
        }

        function applyFilter() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const acaraFilter = document.getElementById('acaraFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            
            filteredData = allData.filter(item => {
                let dateMatch = true;
                let acaraMatch = true;
                let statusMatch = true;
                
                // Date filter
                if (startDate && item.tanggal_sewa_raw) {
                    const itemDate = new Date(item.tanggal_sewa_raw);
                    const filterDate = new Date(startDate);
                    dateMatch = itemDate >= filterDate;
                }
                
                if (endDate && dateMatch && item.tanggal_sewa_raw) {
                    const itemDate = new Date(item.tanggal_sewa_raw);
                    const filterDate = new Date(endDate);
                    dateMatch = itemDate <= filterDate;
                }
                
                // Acara filter
                if (acaraFilter) {
                    acaraMatch = item.nama_acara === acaraFilter;
                }
                
                // Status filter
                if (statusFilter) {
                    statusMatch = item.status.includes(statusFilter);
                }
                
                return dateMatch && acaraMatch && statusMatch;
            });
            
            updateTable();
        }

        function resetFilter() {
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            document.getElementById('acaraFilter').value = '';
            document.getElementById('statusFilter').value = '';
            
            filteredData = allData;
            updateTable();
        }

        function updateTable() {
            const tbody = document.querySelector('#laporanTable tbody');
            
            if (filteredData.length === 0) {
                tbody.innerHTML = `
                    <tr><td colspan="9" class="empty-state">
                        <i class="fas fa-chart-bar"></i><br>
                        Tidak ada data yang sesuai dengan filter
                    </td></tr>
                `;
                return;
            }
            
            let tableHTML = '';
            filteredData.forEach((item, index) => {
                const statusClass = item.status.includes('Lunas') ? 'badge-success' : 'badge-warning';
                
                tableHTML += `
                    <tr>
                        <td><strong>${index + 1}</strong></td>
                        <td><strong>${item.id_pemesanan}</strong></td>
                        <td><strong>${item.nama_penyewa}</strong></td>
                        <td>${item.nama_acara}</td>
                        <td>${item.tanggal_sewa}</td>
                        <td>${item.durasi}</td>
                        <td><strong>${item.total}</strong></td>
                        <td><span class="badge ${statusClass}">${item.status}</span></td>
                        <td>${item.tanggal_pesan}</td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = tableHTML;
        }

        function initChart() {
            // Pastikan Chart.js sudah loaded
            if (typeof Chart === 'undefined') {
                console.error('Chart.js not loaded');
                return;
            }

            const ctx = document.getElementById('monthlyChart');
            if (!ctx) {
                console.error('Canvas element not found');
                return;
            }

            // Destroy existing chart if exists
            if (window.monthlyChart instanceof Chart) {
                window.monthlyChart.destroy();
            }

            // Get data dari PHP untuk grafik
            <?php
            // Query data pemesanan per bulan tahun ini
            $monthly_data = [];
            $monthly_labels = [];
            
            for ($i = 1; $i <= 12; $i++) {
                $month_name = date('M', mktime(0, 0, 0, $i, 1));
                $monthly_labels[] = $month_name;
                
                // Hitung pemesanan per bulan
                $month_query = "SELECT COUNT(*) as total FROM pemesanan WHERE MONTH(tanggal_pesan) = $i AND YEAR(tanggal_pesan) = YEAR(NOW())";
                $month_result = mysqli_query($conn, $month_query);
                $month_count = $month_result ? mysqli_fetch_assoc($month_result)['total'] : 0;
                $monthly_data[] = (int)$month_count;
            }
            ?>
            
            const labels = <?= json_encode($monthly_labels) ?>;
            const pemesananData = <?= json_encode($monthly_data) ?>;
            
            const config = {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Pemesanan',
                        data: pemesananData,
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(99, 102, 241, 1)',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#64748b'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                stepSize: 1,
                                color: '#64748b',
                                callback: function(value) {
                                    return Math.floor(value);
                                }
                            }
                        }
                    },
                    elements: {
                        point: {
                            hoverRadius: 8
                        }
                    }
                }
            };

            try {
                window.monthlyChart = new Chart(ctx, config);
            } catch (error) {
                console.error('Error creating chart:', error);
                document.getElementById('monthlyChart').style.display = 'none';
                document.querySelector('.chart-wrapper').innerHTML = '<div style="text-align: center; color: #64748b; padding: 60px;">Grafik tidak dapat dimuat</div>';
            }
        }

        function exportData() {
            // Implement export functionality
            alert('Fitur export akan segera tersedia');
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
    </script>
</body>
</html>
