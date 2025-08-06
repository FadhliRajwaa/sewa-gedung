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

        .search-box {
            position: relative;
            max-width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 14px;
            background: var(--white);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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

            .table-header {
                padding: 20px;
                flex-direction: column;
                gap: 16px;
            }

            .search-box {
                max-width: 100%;
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
                    <a href="data_penyewa.php" class="nav-link active">
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
                <h1 class="page-title">Data Penyewa</h1>
                <a href="penyewa_add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Tambah Penyewa
                </a>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number" id="totalPenyewa">0</div>
                    <div class="stat-label">Total Penyewa</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="stat-number" id="individuCount">0</div>
                    <div class="stat-label">Individu</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-number" id="instansiCount">0</div>
                    <div class="stat-label">Instansi</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon accent">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number" id="verifiedCount">0</div>
                    <div class="stat-label">Email Terverifikasi</div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="table-container">
                <div class="table-header">
                    <h3 class="table-title">Daftar Penyewa</h3>
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input" placeholder="Cari penyewa..." id="searchInput">
                    </div>
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
                            <?php
                            // Query data penyewa sesuai struktur database SQL
                            $query = "SELECT * FROM penyewa ORDER BY id_penyewa DESC";
                            $result = mysqli_query($conn, $query);
                            
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $nama = ($row['tipe_penyewa'] == 'instansi') ? $row['nama_instansi'] : $row['nama_lengkap'];
                                    $email_status = $row['email_terverifikasi'] ? 'Terverifikasi' : 'Belum Verifikasi';
                                    $email_class = $row['email_terverifikasi'] ? 'badge-success' : 'badge-warning';
                                    $tipe_class = ($row['tipe_penyewa'] == 'instansi') ? 'badge-info' : 'badge-primary';
                                    
                                    echo "<tr>";
                                    echo "<td><strong>#{$row['id_penyewa']}</strong></td>";
                                    echo "<td>{$nama}</td>";
                                    echo "<td>{$row['email']}</td>";
                                    echo "<td>{$row['no_telepon']}</td>";
                                    echo "<td><span class='badge {$tipe_class}'>" . ucfirst($row['tipe_penyewa']) . "</span></td>";
                                    echo "<td><span class='badge {$email_class}'>{$email_status}</span></td>";
                                    echo "<td>";
                                    echo "<button class='btn-action btn-detail' onclick='viewDetail({$row['id_penyewa']})' title='Detail'>";
                                    echo "<i class='fas fa-eye'></i>";
                                    echo "</button>";
                                    echo "<button class='btn-action btn-edit' onclick='editPenyewa({$row['id_penyewa']})' title='Edit'>";
                                    echo "<i class='fas fa-edit'></i>";
                                    echo "</button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr>";
                                echo "<td colspan='7' class='empty-state'>";
                                echo "<div class='empty-icon'><i class='fas fa-users'></i></div>";
                                echo "<div class='empty-text'>Belum ada data penyewa</div>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="mobile-cards" id="mobileCards">
                    <?php
                    // Reset pointer untuk mobile cards
                    mysqli_data_seek($result, 0);
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $nama = ($row['tipe_penyewa'] == 'instansi') ? $row['nama_instansi'] : $row['nama_lengkap'];
                            $email_status = $row['email_terverifikasi'] ? 'Terverifikasi' : 'Belum Verifikasi';
                            $email_class = $row['email_terverifikasi'] ? 'badge-success' : 'badge-warning';
                            $tipe_class = ($row['tipe_penyewa'] == 'instansi') ? 'badge-info' : 'badge-primary';
                            
                            echo "<div class='mobile-card'>";
                            echo "<div class='mobile-card-header'>";
                            echo "<h4>#{$row['id_penyewa']} - {$nama}</h4>";
                            echo "<span class='badge {$tipe_class}'>" . ucfirst($row['tipe_penyewa']) . "</span>";
                            echo "</div>";
                            echo "<div class='mobile-card-content'>";
                            echo "<p><strong>Email:</strong> {$row['email']}</p>";
                            echo "<p><strong>Telepon:</strong> {$row['no_telepon']}</p>";
                            echo "<p><strong>Status Email:</strong> <span class='badge {$email_class}'>{$email_status}</span></p>";
                            if ($row['alamat']) {
                                echo "<p><strong>Alamat:</strong> {$row['alamat']}</p>";
                            }
                            if ($row['nik']) {
                                echo "<p><strong>NIK:</strong> {$row['nik']}</p>";
                            }
                            echo "</div>";
                            echo "<div class='mobile-card-actions'>";
                            echo "<button class='btn-action btn-detail' onclick='viewDetail({$row['id_penyewa']})'>";
                            echo "<i class='fas fa-eye'></i> Detail";
                            echo "</button>";
                            echo "<button class='btn-action btn-edit' onclick='editPenyewa({$row['id_penyewa']})'>";
                            echo "<i class='fas fa-edit'></i> Edit";
                            echo "</button>";
                            echo "</div>";
                            echo "</div>";
                        }
                    } else {
                        echo "<div class='empty-state'>";
                        echo "<div class='empty-icon'><i class='fas fa-users'></i></div>";
                        echo "<div class='empty-text'>Belum ada data penyewa</div>";
                        echo "</div>";
                    }
                    ?>
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
            setupSearch();
        });

        function loadData() {
            fetch('ajax/get_penyewa.php')
                .then(response => response.json())
                .then(data => {
                    allData = data;
                    filteredData = data;
                    displayData(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    displayError();
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
            data.forEach(penyewa => {
                tableHTML += `
                    <tr>
                        <td><strong>#${penyewa.id_penyewa}</strong></td>
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
            });
            tbody.innerHTML = tableHTML;

            // Mobile cards
            let mobileHTML = '';
            data.forEach(penyewa => {
                mobileHTML += `
                    <div class="mobile-card">
                        <div class="mobile-card-header">
                            <div class="mobile-card-title">${penyewa.nama || 'N/A'}</div>
                            <div class="mobile-card-id">#${penyewa.id_penyewa}</div>
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
                                <span class="badge ${penyewa.jenis === 'instansi' ? 'badge-info' : 'badge-secondary'}">
                                    ${penyewa.jenis}
                                </span>
                            </div>
                            <div class="mobile-field">
                                <span class="mobile-field-label">Status:</span>
                                <span class="badge ${penyewa.email_terverifikasi ? 'badge-success' : 'badge-warning'}">
                                    ${penyewa.email_terverifikasi ? 'Terverifikasi' : 'Belum Verifikasi'}
                                </span>
                            </div>
                        </div>
                        <div class="mobile-actions">
                            <a href="penyewa_view.php?id=${penyewa.id_penyewa}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="penyewa_edit.php?id=${penyewa.id_penyewa}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="deletePenyewa(${penyewa.id_penyewa})" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            mobileContainer.innerHTML = mobileHTML;
        }

        function displayError(message = 'Error loading data') {
            const tbody = document.querySelector('#penyewaTable tbody');
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
            const tbody = document.querySelector('#penyewaTable tbody');
            const mobileContainer = document.querySelector('#mobileCards');
            
            const emptyHTML = `
                <tr><td colspan="7" class="empty-state">
                    <i class="fas fa-users"></i><br>
                    Belum ada data penyewa
                </td></tr>
            `;
            
            tbody.innerHTML = emptyHTML;
            mobileContainer.innerHTML = `<div class="empty-state">
                <i class="fas fa-users"></i><br>
                Belum ada data penyewa
            </div>`;
        }

        function setupSearch() {
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                
                if (query === '') {
                    filteredData = allData;
                } else {
                    filteredData = allData.filter(penyewa => 
                        penyewa.nama?.toLowerCase().includes(query) ||
                        penyewa.email?.toLowerCase().includes(query) ||
                        penyewa.telepon?.includes(query) ||
                        penyewa.jenis?.toLowerCase().includes(query)
                    );
                }
                
                displayData(filteredData);
            });
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
