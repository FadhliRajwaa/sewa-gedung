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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* Chart Container */
        .chart-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .chart-title {
            color: #8B4513;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        #myChart {
            max-height: 400px;
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
                padding: 20px 15px;
                padding-top: 80px;
            }

            .page-header {
                padding: 15px 20px;
                margin-bottom: 20px;
            }

            .page-header h1 {
                font-size: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .stat-card {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 15px 10px;
                padding-top: 75px;
            }

            .page-header {
                padding: 12px 15px;
            }

            .page-header h1 {
                font-size: 18px;
            }

            .stat-card {
                padding: 15px;
            }
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
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="logo">
                <h1>Sewa Gedung</h1>
                <p style="color: rgba(255,255,255,0.7); font-size: 12px;">Admin Panel</p>
            </div>
            
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link active">
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

        <!-- Mobile Overlay -->
        <div class="mobile-overlay" onclick="closeMobileMenu()"></div>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard Admin</h1>
            </div>

            <div class="content">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Penyewa</h3>
                        <div class="stat-value" id="totalPenyewa">0</div>
                        <div class="stat-label">Terdaftar</div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Total Pemesanan</h3>
                        <div class="stat-value" id="totalPemesanan">0</div>
                        <div class="stat-label">Semua Waktu</div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Total Pendapatan</h3>
                        <div class="stat-value" id="totalPendapatan">Rp 0</div>
                        <div class="stat-label">dari Pembayaran Lunas</div>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Pemesanan Bulan Ini</h3>
                        <div class="stat-value" id="pemesananBulanIni">0</div>
                        <div class="stat-label">Bulan Ini</div>
                    </div>
                </div>

                <!-- Chart -->
                <div class="chart-container">
                    <h2 class="chart-title">Grafik Pendapatan Bulanan</h2>
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Mobile Menu Functions
        // Mobile Menu Functions
        function toggleMobileMenu() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.mobile-overlay');
            
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        function closeMobileMenu() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.mobile-overlay');
            
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }

        // Close mobile menu when clicking nav links
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', closeMobileMenu);
            });
        });

        // Chart Setup
        let myChart = null;

        // Load Dashboard Stats
        function loadDashboardStats() {
            fetch('ajax/get_dashboard_stats.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error loading stats:', data.error);
                        return;
                    }
                    
                    // Update stat cards
                    document.getElementById('totalPenyewa').textContent = data.total_penyewa || 0;
                    document.getElementById('totalPemesanan').textContent = data.total_pemesanan || 0;
                    document.getElementById('totalPendapatan').textContent = 'Rp ' + (parseInt(data.total_pendapatan || 0)).toLocaleString('id-ID');
                    document.getElementById('pemesananBulanIni').textContent = data.pemesanan_bulan_ini || 0;
                    
                    // Update chart
                    updateChart(data.chart_data || []);
                })
                .catch(error => {
                    console.error('Error loading dashboard stats:', error);
                });
        }

        // Chart Update Function
        function updateChart(chartData) {
            const ctx = document.getElementById('myChart').getContext('2d');
            
            if (myChart) {
                myChart.destroy();
            }
            
            const labels = chartData.labels || [];
            const values = chartData.values || [];
            
            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: values,
                        borderColor: '#8B4513',
                        backgroundColor: 'rgba(139, 69, 19, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
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
                    }
                }
            });
        }

        // Initialize Dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardStats();
            
            // Refresh stats every 5 minutes
            setInterval(loadDashboardStats, 5 * 60 * 1000);
        });
    </script>
</body>
</html>
