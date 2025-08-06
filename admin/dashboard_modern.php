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
    <title>Dashboard Admin - Sewa Gedung</title>
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

        .sidebar-header i {
            font-size: 24px;
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
            position: relative;
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

        /* Mobile Menu Toggle */
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
            transition: all 0.2s ease;
        }

        .mobile-menu-toggle:hover {
            background: var(--gray-light);
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

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
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

        .stat-header {
            display: flex;
            justify-content: between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .stat-icon {
            width: 64px;
            height: 64px;
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
            font-size: 36px;
            font-weight: 800;
            color: var(--dark);
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--gray);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
        }

        .stat-trend.up { color: var(--success); }
        .stat-trend.down { color: var(--danger); }

        /* Chart Container */
        .chart-container {
            background: var(--white);
            padding: 32px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 32px;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .chart-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--dark);
        }

        .chart-filters {
            display: flex;
            gap: 8px;
        }

        .filter-btn {
            padding: 8px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--white);
            color: var(--gray);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary);
            color: var(--white);
            border-color: var(--primary);
        }

        /* Recent Activity */
        .activity-container {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }

        .activity-header {
            padding: 24px 32px;
            border-bottom: 1px solid var(--border);
        }

        .activity-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--dark);
        }

        .activity-list {
            padding: 24px 32px;
        }

        .activity-item {
            display: flex;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid var(--border);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--white);
            flex-shrink: 0;
        }

        .activity-content {
            flex: 1;
        }

        .activity-text {
            color: var(--dark);
            font-weight: 500;
            margin-bottom: 4px;
        }

        .activity-time {
            color: var(--gray);
            font-size: 13px;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .main-content {
                padding: 24px;
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

            .chart-container,
            .activity-container {
                padding: 20px;
            }

            .chart-header {
                flex-direction: column;
                gap: 16px;
                align-items: stretch;
            }

            .page-header {
                padding: 20px;
            }

            .page-title {
                font-size: 24px;
            }
        }

        /* Loading States */
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

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-light);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gray);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--dark);
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
                    <a href="dashboard.php" class="nav-link active">
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
                <h1 class="page-title">Dashboard Overview</h1>
                <p class="page-subtitle">Selamat datang di sistem administrasi sewa gedung</p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number" id="totalPenyewa">0</div>
                    <div class="stat-label">Total Penyewa</div>
                    <div class="stat-trend up">
                        <i class="fas fa-arrow-up"></i>
                        <span>+12% dari bulan lalu</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-number" id="totalPemesanan">0</div>
                    <div class="stat-label">Total Pemesanan</div>
                    <div class="stat-trend up">
                        <i class="fas fa-arrow-up"></i>
                        <span>+8% dari bulan lalu</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-number" id="totalPendapatan">Rp 0</div>
                    <div class="stat-label">Total Pendapatan</div>
                    <div class="stat-trend up">
                        <i class="fas fa-arrow-up"></i>
                        <span>+15% dari bulan lalu</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon accent">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-number" id="gedungTersedia">0</div>
                    <div class="stat-label">Gedung Tersedia</div>
                    <div class="stat-trend">
                        <i class="fas fa-minus"></i>
                        <span>Tidak ada perubahan</span>
                    </div>
                </div>
            </div>

            <!-- Chart Container -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">Grafik Pemesanan Bulanan</h3>
                    <div class="chart-filters">
                        <button class="filter-btn active">6 Bulan</button>
                        <button class="filter-btn">1 Tahun</button>
                        <button class="filter-btn">Semua</button>
                    </div>
                </div>
                <div style="position: relative; height: 400px;">
                    <canvas id="pemesananChart"></canvas>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="activity-container">
                <div class="activity-header">
                    <h3 class="activity-title">Aktivitas Terbaru</h3>
                </div>
                <div class="activity-list" id="recentActivity">
                    <div class="loading">
                        <div class="spinner"></div>
                        <p>Memuat aktivitas...</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Global variables
        let pemesananChart;

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardStats();
            initChart();
            loadRecentActivity();
        });

        // Load dashboard statistics
        function loadDashboardStats() {
            fetch('ajax/get_dashboard_stats.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }
                    
                    document.getElementById('totalPenyewa').textContent = data.total_penyewa || 0;
                    document.getElementById('totalPemesanan').textContent = data.total_pemesanan || 0;
                    document.getElementById('totalPendapatan').textContent = 
                        new Intl.NumberFormat('id-ID', { 
                            style: 'currency', 
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(data.total_pendapatan || 0);
                    document.getElementById('gedungTersedia').textContent = data.gedung_tersedia || 0;
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                });
        }

        // Initialize chart
        function initChart() {
            const ctx = document.getElementById('pemesananChart').getContext('2d');
            
            pemesananChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Pemesanan',
                        data: [],
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#6366f1',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                color: '#64748b'
                            }
                        },
                        x: {
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                color: '#64748b'
                            }
                        }
                    },
                    elements: {
                        point: {
                            hoverBackgroundColor: '#4f46e5'
                        }
                    }
                }
            });

            loadChartData();
        }

        // Load chart data
        function loadChartData() {
            fetch('ajax/get_chart_data.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }
                    
                    pemesananChart.data.labels = data.labels || [];
                    pemesananChart.data.datasets[0].data = data.values || [];
                    pemesananChart.update();
                })
                .catch(error => {
                    console.error('Error loading chart data:', error);
                });
        }

        // Load recent activity
        function loadRecentActivity() {
            fetch('ajax/get_recent_activity.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('recentActivity');
                    
                    if (data.error) {
                        container.innerHTML = '<div class="loading"><p>Error loading activity</p></div>';
                        return;
                    }
                    
                    if (data.length === 0) {
                        container.innerHTML = '<div class="loading"><p>Tidak ada aktivitas terbaru</p></div>';
                        return;
                    }
                    
                    let html = '';
                    data.forEach(activity => {
                        const iconClass = getActivityIcon(activity.type);
                        const iconColor = getActivityColor(activity.type);
                        
                        html += `
                            <div class="activity-item">
                                <div class="activity-icon" style="background: ${iconColor}">
                                    <i class="${iconClass}"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-text">${activity.description}</div>
                                    <div class="activity-time">${formatTime(activity.created_at)}</div>
                                </div>
                            </div>
                        `;
                    });
                    
                    container.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error loading activity:', error);
                    document.getElementById('recentActivity').innerHTML = 
                        '<div class="loading"><p>Error loading activity</p></div>';
                });
        }

        // Helper functions
        function getActivityIcon(type) {
            const icons = {
                'booking': 'fas fa-calendar-plus',
                'payment': 'fas fa-money-bill',
                'cancellation': 'fas fa-times-circle',
                'registration': 'fas fa-user-plus',
                'default': 'fas fa-info-circle'
            };
            return icons[type] || icons.default;
        }

        function getActivityColor(type) {
            const colors = {
                'booking': '#10b981',
                'payment': '#f59e0b',
                'cancellation': '#ef4444',
                'registration': '#6366f1',
                'default': '#64748b'
            };
            return colors[type] || colors.default;
        }

        function formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            
            const minutes = Math.floor(diff / (1000 * 60));
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            
            if (minutes < 60) {
                return `${minutes} menit yang lalu`;
            } else if (hours < 24) {
                return `${hours} jam yang lalu`;
            } else {
                return `${days} hari yang lalu`;
            }
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

        // Chart filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                // Add filter logic here
                loadChartData();
            });
        });
    </script>
</body>
</html>
