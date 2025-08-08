<?php
// Mulai sesi
session_start();
require_once 'config.php';

// Cek apakah user sudah login
if (!isset($_SESSION['id_penyewa'])) {
    header('Location: login.php');
    exit;
}

// Periksa apakah parameter 'gedung' ada di URL
if (isset($_GET['gedung'])) {
    $id_acara = $_GET['gedung'];
    
    // Get event data from database
    try {
        $query = "SELECT * FROM acara WHERE id_acara = ? AND status = 'tersedia'";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_acara]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$event) {
            header("Location: gedung.php");
            exit();
        }
        
    } catch (PDOException $e) {
        header("Location: gedung.php");
        exit();
    }
} else {
    // Jika tidak ada parameter 'gedung', arahkan ke halaman gedung
    header("Location: gedung.php");
    exit();
}

// Get image based on event type
$eventImages = [
    'Pernikahan' => 'asset/gambar/gedung_pernikahan.jpg',
    'Rapat' => 'asset/gambar/gedung_rapat.jpg', 
    'Seminar' => 'asset/gambar/gedung_seminar.jpg',
    'Gedung Serbaguna A' => 'asset/gambar/gedung1.jpg',
    'Gedung Serbaguna B' => 'asset/gambar/gedung2.jpg'
];

$eventImage = $eventImages[$event['nama_acara']] ?? 'asset/gambar/gedung1.jpg';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= strtoupper($event['nama_acara']) ?> - PT. Aneka Usaha</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f1eb 0%, #e8ddd0 100%);
            min-height: 100vh;
            line-height: 1.6;
        }

        /* Modern Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .nav-logo img {
            height: 45px;
            width: auto;
        }

        .nav-logo .logo-text {
            font-size: 18px;
            font-weight: 700;
            color: #8B4513;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 30px;
            align-items: center;
        }

        .nav-link {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 25px;
        }

        .nav-link:hover {
            color: #8B4513;
            background: rgba(139, 69, 19, 0.1);
            text-decoration: none;
            transform: translateY(-2px);
        }

        /* Hamburger Menu */
        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 8px;
            gap: 4px;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: #8B4513;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        /* Main Content */
        .main-content {
            margin-top: 100px;
            padding: 40px 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: start;
        }

        /* Left Section - Event Info */
        .event-section {
            background: white;
            border-radius: 20px;
            padding: 0;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .event-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 20px 20px 0 0;
        }

        .event-content {
            padding: 30px;
        }

        .event-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #8B4513;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .event-details {
            margin-bottom: 25px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            font-size: 1.1rem;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #8B4513;
        }

        .detail-value {
            color: #333;
            font-weight: 500;
        }

        .price-highlight {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }

        .price-highlight .price {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .facilities-section {
            margin-top: 20px;
        }

        .facilities-title {
            font-weight: 600;
            color: #8B4513;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .facilities-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .facility-tag {
            background: rgba(139, 69, 19, 0.1);
            color: #8B4513;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Right Section - Booking Form */
        .booking-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 120px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .form-subtitle {
            color: #666;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #8B4513;
            font-weight: 600;
            font-size: 1rem;
        }

        .required {
            color: #e74c3c;
        }

        .form-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .form-input:focus {
            outline: none;
            border-color: #8B4513;
            background: white;
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }

        /* Special styling for kebutuhan tambahan textarea */
        .form-input[name="kebutuhan_tambahan"] {
            min-height: 120px;
            resize: vertical;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border: 2px solid #e9ecef;
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
        }

        .form-input[name="kebutuhan_tambahan"]:focus {
            border-color: #8B4513;
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }

        .form-input[name="kebutuhan_tambahan"]::placeholder {
            color: #6c757d;
            font-style: italic;
        }

        .form-help {
            display: block;
            margin-top: 5px;
            font-size: 0.85rem;
            color: #6c757d;
            font-style: italic;
        }

        .date-input {
            position: relative;
        }

        .date-input::before {
            content: '\f073';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #8B4513;
            pointer-events: none;
            z-index: 1;
        }

        .price-calculator {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
        }

        .calc-title {
            font-weight: 600;
            color: #8B4513;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .calc-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: #666;
        }

        .calc-total {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #8B4513;
            font-weight: 700;
            font-size: 1.2rem;
            color: #8B4513;
        }

        .btn-check {
            width: 100%;
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .btn-check:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 69, 19, 0.3);
        }

        .btn-book {
            width: 100%;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            padding: 18px 20px;
            border: none;
            border-radius: 15px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.3);
            color: white;
            text-decoration: none;
        }

        .availability-info {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }

        .availability-info.warning {
            background: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
        }

        .availability-info.error {
            background: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .disclaimer {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #666;
            line-height: 1.6;
        }

        /* Mobile Responsive */
        @media (max-width: 1024px) {
            .container {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .booking-section {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .nav-menu {
                position: fixed;
                top: 80px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 80px);
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(20px);
                flex-direction: column;
                justify-content: flex-start;
                align-items: center;
                gap: 20px;
                padding-top: 50px;
                transition: left 0.3s ease;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            }

            .nav-menu.active {
                left: 0;
            }

            .nav-menu li {
                width: 80%;
                text-align: center;
            }

            .nav-link {
                display: block;
                width: 100%;
                padding: 15px 20px;
                font-size: 16px;
                border-radius: 10px;
                background: rgba(139, 69, 19, 0.05);
                margin-bottom: 10px;
            }

            .hamburger {
                display: flex;
            }

            .main-content {
                margin-top: 80px;
                padding: 20px 10px;
            }

            .event-title {
                font-size: 2rem;
            }

            .event-content {
                padding: 20px;
            }

            .booking-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Modern Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <img src="logo.png" alt="PT. Aneka Usaha" onerror="this.style.display='none'">
                <div class="logo-text">PT. ANEKA USAHA</div>
            </div>
            
            <div class="hamburger" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <ul class="nav-menu" id="navMenu">
                <li><a href="dashboard_user.php" class="nav-link"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="acara_saya.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Acara Saya</a></li>
                <li><a href="gedung.php" class="nav-link"><i class="fas fa-building"></i> Gedung</a></li>
                <li><a href="panduan.php" class="nav-link"><i class="fas fa-book"></i> Panduan</a></li>
                <li><a href="akun.php" class="nav-link"><i class="fas fa-user"></i> Akun</a></li>
                <li><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Left Section - Event Information -->
            <div class="event-section">
                <img src="<?= $eventImage ?>" alt="<?= $event['nama_acara'] ?>" class="event-image" onerror="this.src='asset/gambar/bg.jpg'">
                
                <div class="event-content">
                    <h1 class="event-title"><?= htmlspecialchars($event['nama_acara']) ?></h1>
                    
                    <div class="price-highlight">
                        <div>Harga Sewa</div>
                        <div class="price">Rp <?= number_format($event['harga'], 0, ',', '.') ?></div>
                    </div>

                    <div class="event-details">
                        <div class="detail-item">
                            <span class="detail-label">Kapasitas:</span>
                            <span class="detail-value"><?= $event['kapasitas'] ?> Orang</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Lokasi:</span>
                            <span class="detail-value"><?= htmlspecialchars($event['lokasi']) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value">Tersedia</span>
                        </div>
                    </div>

                    <?php if ($event['fasilitas']): ?>
                    <div class="facilities-section">
                        <div class="facilities-title">
                            <i class="fas fa-star"></i> Fasilitas
                        </div>
                        <div class="facilities-list">
                            <?php
                            $facilities = explode(', ', $event['fasilitas']);
                            foreach ($facilities as $facility): ?>
                                <span class="facility-tag"><?= htmlspecialchars(trim($facility)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Section - Booking Form -->
            <div class="booking-section">
                <div class="form-header">
                    <h2 class="form-title">Formulir Pemesanan</h2>
                    <p class="form-subtitle">Isi data pemesanan Anda dengan lengkap</p>
                </div>

                <form action="proses_booking.php" method="POST" id="bookingForm">
                    <input type="hidden" name="id_acara" value="<?= $id_acara ?>">
                    <input type="hidden" name="id_penyewa" value="<?= $_SESSION['id_penyewa'] ?>">

                    <div class="form-group">
                        <label class="form-label">Tanggal Mulai<span class="required">*</span></label>
                        <div class="date-input">
                            <input type="date" class="form-input" name="tanggal_sewa" id="tanggal_sewa" required min="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Selesai<span class="required">*</span></label>
                        <div class="date-input">
                            <input type="date" class="form-input" name="tanggal_selesai" id="tanggal_selesai" required min="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <button type="button" class="btn-check" onclick="calculatePrice()">
                        <i class="fas fa-calculator"></i> Cek
                    </button>

                    <div class="price-calculator" id="priceCalculator" style="display: none;">
                        <div class="calc-title">
                            <i class="fas fa-receipt"></i> Rincian Harga (Otomatis)
                        </div>
                        <div class="calc-item">
                            <span>Hari yang ditentukan x 1 Hari harga sewa <?= strtolower($event['nama_acara']) ?></span>
                        </div>
                        <div class="calc-item">
                            <span id="durationText">0 hari x Rp <?= number_format($event['harga'], 0, ',', '.') ?></span>
                            <span id="subtotalText">Rp 0</span>
                        </div>
                        <div class="calc-total">
                            <span>Total:</span>
                            <span id="totalText">Rp 0</span>
                        </div>
                    </div>

                    <div class="availability-info" id="availabilityInfo" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                        Ketersediaan gedung dapat berubah sewaktu-waktu. Mohon melakukan pengecekan sebelumnya di tanggal dan waktu operasional untuk konfirmasi sebelum dan melakukan penyewaan. Pengurus gedung PT. Aneka Usaha tidak bertanggung jawab apabila terjadi pembatalan saat melakukan penyewaan gedung yang telah dikonfirmasi pengguna sebelumnya. Terimakasih
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-clipboard-list"></i> Kebutuhan Tambahan
                        </label>
                        <textarea class="form-input" name="kebutuhan_tambahan" id="kebutuhan_tambahan" rows="4" placeholder="Tuliskan kebutuhan khusus untuk acara Anda (opsional)"></textarea>
                        <small class="form-help">Contoh: Dekorasi, catering, sound system tambahan, dll.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Metode Pembayaran<span class="required">*</span></label>
                        <select class="form-input" name="metode_pembayaran" required>
                            <option value="">Pilih Metode Pembayaran</option>
                            <option value="Transfer_BCA">Transfer BCA</option>
                            <option value="Transfer_Mandiri">Transfer Mandiri</option>
                            <option value="Transfer_BNI">Transfer BNI</option>
                            <option value="Transfer_BRI">Transfer BRI</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-book">
                        <i class="fas fa-calendar-plus"></i> Pesan Sekarang
                    </button>

                    <div class="disclaimer">
                        <strong>Catatan:</strong> Setelah melakukan pemesanan, Anda akan diarahkan ke halaman pembayaran. Pembayaran harus diselesaikan dalam 24 jam untuk mengkonfirmasi pemesanan Anda.
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        function calculatePrice() {
            const tanggalSewa = document.getElementById('tanggal_sewa').value;
            const tanggalSelesai = document.getElementById('tanggal_selesai').value;
            
            if (!tanggalSewa || !tanggalSelesai) {
                alert('Mohon pilih tanggal mulai dan selesai terlebih dahulu');
                return;
            }
            
            const startDate = new Date(tanggalSewa);
            const endDate = new Date(tanggalSelesai);
            
            if (endDate < startDate) {
                alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai');
                return;
            }
            
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            
            const pricePerDay = <?= $event['harga'] ?>;
            const total = diffDays * pricePerDay;
            
            // Update calculator display
            document.getElementById('durationText').textContent = `${diffDays} hari x Rp ${pricePerDay.toLocaleString('id-ID')}`;
            document.getElementById('subtotalText').textContent = `Rp ${total.toLocaleString('id-ID')}`;
            document.getElementById('totalText').textContent = `Rp ${total.toLocaleString('id-ID')}`;
            
            // Show calculator and availability info
            document.getElementById('priceCalculator').style.display = 'block';
            document.getElementById('availabilityInfo').style.display = 'block';
        }

        // Update minimum date for end date when start date changes
        document.getElementById('tanggal_sewa').addEventListener('change', function() {
            const startDate = this.value;
            document.getElementById('tanggal_selesai').min = startDate;
            
            // Reset calculator if dates change
            document.getElementById('priceCalculator').style.display = 'none';
            document.getElementById('availabilityInfo').style.display = 'none';
        });

        document.getElementById('tanggal_selesai').addEventListener('change', function() {
            // Reset calculator if dates change
            document.getElementById('priceCalculator').style.display = 'none';
            document.getElementById('availabilityInfo').style.display = 'none';
        });

        // Form validation
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            const tanggalSewa = document.getElementById('tanggal_sewa').value;
            const tanggalSelesai = document.getElementById('tanggal_selesai').value;
            
            if (!tanggalSewa || !tanggalSelesai) {
                e.preventDefault();
                alert('Mohon lengkapi tanggal mulai dan selesai');
                return;
            }
            
            const startDate = new Date(tanggalSewa);
            const endDate = new Date(tanggalSelesai);
            
            if (endDate < startDate) {
                e.preventDefault();
                alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai');
                return;
            }
            
            // Check if price has been calculated
            const calculator = document.getElementById('priceCalculator');
            if (calculator.style.display === 'none') {
                e.preventDefault();
                alert('Mohon klik tombol "Cek" terlebih dahulu untuk menghitung harga');
                return;
            }
        });

        // Hamburger Menu Toggle
        function toggleMenu() {
            const navMenu = document.getElementById('navMenu');
            const hamburger = document.querySelector('.hamburger');
            
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        }

        // Close menu when clicking on a link (mobile)
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                const navMenu = document.getElementById('navMenu');
                const hamburger = document.querySelector('.hamburger');
                
                if (window.innerWidth <= 768) {
                    navMenu.classList.remove('active');
                    hamburger.classList.remove('active');
                }
            });
        });

        // Close menu when clicking outside (mobile)
        document.addEventListener('click', (e) => {
            const navMenu = document.getElementById('navMenu');
            const hamburger = document.querySelector('.hamburger');
            const navbar = document.querySelector('.navbar');
            
            if (window.innerWidth <= 768 && !navbar.contains(e.target)) {
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
            }
        });

        // Set dynamic placeholder for kebutuhan tambahan based on event type
        document.addEventListener('DOMContentLoaded', function() {
            const kebutuhanTextarea = document.getElementById('kebutuhan_tambahan');
            const eventType = '<?= $event['nama_acara'] ?>';
            
            let placeholder = '';
            switch(eventType.toLowerCase()) {
                case 'pernikahan':
                    placeholder = 'Contoh: Dekorasi tema warna merah putih, live band, catering untuk 200 tamu, bunga pelaminan, fotografer, tenda VIP, sound system outdoor, dll.';
                    break;
                case 'rapat/meeting':
                case 'rapat':
                case 'meeting':
                    placeholder = 'Contoh: Proyektor tambahan, flipchart, coffee break, setup meja U-shape, microphone wireless, webcam untuk meeting online, dll.';
                    break;
                case 'seminar':
                    placeholder = 'Contoh: Layar proyektor besar, sound system berkualitas tinggi, podium khusus, area registrasi, snack break, banner backdrop, dll.';
                    break;
                default:
                    placeholder = 'Contoh: Dekorasi khusus, catering, sound system tambahan, setup panggung, lighting khusus, dll.';
            }
            
            kebutuhanTextarea.placeholder = placeholder;
        });
    </script>
</body>
</html>
