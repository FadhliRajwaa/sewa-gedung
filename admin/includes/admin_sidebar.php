<?php
// Template sidebar admin yang konsisten untuk semua halaman
?>
<!-- Mobile Menu Toggle -->
<button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
    <i class="fas fa-bars"></i>
</button>

<!-- Mobile Overlay -->
<div class="mobile-overlay" onclick="closeMobileMenu()"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="asset/logo.png" alt="PT Aneka Usaha" onerror="this.style.display='none'">
        </div>
        <h2>PT. ANEKA USAHA</h2>
    </div>
    
    <ul class="nav-menu">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="manage_payments.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'manage_payments.php' ? 'active' : '' ?>">
                <i class="fas fa-credit-card"></i>
                Kelola Pembayaran
            </a>
        </li>
        <li class="nav-item">
            <a href="transaction_history.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'transaction_history.php' ? 'active' : '' ?>">
                <i class="fas fa-history"></i>
                Riwayat Transaksi
            </a>
        </li>
        <li class="nav-item">
            <a href="reports.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">
                <i class="fas fa-chart-bar"></i>
                Laporan
            </a>
        </li>
        <li class="nav-item">
            <a href="add_event.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'add_event.php' ? 'active' : '' ?>">
                <i class="fas fa-plus-circle"></i>
                Tambah Event
            </a>
        </li>
        <li class="nav-item">
            <a href="account.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'account.php' ? 'active' : '' ?>">
                <i class="fas fa-user-cog"></i>
                Akun
            </a>
        </li>
        <li class="nav-item">
            <a href="logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </li>
    </ul>
</aside>
