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
    <title>Data Penyewa - Admin</title>
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
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-header {
            text-align: center;
            padding: 0 2rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 2rem;
        }

        .sidebar-header h2 {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
        }

        /* Mobile Menu */
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

        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            backdrop-filter: blur(3px);
        }

        .mobile-overlay.active {
            display: block;
        }
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

        .btn-primary {
            background: #8B4513;
            color: white;
        }

        .btn-primary:hover {
            background: #6d3410;
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .btn-warning {
            background: #ffc107;
            color: #212529;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        /* Stats Section */
        .stats-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #8B4513;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        /* Table Section */
        .table-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-header {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title {
            color: #8B4513;
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
            position: sticky;
            top: 0;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
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

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            color: #dee2e6;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
                transition: transform 0.3s ease;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 10px;
                padding-top: 70px;
            }

            .page-header {
                padding: 15px;
                margin-bottom: 15px;
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .page-header h1 {
                font-size: 18px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .stat-item {
                padding: 10px;
            }

            .stat-number {
                font-size: 20px;
            }

            .stat-label {
                font-size: 12px;
            }

            .table-section {
                margin: 0 -10px;
                border-radius: 0;
            }

            .table-header {
                padding: 15px;
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .data-table {
                font-size: 12px;
            }

            .data-table th,
            .data-table td {
                padding: 8px 4px;
                min-width: 60px;
            }

            .data-table th:nth-child(1),
            .data-table td:nth-child(1) {
                width: 40px;
            }

            .data-table th:nth-child(2),
            .data-table td:nth-child(2) {
                min-width: 80px;
            }

            .data-table th:nth-child(3),
            .data-table td:nth-child(3) {
                min-width: 100px;
            }

            .data-table th:nth-child(4),
            .data-table td:nth-child(4) {
                min-width: 80px;
            }

            .data-table th:nth-child(6),
            .data-table td:nth-child(6) {
                min-width: 70px;
            }

            .btn {
                padding: 6px 8px;
                font-size: 11px;
            }

            .badge {
                font-size: 10px;
                padding: 3px 6px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 8px;
                padding-top: 65px;
            }

            .page-header {
                padding: 12px;
            }

            .page-header h1 {
                font-size: 16px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .stats-section {
                padding: 12px;
            }

            .data-table {
                font-size: 11px;
            }

            .data-table th,
            .data-table td {
                padding: 6px 3px;
            }

            .btn {
                padding: 4px 6px;
                font-size: 10px;
            }

        /* Mobile Utility Classes */
        .mobile-card {
            display: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
                transition: transform 0.3s ease;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 10px;
                padding-top: 70px;
            }

            .page-header {
                padding: 15px;
                margin-bottom: 15px;
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .page-header h1 {
                font-size: 18px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .stat-item {
                padding: 10px;
            }

            .stat-number {
                font-size: 20px;
            }

            .stat-label {
                font-size: 12px;
            }

            .table-section {
                margin: 0 -10px;
                border-radius: 0;
            }

            .table-header {
                padding: 15px;
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            /* Hide table on mobile, show cards */
            .table-responsive table {
                display: none;
            }

            .mobile-card {
                display: block;
            }
            
            .mobile-card-item {
                background: white;
                margin-bottom: 15px;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                border: 1px solid #dee2e6;
                overflow: hidden;
            }

            .mobile-card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 15px;
                background: #f8f9fa;
                border-bottom: 1px solid #dee2e6;
            }

            .mobile-card-title {
                font-weight: 600;
                color: #8B4513;
                font-size: 16px;
                margin: 0;
            }
            
            .mobile-card-id {
                background: #8B4513;
                color: white;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 600;
            }

            .mobile-card-body {
                padding: 15px;
                display: grid;
                gap: 12px;
            }

            .mobile-field {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .mobile-field:last-child {
                border-bottom: none;
            }

            .mobile-field-label {
                font-size: 14px;
                color: #666;
                font-weight: 500;
                flex: 1;
            }

            .mobile-field-value {
                font-size: 14px;
                color: #333;
                flex: 1;
                text-align: right;
            }
            
            .mobile-actions {
                display: flex;
                gap: 8px;
                justify-content: center;
                padding: 15px;
                background: #fafafa;
                border-top: 1px solid #dee2e6;
            }
        }
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 4px 0;
            }

            .mobile-field-label {
                font-size: 13px;
                color: #666;
                font-weight: 500;
                flex: 1;
            }

            .mobile-field-value {
                font-size: 13px;
                color: #333;
                flex: 2;
                text-align: right;
            }
            
            .mobile-actions {
                display: flex;
                gap: 8px;
                justify-content: center;
                border-top: 1px solid #eee;
                padding-top: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Mobile Menu Button -->
        <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Mobile Overlay -->
        <div class="mobile-overlay" onclick="closeMobileMenu()"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-building"></i> Admin Panel</h2>
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
                <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1><i class="fas fa-users"></i> Data Penyewa</h1>
                <a href="penyewa_add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Tambah Penyewa
                </a>
            </div>

            <!-- Stats Section -->
            <div class="stats-section">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number" id="totalPenyewa">0</div>
                        <div class="stat-label">Total Penyewa</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" id="individuCount">0</div>
                        <div class="stat-label">Individu</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" id="instansiCount">0</div>
                        <div class="stat-label">Instansi</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" id="verifiedCount">0</div>
                        <div class="stat-label">Email Terverifikasi</div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="table-section">
                <div class="table-header">
                    <h2 class="table-title">Daftar Penyewa</h2>
                </div>
                
                <div class="table-responsive">
                    <table id="penyewaTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th>Tipe</th>
                                <th>Status Email</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="loading">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    Memuat data...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <!-- Mobile Cards Container -->
                    <div class="mobile-card" id="mobileCards">
                        <div class="loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            Memuat data...
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let allData = [];

        // Load data on page ready
        document.addEventListener('DOMContentLoaded', function() {
            loadData();
            loadStats();
        });

        function loadData() {
            fetch('ajax/get_penyewa.php')
                .then(response => response.json())
                .then(data => {
                    allData = data;
                    displayData(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.querySelector('#penyewaTable tbody').innerHTML = 
                        '<tr><td colspan="7" class="empty-state"><i class="fas fa-exclamation-triangle"></i><br>Error loading data</td></tr>';
                });
        }

        function loadStats() {
            fetch('ajax/get_penyewa_stats.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalPenyewa').textContent = data.total || 0;
                    document.getElementById('individuCount').textContent = data.individu || 0;
                    document.getElementById('instansiCount').textContent = data.instansi || 0;
                    document.getElementById('verifiedCount').textContent = data.verified || 0;
                })
                .catch(error => console.error('Error loading stats:', error));
        }

        function displayData(data) {
            const tbody = document.querySelector('#penyewaTable tbody');
            const mobileContainer = document.querySelector('#mobileCards');
            
            tbody.innerHTML = '';
            if (mobileContainer) mobileContainer.innerHTML = '';
            
            if (data.error) {
                const errorMsg = '<tr><td colspan="7" class="empty-state"><i class="fas fa-exclamation-triangle"></i><br>Error: ' + data.error + '</td></tr>';
                tbody.innerHTML = errorMsg;
                if (mobileContainer) mobileContainer.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><br>Error: ' + data.error + '</div>';
                return;
            }
            
            if (data.length === 0) {
                const emptyMsg = '<tr><td colspan="7" class="empty-state"><i class="fas fa-users"></i><br>Belum ada data penyewa</td></tr>';
                tbody.innerHTML = emptyMsg;
                if (mobileContainer) mobileContainer.innerHTML = '<div class="empty-state"><i class="fas fa-users"></i><br>Belum ada data penyewa</div>';
                return;
            }
            
            let mobileCardsHTML = '';
            
            data.forEach(penyewa => {
                // Desktop table row
                const row = `
                    <tr>
                        <td>${penyewa.id_penyewa}</td>
                        <td>${penyewa.nama || 'N/A'}</td>
                        <td>${penyewa.email}</td>
                        <td>${penyewa.telepon}</td>
                        <td>
                            <span class="badge ${penyewa.jenis === 'instansi' ? 'badge-info' : 'badge-secondary'}">
                                ${penyewa.jenis}
                            </span>
                        </td>
                        <td>
                            <span class="badge ${penyewa.email_terverifikasi ? 'badge-success' : 'badge-warning'}">
                                ${penyewa.email_terverifikasi ? 'Terverifikasi' : 'Belum Verifikasi'}
                            </span>
                        </td>
                        <td>
                            <a href="penyewa_view.php?id=${penyewa.id_penyewa}" class="btn btn-info btn-sm" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="penyewa_edit.php?id=${penyewa.id_penyewa}" class="btn btn-warning btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deletePenyewa(${penyewa.id_penyewa})" class="btn btn-danger btn-sm" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
                
                // Mobile card
                const mobileCard = `
                    <div class="mobile-card-item">
                        <div class="mobile-card-header">
                            <span class="mobile-card-title">${penyewa.nama || 'N/A'}</span>
                            <span class="mobile-card-id">ID: ${penyewa.id_penyewa}</span>
                        </div>
                        <div class="mobile-card-body">
                            <div class="mobile-field">
                                <span class="mobile-field-label">Email:</span>
                                <span class="mobile-field-value">${penyewa.email}</span>
                            </div>
                            <div class="mobile-field">
                                <span class="mobile-field-label">Telepon:</span>
                                <span class="mobile-field-value">${penyewa.telepon}</span>
                            </div>
                            <div class="mobile-field">
                                <span class="mobile-field-label">Tipe:</span>
                                <span class="badge ${penyewa.jenis === 'instansi' ? 'badge-info' : 'badge-secondary'}">${penyewa.jenis}</span>
                            </div>
                            <div class="mobile-field">
                                <span class="mobile-field-label">Email:</span>
                                <span class="badge ${penyewa.email_terverifikasi ? 'badge-success' : 'badge-warning'}">
                                    ${penyewa.email_terverifikasi ? 'Terverifikasi' : 'Belum Verifikasi'}
                                </span>
                            </div>
                        </div>
                        <div class="mobile-actions">
                            <a href="penyewa_view.php?id=${penyewa.id_penyewa}" class="btn btn-info btn-sm" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="penyewa_edit.php?id=${penyewa.id_penyewa}" class="btn btn-warning btn-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deletePenyewa(${penyewa.id_penyewa})" class="btn btn-danger btn-sm" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                mobileCardsHTML += mobileCard;
            });
            
            if (mobileContainer) {
                mobileContainer.innerHTML = mobileCardsHTML;
            }
        }

        function deletePenyewa(id) {
            if (confirm('Apakah Anda yakin ingin menghapus penyewa ini?')) {
                fetch('ajax/delete_penyewa.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Penyewa berhasil dihapus');
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

        // Mobile Menu Functions
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
        
        // Close mobile menu when clicking on nav links
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-item a');
            navLinks.forEach(link => {
                link.addEventListener('click', closeMobileMenu);
            });
        });
    </script>
</body>
</html>
