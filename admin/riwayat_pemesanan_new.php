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
            min-width: 1200px;
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
                        <label class="filter-label">Tanggal Acara</label>
                        <input type="date" class="filter-input" id="endDate" onchange="applyFilter()">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Tipe Penyewa</label>
                        <select class="filter-input" id="tipeFilter" onchange="applyFilter()">
                            <option value="">Semua Tipe</option>
                            <option value="individu">Umum</option>
                            <option value="instansi">Instansi</option>
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
                    <h3 class="table-title">Riwayat Pemesanan</h3>
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
                            // Query riwayat pemesanan dengan JOIN semua tabel terkait
                            $query = "SELECT 
                                        p.id_pemesanan,
                                        p.id_penyewa,
                                        p.tanggal_sewa,
                                        p.tanggal_selesai,
                                        p.durasi,
                                        p.kebutuhan_tambahan,
                                        p.total,
                                        p.metode_pembayaran,
                                        p.tanggal_pesan,
                                        p.tipe_pesanan,
                                        CASE 
                                            WHEN py.tipe_penyewa = 'instansi' THEN py.nama_instansi
                                            ELSE py.nama_lengkap
                                        END as nama_penyewa,
                                        py.tipe_penyewa,
                                        py.email as email_penyewa,
                                        a.nama_acara,
                                        pb.status_pembayaran,
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
                                    $tipe_display = ($row['tipe_penyewa'] == 'instansi') ? 'Instansi' : 'Umum';
                                    
                                    echo "<tr data-tipe='{$row['tipe_penyewa']}' data-tanggal-sewa='{$row['tanggal_sewa']}' data-tanggal-selesai='{$row['tanggal_selesai']}'>";
                                    echo "<td><strong>$no</strong></td>";
                                    echo "<td><strong>#{$row['id_penyewa']}</strong></td>";
                                    echo "<td><strong>#{$row['id_pemesanan']}</strong></td>";
                                    echo "<td><span class='badge {$tipe_class}'>{$tipe_display}</span></td>";
                                    echo "<td>";
                                    echo "<strong>{$row['nama_penyewa']}</strong>";
                                    echo "<br><small>{$row['email_penyewa']}</small>";
                                    echo "</td>";
                                    echo "<td><strong>{$row['nama_acara']}</strong></td>";
                                    echo "<td>" . date('d M Y', strtotime($row['tanggal_sewa'])) . "</td>";
                                    echo "<td>" . date('d M Y', strtotime($row['tanggal_selesai'])) . "</td>";
                                    echo "<td><strong>Rp " . number_format($row['total'], 0, ',', '.') . "</strong></td>";
                                    echo "<td>{$row['metode_pembayaran']}</td>";
                                    echo "<td>" . ($row['kebutuhan_tambahan'] ?: '-') . "</td>";
                                    echo "<td><span class='badge {$status_class}'>{$status_pembayaran}</span></td>";
                                    echo "<td>" . date('d M Y H:i', strtotime($row['tanggal_pesan'])) . "</td>";
                                    echo "</tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr>";
                                echo "<td colspan='13' class='empty-state'>";
                                echo "<div class='empty-icon'><i class='fas fa-history'></i></div>";
                                echo "<div class='empty-text'>Belum ada riwayat pemesanan</div>";
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

        // Load data on page ready
        document.addEventListener('DOMContentLoaded', function() {
            loadData();
        });

        function loadData() {
            const tableRows = document.querySelectorAll('#riwayatTable tbody tr');
            allData = [];
            
            tableRows.forEach(row => {
                const cells = row.cells;
                if (cells.length > 1) {
                    allData.push({
                        no: cells[0].textContent.trim(),
                        id_penyewa: cells[1].textContent.trim(),
                        id_pemesanan: cells[2].textContent.trim(),
                        tipe_penyewa: row.getAttribute('data-tipe'),
                        tipe_display: cells[3].textContent.trim(),
                        nama_penyewa: cells[4].querySelector('strong')?.textContent || '',
                        email_penyewa: cells[4].querySelector('small')?.textContent || '',
                        nama_acara: cells[5].textContent.trim(),
                        tanggal_sewa: cells[6].textContent.trim(),
                        tanggal_sewa_raw: row.getAttribute('data-tanggal-sewa'),
                        tanggal_selesai: cells[7].textContent.trim(),
                        tanggal_selesai_raw: row.getAttribute('data-tanggal-selesai'),
                        total: cells[8].textContent.trim(),
                        metode_pembayaran: cells[9].textContent.trim(),
                        kebutuhan_tambahan: cells[10].textContent.trim(),
                        status: cells[11].textContent.trim(),
                        tanggal_dibuat: cells[12].textContent.trim()
                    });
                }
            });
            
            filteredData = allData;
        }

        function applyFilter() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const tipeFilter = document.getElementById('tipeFilter').value;
            
            filteredData = allData.filter(item => {
                let searchMatch = true;
                let dateMatch = true;
                let tipeMatch = true;
                
                // Search filter
                if (searchInput) {
                    searchMatch = (
                        item.nama_penyewa?.toLowerCase().includes(searchInput) ||
                        item.nama_acara?.toLowerCase().includes(searchInput) ||
                        item.email_penyewa?.toLowerCase().includes(searchInput) ||
                        item.id_penyewa?.toLowerCase().includes(searchInput) ||
                        item.id_pemesanan?.toLowerCase().includes(searchInput)
                    );
                }
                
                // Date filter (tanggal mulai)
                if (startDate && item.tanggal_sewa_raw) {
                    const itemDate = new Date(item.tanggal_sewa_raw);
                    const filterDate = new Date(startDate);
                    dateMatch = itemDate >= filterDate;
                }
                
                // Date filter (tanggal acara/selesai)
                if (endDate && dateMatch && item.tanggal_selesai_raw) {
                    const itemDate = new Date(item.tanggal_selesai_raw);
                    const filterDate = new Date(endDate);
                    dateMatch = itemDate <= filterDate;
                }
                
                // Tipe filter
                if (tipeFilter) {
                    tipeMatch = item.tipe_penyewa === tipeFilter;
                }
                
                return searchMatch && dateMatch && tipeMatch;
            });
            
            updateTable();
        }

        function resetFilter() {
            document.getElementById('searchInput').value = '';
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            document.getElementById('tipeFilter').value = '';
            
            filteredData = allData;
            updateTable();
        }

        function performSearch() {
            setTimeout(applyFilter, 300); // Debounce search
        }

        function updateTable() {
            const tbody = document.querySelector('#riwayatTable tbody');
            
            if (filteredData.length === 0) {
                tbody.innerHTML = `
                    <tr><td colspan="13" class="empty-state">
                        <i class="fas fa-history"></i><br>
                        Tidak ada data yang sesuai dengan filter
                    </td></tr>
                `;
                return;
            }
            
            let tableHTML = '';
            filteredData.forEach((item, index) => {
                const statusClass = item.status.includes('Lunas') ? 'badge-success' : 'badge-warning';
                const tipeClass = item.tipe_penyewa === 'instansi' ? 'badge-info' : 'badge-primary';
                
                tableHTML += `
                    <tr>
                        <td><strong>${index + 1}</strong></td>
                        <td><strong>${item.id_penyewa}</strong></td>
                        <td><strong>${item.id_pemesanan}</strong></td>
                        <td><span class="badge ${tipeClass}">${item.tipe_display}</span></td>
                        <td>
                            <strong>${item.nama_penyewa}</strong><br>
                            <small>${item.email_penyewa}</small>
                        </td>
                        <td><strong>${item.nama_acara}</strong></td>
                        <td>${item.tanggal_sewa}</td>
                        <td>${item.tanggal_selesai}</td>
                        <td><strong>${item.total}</strong></td>
                        <td>${item.metode_pembayaran}</td>
                        <td>${item.kebutuhan_tambahan}</td>
                        <td><span class="badge ${statusClass}">${item.status}</span></td>
                        <td>${item.tanggal_dibuat}</td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = tableHTML;
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
