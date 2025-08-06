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

        /* Sidebar - Same as dashboard */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
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
            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
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

            .table-header {
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
                <h1>Data Penyewa</h1>
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
            tbody.innerHTML = '';
            
            if (data.error) {
                tbody.innerHTML = '<tr><td colspan="7" class="empty-state"><i class="fas fa-exclamation-triangle"></i><br>Error: ' + data.error + '</td></tr>';
                return;
            }
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="empty-state"><i class="fas fa-users"></i><br>Belum ada data penyewa</td></tr>';
                return;
            }
            
            data.forEach(penyewa => {
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
            });
        }

        function deletePenyewa(id) {
            if (confirm('Apakah Anda yakin ingin menghapus penyewa ini?')) {
                fetch('ajax/delete_penyewa.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + id
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
    </script>
</body>
</html>
