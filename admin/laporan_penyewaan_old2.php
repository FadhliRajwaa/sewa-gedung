<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penyewaan - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #334155;
            line-height: 1.6;
        }

        /* Layout Container */
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: #1e293b;
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .sidebar-header {
            text-align: center;
            padding: 0 2rem 2rem;
            border-bottom: 1px solid #334155;
            margin-bottom: 2rem;
        }

        .sidebar-header h2 {
            color: #f1f5f9;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .sidebar-header p {
            color: #94a3b8;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .nav-menu {
            list-style: none;
            padding: 0 1rem;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #334155;
            color: #f1f5f9;
            transform: translateX(4px);
        }

        .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.125rem;
            width: 20px;
            text-align: center;
        }

        /* Mobile Menu */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: #1e293b;
            border: none;
            color: #f1f5f9;
            padding: 0.75rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 1.125rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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
            padding: 2rem;
            min-height: 100vh;
        }

        /* Page Header */
        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            color: #1e293b;
            font-size: 2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .page-title i {
            color: #3b82f6;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-label {
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
        }

        .filter-input {
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: border-color 0.2s ease;
        }

        .filter-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        /* Report Summary Cards */
        .report-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-2px);
        }

        .summary-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .summary-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .summary-icon {
            padding: 0.75rem;
            border-radius: 0.75rem;
            font-size: 1.25rem;
        }

        .summary-icon.blue {
            background: #dbeafe;
            color: #3b82f6;
        }

        .summary-icon.green {
            background: #d1fae5;
            color: #10b981;
        }

        .summary-icon.purple {
            background: #e9d5ff;
            color: #8b5cf6;
        }

        .summary-icon.orange {
            background: #fed7aa;
            color: #f97316;
        }

        .summary-value {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1;
        }

        .summary-period {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }

        /* Chart Section */
        .chart-section {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
        }

        .chart-container {
            position: relative;
            height: 400px;
        }

        /* Data Section */
        .data-section {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .data-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .data-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
        }

        /* Table Styles */
        .table-container {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .data-table th {
            background: #f8fafc;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .data-table tbody tr:hover {
            background: #f8fafc;
        }

        /* Status Badges */
        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .status-lunas {
            background: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-belum-lunas {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Mobile Cards */
        .mobile-cards {
            display: none;
        }

        .mobile-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        .mobile-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .mobile-card-title {
            font-weight: 600;
            color: #1e293b;
            font-size: 1rem;
        }

        .mobile-card-date {
            background: #f1f5f9;
            color: #475569;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .mobile-card-body {
            display: grid;
            gap: 0.75rem;
        }

        .mobile-field {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mobile-field-label {
            font-weight: 500;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .mobile-field-value {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.875rem;
            text-align: right;
        }

        /* Loading & Empty States */
        .loading-state,
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6b7280;
        }

        .loading-state i,
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #d1d5db;
        }

        .loading-state i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .mobile-menu-toggle {
                display: block;
            }

            .mobile-overlay.active {
                display: block;
            }

            .page-header {
                padding: 1.5rem;
                flex-direction: column;
                text-align: center;
            }

            .page-title {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1rem 0.5rem;
                padding-top: 5rem;
            }

            .page-header {
                margin: 0 -0.5rem 1rem;
                border-radius: 0;
                padding: 1rem;
            }

            .filter-section {
                margin: 0 -0.5rem 1rem;
                border-radius: 0;
            }

            .chart-section {
                margin: 0 -0.5rem 1rem;
                border-radius: 0;
            }

            .data-section {
                margin: 0 -0.5rem;
                border-radius: 0;
            }

            .report-summary {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
                margin: 0 -0.5rem 1rem;
            }

            .summary-card {
                margin: 0 0.5rem;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .table-container {
                display: none;
            }

            .mobile-cards {
                display: block;
                padding: 1rem;
            }

            .data-header {
                padding: 1rem;
            }

            .chart-container {
                height: 250px;
            }
        }

        @media (max-width: 480px) {
            .report-summary {
                grid-template-columns: 1fr;
            }

            .mobile-field {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.25rem;
            }

            .mobile-field-value {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" onclick="closeMobileMenu()"></div>

    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>Sewa Gedung</h2>
                <p>Admin Panel</p>
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
                        <a href="data_penyewa.php" class="nav-link">
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
                        <a href="laporan_penyewaan.php" class="nav-link active">
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
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-chart-bar"></i>
                    Laporan Penyewaan
                </h1>
                <button class="btn btn-success" onclick="exportReport()">
                    <i class="fas fa-download"></i>
                    Export Laporan
                </button>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label class="filter-label">Periode</label>
                        <select class="filter-input" id="periodFilter" onchange="changePeriod()">
                            <option value="harian">Harian</option>
                            <option value="mingguan">Mingguan</option>
                            <option value="bulanan" selected>Bulanan</option>
                            <option value="tahunan">Tahunan</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div class="filter-group" id="monthGroup">
                        <label class="filter-label">Bulan</label>
                        <select class="filter-input" id="monthFilter" onchange="filterData()">
                            <option value="">Semua Bulan</option>
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Tahun</label>
                        <select class="filter-input" id="yearFilter" onchange="filterData()">
                        </select>
                    </div>
                    <div class="filter-group" id="dateRangeGroup" style="display: none;">
                        <label class="filter-label">Tanggal Mulai</label>
                        <input type="date" class="filter-input" id="startDate" onchange="filterData()">
                    </div>
                    <div class="filter-group" id="dateRangeGroup2" style="display: none;">
                        <label class="filter-label">Tanggal Selesai</label>
                        <input type="date" class="filter-input" id="endDate" onchange="filterData()">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">&nbsp;</label>
                        <button class="btn btn-primary" onclick="resetFilters()">
                            <i class="fas fa-refresh"></i>
                            Reset Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Report Summary -->
            <div class="report-summary">
                <div class="summary-card">
                    <div class="summary-header">
                        <span class="summary-title">Total Pendapatan</span>
                        <div class="summary-icon green">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div class="summary-value" id="totalRevenue">Rp 0</div>
                    <div class="summary-period">Periode ini</div>
                </div>
                <div class="summary-card">
                    <div class="summary-header">
                        <span class="summary-title">Total Transaksi</span>
                        <div class="summary-icon blue">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                    <div class="summary-value" id="totalTransactions">0</div>
                    <div class="summary-period">Transaksi selesai</div>
                </div>
                <div class="summary-card">
                    <div class="summary-header">
                        <span class="summary-title">Rata-rata per Hari</span>
                        <div class="summary-icon purple">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="summary-value" id="avgDaily">Rp 0</div>
                    <div class="summary-period">Pendapatan harian</div>
                </div>
                <div class="summary-card">
                    <div class="summary-header">
                        <span class="summary-title">Gedung Terpopuler</span>
                        <div class="summary-icon orange">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                    <div class="summary-value" id="popularVenue">-</div>
                    <div class="summary-period">Paling banyak disewa</div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="chart-section">
                <div class="chart-header">
                    <h3 class="chart-title">Grafik Pendapatan</h3>
                    <button class="btn btn-primary" onclick="toggleChartType()">
                        <i class="fas fa-chart-bar"></i>
                        Ubah Grafik
                    </button>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Data Section -->
            <div class="data-section">
                <div class="data-header">
                    <h3 class="data-title">Detail Laporan Penyewaan</h3>
                </div>
                
                <!-- Desktop Table -->
                <div class="table-container">
                    <table class="data-table" id="laporanTable">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Penyewa</th>
                                <th>Acara</th>
                                <th>Gedung</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="loading-state">
                                    <i class="fas fa-spinner"></i>
                                    <br>Memuat data...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Mobile Cards -->
                <div class="mobile-cards" id="mobileCards">
                    <div class="loading-state">
                        <i class="fas fa-spinner"></i>
                        <br>Memuat data...
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let allData = [];
        let chart = null;
        let chartType = 'line';

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadData();
            loadSummary();
            populateYearFilter();
            initChart();
        });

        // Load laporan data
        function loadData() {
            fetch('ajax/get_laporan.php')
                .then(response => response.json())
                .then(data => {
                    allData = data;
                    displayData(data);
                    updateChart(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Gagal memuat data laporan');
                });
        }

        // Load summary statistics
        function loadSummary() {
            fetch('ajax/get_laporan_stats.php')
                .then(response => response.json())
                .then(data => {
                    updateSummary(data);
                })
                .catch(error => console.error('Error loading summary:', error));
        }

        // Update summary display
        function updateSummary(data) {
            document.getElementById('totalRevenue').textContent = 'Rp ' + (parseInt(data.total_revenue || 0)).toLocaleString('id-ID');
            document.getElementById('totalTransactions').textContent = data.total_transactions || 0;
            document.getElementById('avgDaily').textContent = 'Rp ' + (parseInt(data.avg_daily || 0)).toLocaleString('id-ID');
            document.getElementById('popularVenue').textContent = data.popular_venue || '-';
        }

        // Display data in table and mobile cards
        function displayData(data) {
            const tbody = document.querySelector('#laporanTable tbody');
            const mobileContainer = document.querySelector('#mobileCards');
            
            tbody.innerHTML = '';
            mobileContainer.innerHTML = '';
            
            if (!Array.isArray(data)) {
                showError('Format data tidak valid');
                return;
            }
            
            if (data.length === 0) {
                showEmptyState();
                return;
            }

            let mobileCardsHTML = '';
            
            data.forEach(item => {
                const tableRow = createTableRow(item);
                tbody.innerHTML += tableRow;
                
                const mobileCard = createMobileCard(item);
                mobileCardsHTML += mobileCard;
            });
            
            mobileContainer.innerHTML = mobileCardsHTML;
        }

        // Create table row HTML
        function createTableRow(item) {
            const statusClass = getStatusClass(item.status_pembayaran);
            const formattedDate = item.tanggal_acara ? new Date(item.tanggal_acara).toLocaleDateString('id-ID') : 'N/A';
            const formattedPrice = 'Rp ' + parseInt(item.total_biaya || item.total || 0).toLocaleString('id-ID');
            const pendapatan = item.status_pembayaran === 'Lunas' ? (item.total_biaya || item.total || 0) : 0;
            const formattedPendapatan = 'Rp ' + parseInt(pendapatan).toLocaleString('id-ID');
            
            return `
                <tr>
                    <td>${formattedDate}</td>
                    <td>${item.nama_penyewa || 'N/A'}</td>
                    <td>${item.nama_acara || 'N/A'}</td>
                    <td>${getVenueName(item.nama_acara)}</td>
                    <td>${formattedPrice}</td>
                    <td><span class="status-badge ${statusClass}">${item.status_pembayaran || 'Pending'}</span></td>
                    <td>${formattedPendapatan}</td>
                </tr>
            `;
        }

        // Create mobile card HTML
        function createMobileCard(item) {
            const statusClass = getStatusClass(item.status_pembayaran);
            const formattedDate = item.tanggal_acara ? new Date(item.tanggal_acara).toLocaleDateString('id-ID') : 'N/A';
            const formattedPrice = 'Rp ' + parseInt(item.total_biaya || item.total || 0).toLocaleString('id-ID');
            const pendapatan = item.status_pembayaran === 'Lunas' ? (item.total_biaya || item.total || 0) : 0;
            const formattedPendapatan = 'Rp ' + parseInt(pendapatan).toLocaleString('id-ID');
            
            return `
                <div class="mobile-card">
                    <div class="mobile-card-header">
                        <div class="mobile-card-title">${item.nama_acara || 'N/A'}</div>
                        <div class="mobile-card-date">${formattedDate}</div>
                    </div>
                    <div class="mobile-card-body">
                        <div class="mobile-field">
                            <span class="mobile-field-label">Penyewa</span>
                            <span class="mobile-field-value">${item.nama_penyewa || 'N/A'}</span>
                        </div>
                        <div class="mobile-field">
                            <span class="mobile-field-label">Gedung</span>
                            <span class="mobile-field-value">${getVenueName(item.nama_acara)}</span>
                        </div>
                        <div class="mobile-field">
                            <span class="mobile-field-label">Total</span>
                            <span class="mobile-field-value">${formattedPrice}</span>
                        </div>
                        <div class="mobile-field">
                            <span class="mobile-field-label">Status</span>
                            <span class="status-badge ${statusClass}">${item.status_pembayaran || 'Pending'}</span>
                        </div>
                        <div class="mobile-field">
                            <span class="mobile-field-label">Pendapatan</span>
                            <span class="mobile-field-value">${formattedPendapatan}</span>
                        </div>
                    </div>
                </div>
            `;
        }

        // Get venue name from event type
        function getVenueName(acara) {
            if (!acara) return 'Gedung Umum';
            const acaraLower = acara.toLowerCase();
            if (acaraLower.includes('pernikahan')) return 'Gedung Pernikahan';
            if (acaraLower.includes('rapat')) return 'Gedung Rapat';
            if (acaraLower.includes('seminar')) return 'Gedung Seminar';
            return 'Gedung Umum';
        }

        // Get status CSS class
        function getStatusClass(status) {
            if (!status) return 'status-pending';
            const statusLower = status.toLowerCase();
            if (statusLower === 'lunas') return 'status-lunas';
            if (statusLower === 'belum lunas') return 'status-belum-lunas';
            return 'status-pending';
        }

        // Show error state
        function showError(message) {
            const tbody = document.querySelector('#laporanTable tbody');
            const mobileContainer = document.querySelector('#mobileCards');
            
            const errorHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <br>${message}
                </div>
            `;
            
            tbody.innerHTML = `<tr><td colspan="7" class="empty-state"><i class="fas fa-exclamation-triangle"></i><br>${message}</td></tr>`;
            mobileContainer.innerHTML = errorHTML;
        }

        // Show empty state
        function showEmptyState() {
            const tbody = document.querySelector('#laporanTable tbody');
            const mobileContainer = document.querySelector('#mobileCards');
            
            const emptyHTML = `
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <br>Tidak ada data laporan
                </div>
            `;
            
            tbody.innerHTML = `<tr><td colspan="7" class="empty-state"><i class="fas fa-inbox"></i><br>Tidak ada data laporan</td></tr>`;
            mobileContainer.innerHTML = emptyHTML;
        }

        // Initialize chart
        function initChart() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            chart = new Chart(ctx, {
                type: chartType,
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: [],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Pendapatan: Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
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

        // Update chart
        function updateChart(data) {
            if (!chart) return;
            
            // Process data for chart
            const chartData = processChartData(data);
            
            chart.data.labels = chartData.labels;
            chart.data.datasets[0].data = chartData.values;
            chart.update();
        }

        // Process data for chart
        function processChartData(data) {
            // This is a simple example - you should implement proper grouping based on period
            const labels = [];
            const values = [];
            
            // Group by month for now
            const monthlyData = {};
            data.forEach(item => {
                if (item.tanggal_acara && item.status_pembayaran === 'Lunas') {
                    const date = new Date(item.tanggal_acara);
                    const monthKey = date.getFullYear() + '-' + (date.getMonth() + 1);
                    if (!monthlyData[monthKey]) {
                        monthlyData[monthKey] = 0;
                    }
                    monthlyData[monthKey] += parseInt(item.total_biaya || item.total || 0);
                }
            });
            
            Object.keys(monthlyData).sort().forEach(key => {
                labels.push(key);
                values.push(monthlyData[key]);
            });
            
            return { labels, values };
        }

        // Toggle chart type
        function toggleChartType() {
            chartType = chartType === 'line' ? 'bar' : 'line';
            chart.destroy();
            initChart();
            updateChart(allData);
        }

        // Change period
        function changePeriod() {
            const period = document.getElementById('periodFilter').value;
            const dateGroup1 = document.getElementById('dateRangeGroup');
            const dateGroup2 = document.getElementById('dateRangeGroup2');
            const monthGroup = document.getElementById('monthGroup');
            
            if (period === 'custom') {
                dateGroup1.style.display = 'block';
                dateGroup2.style.display = 'block';
                monthGroup.style.display = 'none';
            } else {
                dateGroup1.style.display = 'none';
                dateGroup2.style.display = 'none';
                monthGroup.style.display = 'block';
            }
            
            filterData();
        }

        // Filter data
        function filterData() {
            // Implement filtering logic here
            console.log('Filter data based on current filters');
            displayData(allData);
        }

        // Reset filters
        function resetFilters() {
            document.getElementById('periodFilter').value = 'bulanan';
            document.getElementById('monthFilter').value = '';
            document.getElementById('yearFilter').value = '';
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            changePeriod();
            displayData(allData);
        }

        // Populate year filter
        function populateYearFilter() {
            const yearSelect = document.getElementById('yearFilter');
            const currentYear = new Date().getFullYear();
            
            yearSelect.innerHTML = '<option value="">Semua Tahun</option>';
            for (let year = currentYear; year >= currentYear - 5; year--) {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                if (year === currentYear) option.selected = true;
                yearSelect.appendChild(option);
            }
        }

        // Export report
        function exportReport() {
            alert('Export laporan penyewaan');
        }

        // Mobile menu functions
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
