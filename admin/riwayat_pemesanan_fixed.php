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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: #2c3e50;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar.mobile-hidden {
            transform: translateX(-100%);
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
            transition: all 0.3s ease;
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

        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: #8B4513;
            border: none;
            color: white;
            padding: 12px 15px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3);
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle:hover {
            background: #A0522D;
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(139, 69, 19, 0.4);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            color: #666;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #8B4513;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #999;
            font-size: 12px;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
        }

        .filter-row {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #8B4513;
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-primary {
            background: #8B4513;
            color: white;
        }

        .btn-primary:hover {
            background: #A0522D;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3);
        }

        /* Table Container */
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-header {
            background: #f8f9fa;
            padding: 20px 25px;
            border-bottom: 1px solid #e1e5e9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h2 {
            color: #333;
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .data-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #555;
            border-bottom: 2px solid #e1e5e9;
            font-size: 14px;
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #e1e5e9;
            font-size: 14px;
            color: #666;
        }

        .data-table tbody tr:hover {
            background: #f8f9fa;
        }

        /* Status Badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }

        /* Mobile Cards */
        .mobile-cards {
            display: none;
        }

        .mobile-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #8B4513;
        }

        .mobile-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e1e5e9;
        }

        .mobile-card-title {
            font-weight: 600;
            color: #333;
            font-size: 16px;
        }

        .mobile-card-date {
            background: #f8f9fa;
            color: #666;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
        }

        .mobile-card-body {
            display: grid;
            gap: 10px;
        }

        .mobile-field {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mobile-field-label {
            font-weight: 500;
            color: #666;
            font-size: 14px;
        }

        .mobile-field-value {
            font-weight: 600;
            color: #333;
            font-size: 14px;
            text-align: right;
        }

        /* Loading & Empty States */
        .loading-state,
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .loading-state i,
        .empty-state i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 15px;
            display: block;
        }

        .loading-state i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-show {
                transform: translateX(0);
            }

            .mobile-menu-toggle {
                display: block;
            }

            .page-header {
                padding: 15px 20px;
                margin-top: 60px;
            }

            .page-header h1 {
                font-size: 20px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-value {
                font-size: 24px;
            }

            .filter-row {
                flex-direction: column;
                gap: 15px;
            }

            .filter-group {
                min-width: auto;
            }

            .table-container {
                overflow-x: auto;
                display: none;
            }

            .mobile-cards {
                display: block;
                padding: 20px;
            }

            .mobile-field {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .mobile-field-value {
                text-align: left;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .mobile-card {
                padding: 15px;
            }

            .page-header {
                padding: 15px;
            }
        }

        /* Sidebar Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-overlay.active {
            display: block;
        }

        @media (max-width: 768px) {
            .sidebar {
                z-index: 1000;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" onclick="closeMobileMenu()"></div>

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="logo">
                <h1>Sewa Gedung</h1>
                <p style="color: rgba(255,255,255,0.7); font-size: 14px; margin-top: 5px;">Admin Panel</p>
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
                        <a href="riwayat_pemesanan.php" class="nav-link active">
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
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1><i class="fas fa-history"></i> Riwayat Pemesanan</h1>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Riwayat</h3>
                    <div class="stat-value" id="totalRiwayat">0</div>
                    <div class="stat-label">Pemesanan Selesai</div>
                </div>
                <div class="stat-card">
                    <h3>Bulan Ini</h3>
                    <div class="stat-value" id="bulanIni">0</div>
                    <div class="stat-label">Pemesanan</div>
                </div>
                <div class="stat-card">
                    <h3>Total Pendapatan</h3>
                    <div class="stat-value" id="totalPendapatan">Rp 0</div>
                    <div class="stat-label">Dari Pemesanan</div>
                </div>
                <div class="stat-card">
                    <h3>Rata-rata</h3>
                    <div class="stat-value" id="rataRata">Rp 0</div>
                    <div class="stat-label">Per Pemesanan</div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Bulan</label>
                        <select id="filterBulan" onchange="filterData()">
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
                        <label>Tahun</label>
                        <select id="filterTahun" onchange="filterData()">
                            <option value="">Semua Tahun</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Status</label>
                        <select id="filterStatus" onchange="filterData()">
                            <option value="">Semua Status</option>
                            <option value="completed">Selesai</option>
                            <option value="confirmed">Dikonfirmasi</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button class="btn btn-primary" onclick="resetFilter()">
                            <i class="fas fa-refresh"></i>
                            Reset Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="table-container">
                <div class="table-header">
                    <h2>Data Riwayat Pemesanan</h2>
                </div>
                <table class="data-table" id="riwayatTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Penyewa</th>
                            <th>Acara</th>
                            <th>Total Biaya</th>
                            <th>Status</th>
                            <th>Aksi</th>
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
    </div>

    <script>
        let allData = [];

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadData();
            loadStats();
        });

        // Load riwayat data
        function loadData() {
            fetch('ajax/get_riwayat.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showError(data.error);
                        return;
                    }
                    allData = data;
                    displayData(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Gagal memuat data riwayat');
                });
        }

        // Load statistics
        function loadStats() {
            fetch('ajax/get_riwayat_stats.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Stats error:', data.error);
                        return;
                    }
                    updateStats(data);
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                });
        }

        // Update statistics
        function updateStats(data) {
            document.getElementById('totalRiwayat').textContent = data.total_riwayat || 0;
            document.getElementById('bulanIni').textContent = data.bulan_ini || 0;
            document.getElementById('totalPendapatan').textContent = 'Rp ' + (parseInt(data.total_pendapatan || 0)).toLocaleString('id-ID');
            document.getElementById('rataRata').textContent = 'Rp ' + (parseInt(data.rata_rata || 0)).toLocaleString('id-ID');
        }

        // Display data in table and mobile cards
        function displayData(data) {
            const tbody = document.querySelector('#riwayatTable tbody');
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
            const statusClass = getStatusClass(item.status_pemesanan);
            const formattedDate = item.tanggal_acara ? new Date(item.tanggal_acara).toLocaleDateString('id-ID') : 'N/A';
            const formattedPrice = 'Rp ' + parseInt(item.total_biaya || item.harga_paket || 0).toLocaleString('id-ID');
            
            return `
                <tr>
                    <td>#${item.id || 'N/A'}</td>
                    <td>${formattedDate}</td>
                    <td>${item.nama_penyewa || item.nama || 'N/A'}</td>
                    <td>${item.nama_acara || 'N/A'}</td>
                    <td>${formattedPrice}</td>
                    <td><span class="status-badge ${statusClass}">${item.status_pemesanan || item.status_pembayaran || 'Pending'}</span></td>
                    <td>
                        <button class="btn btn-primary" onclick="viewDetail(${item.id})" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        // Create mobile card HTML
        function createMobileCard(item) {
            const statusClass = getStatusClass(item.status_pemesanan);
            const formattedDate = item.tanggal_acara ? new Date(item.tanggal_acara).toLocaleDateString('id-ID') : 'N/A';
            const formattedPrice = 'Rp ' + parseInt(item.total_biaya || item.harga_paket || 0).toLocaleString('id-ID');
            
            return `
                <div class="mobile-card">
                    <div class="mobile-card-header">
                        <div class="mobile-card-title">${item.nama_acara || 'N/A'}</div>
                        <div class="mobile-card-date">${formattedDate}</div>
                    </div>
                    <div class="mobile-card-body">
                        <div class="mobile-field">
                            <span class="mobile-field-label">ID</span>
                            <span class="mobile-field-value">#${item.id || 'N/A'}</span>
                        </div>
                        <div class="mobile-field">
                            <span class="mobile-field-label">Penyewa</span>
                            <span class="mobile-field-value">${item.nama_penyewa || item.nama || 'N/A'}</span>
                        </div>
                        <div class="mobile-field">
                            <span class="mobile-field-label">Total Biaya</span>
                            <span class="mobile-field-value">${formattedPrice}</span>
                        </div>
                        <div class="mobile-field">
                            <span class="mobile-field-label">Status</span>
                            <span class="status-badge ${statusClass}">${item.status_pemesanan || item.status_pembayaran || 'Pending'}</span>
                        </div>
                    </div>
                </div>
            `;
        }

        // Get status CSS class
        function getStatusClass(status) {
            if (!status) return 'status-pending';
            const statusLower = status.toLowerCase();
            if (statusLower === 'completed' || statusLower === 'selesai') return 'status-completed';
            if (statusLower === 'confirmed' || statusLower === 'dikonfirmasi') return 'status-confirmed';
            if (statusLower === 'cancelled' || statusLower === 'dibatalkan') return 'status-cancelled';
            if (statusLower === 'lunas') return 'status-confirmed';
            return 'status-pending';
        }

        // Show error state
        function showError(message) {
            const tbody = document.querySelector('#riwayatTable tbody');
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
            const tbody = document.querySelector('#riwayatTable tbody');
            const mobileContainer = document.querySelector('#mobileCards');
            
            const emptyHTML = `
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <br>Tidak ada data riwayat
                </div>
            `;
            
            tbody.innerHTML = `<tr><td colspan="7" class="empty-state"><i class="fas fa-inbox"></i><br>Tidak ada data riwayat</td></tr>`;
            mobileContainer.innerHTML = emptyHTML;
        }

        // Filter data
        function filterData() {
            const bulan = document.getElementById('filterBulan').value;
            const tahun = document.getElementById('filterTahun').value;
            const status = document.getElementById('filterStatus').value;
            
            let filteredData = allData;
            
            if (bulan) {
                filteredData = filteredData.filter(item => {
                    if (item.tanggal_acara) {
                        const date = new Date(item.tanggal_acara);
                        return (date.getMonth() + 1) == bulan;
                    }
                    return false;
                });
            }
            
            if (tahun) {
                filteredData = filteredData.filter(item => {
                    if (item.tanggal_acara) {
                        const date = new Date(item.tanggal_acara);
                        return date.getFullYear() == tahun;
                    }
                    return false;
                });
            }
            
            if (status) {
                filteredData = filteredData.filter(item => {
                    const itemStatus = (item.status_pemesanan || item.status_pembayaran || '').toLowerCase();
                    return itemStatus === status.toLowerCase();
                });
            }
            
            displayData(filteredData);
        }

        // Reset filter
        function resetFilter() {
            document.getElementById('filterBulan').value = '';
            document.getElementById('filterTahun').value = '';
            document.getElementById('filterStatus').value = '';
            displayData(allData);
        }

        // View detail
        function viewDetail(id) {
            alert('Fitur detail akan segera tersedia untuk ID: ' + id);
        }

        // Mobile menu functions
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            sidebar.classList.toggle('mobile-show');
            overlay.classList.toggle('active');
        }

        function closeMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            sidebar.classList.remove('mobile-show');
            overlay.classList.remove('active');
        }
    </script>
</body>
</html>
