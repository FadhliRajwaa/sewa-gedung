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

        .btn-primary {
            background: #8B4513;
            color: white;
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
        }

        .table-title {
            color: #8B4513;
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .table-responsive {
            overflow-x: auto;
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

        .empty-state, .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-state i, .loading i {
            font-size: 48px;
            margin-bottom: 16px;
            color: #dee2e6;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
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
                        <a href="data_penyewa.php" class="nav-link">
                            <i class="fas fa-users"></i>
                            Data Penyewa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="data_pemesanan.php" class="nav-link active">
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
                <h1>Data Pemesanan</h1>
            </div>

            <!-- Stats Section -->
            <div class="stats-section">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number" id="totalPemesanan">0</div>
                        <div class="stat-label">Total Pemesanan</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" id="lunasCount">0</div>
                        <div class="stat-label">Pembayaran Lunas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" id="belumLunasCount">0</div>
                        <div class="stat-label">Belum Lunas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number" id="noPaymentCount">0</div>
                        <div class="stat-label">Belum Bayar</div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="table-section">
                <div class="table-header">
                    <h2 class="table-title">Daftar Pemesanan</h2>
                </div>
                
                <div class="table-responsive">
                    <table id="pemesananTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Penyewa</th>
                                <th>Acara</th>
                                <th>Tanggal Sewa</th>
                                <th>Total</th>
                                <th>Status Pembayaran</th>
                                <th>Metode Pembayaran</th>
                                <th>Tanggal Pesan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="9" class="loading">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    Memuat data...
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
            fetch('ajax/get_pemesanan.php')
                .then(response => response.json())
                .then(data => {
                    allData = data;
                    displayData(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.querySelector('#pemesananTable tbody').innerHTML = 
                        '<tr><td colspan="9" class="empty-state"><i class="fas fa-exclamation-triangle"></i><br>Error loading data</td></tr>';
                });
        }

        function loadStats() {
            fetch('ajax/get_pemesanan_stats.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalPemesanan').textContent = data.total || 0;
                    document.getElementById('lunasCount').textContent = data.lunas || 0;
                    document.getElementById('belumLunasCount').textContent = data.belum_lunas || 0;
                    document.getElementById('noPaymentCount').textContent = data.no_payment || 0;
                })
                .catch(error => console.error('Error loading stats:', error));
        }

        function displayData(data) {
            const tbody = document.querySelector('#pemesananTable tbody');
            tbody.innerHTML = '';
            
            if (data.error) {
                tbody.innerHTML = '<tr><td colspan="9" class="empty-state"><i class="fas fa-exclamation-triangle"></i><br>Error: ' + data.error + '</td></tr>';
                return;
            }
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="empty-state"><i class="fas fa-calendar-times"></i><br>Belum ada data pemesanan</td></tr>';
                return;
            }
            
            data.forEach(pemesanan => {
                const statusClass = pemesanan.status_pembayaran === 'Lunas' ? 'badge-success' : 'badge-warning';
                const formattedDate = pemesanan.tanggal_pesan ? new Date(pemesanan.tanggal_pesan).toLocaleDateString('id-ID') : 'N/A';
                const formattedPrice = parseInt(pemesanan.total || 0).toLocaleString('id-ID');
                
                const row = `
                    <tr>
                        <td>${pemesanan.id_pemesanan}</td>
                        <td>${pemesanan.nama_penyewa || 'N/A'}</td>
                        <td>${pemesanan.nama_acara || 'N/A'}</td>
                        <td>${pemesanan.tanggal_sewa}</td>
                        <td>Rp ${formattedPrice}</td>
                        <td>
                            <span class="badge ${statusClass}">
                                ${pemesanan.status_pembayaran || 'Belum Bayar'}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-info">
                                ${pemesanan.metode_pembayaran || 'N/A'}
                            </span>
                        </td>
                        <td>${formattedDate}</td>
                        <td>
                            <a href="pemesanan_view.php?id=${pemesanan.id_pemesanan}" class="btn btn-info btn-sm" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }
    </script>
</body>
</html>
