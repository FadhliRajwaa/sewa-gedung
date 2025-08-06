<?php
session_start();
require_once 'config.php';
require_once 'includes/pricing.php';

// Check if user is logged in (for booking functionality)
$user_logged_in = isset($_SESSION['id_penyewa']);

// Get pricing for Seminar (ID: 3)
$event_pricing = getPriceByEventId($pdo, 3);
$event_price = $event_pricing ? $event_pricing['formatted_price'] : 'Rp 4.350.000';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    
    try {
        // Check date availability (no login required for checking)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM pemesanan WHERE id_acara = 3 AND 
                              ((tanggal_sewa <= ? AND tanggal_selesai >= ?) OR 
                               (tanggal_sewa <= ? AND tanggal_selesai >= ?))");
        $stmt->execute([$tanggal_mulai, $tanggal_mulai, $tanggal_selesai, $tanggal_selesai]);
        $conflict = $stmt->fetchColumn();
        
        if ($conflict > 0) {
            $error = "Tanggal yang dipilih sudah dipesan. Silakan pilih tanggal lain.";
        } else {
            $success = "Tanggal tersedia! Harga sewa: " . $event_price . ". " . ($user_logged_in ? "Silakan lanjutkan ke pemesanan." : "Silakan login untuk melanjutkan pemesanan.");
        }
    } catch (Exception $e) {
        $error = "Terjadi kesalahan saat mengecek ketersediaan tanggal.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Tanggal Seminar</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f0;
            margin: 0;
            padding: 0;
        }
        
        .container-fluid {
            padding: 0;
        }
        
        .left-section {
            background-color: #8B4513;
            color: white;
            padding: 40px;
            min-height: 100vh;
        }
        
        .event-title {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 30px;
            color: white;
        }
        
        .event-image {
            width: 100%;
            max-width: 500px;
            height: 300px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .event-details {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        
        .event-details strong {
            display: block;
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .right-section {
            background-color: #f5f5f0;
            padding: 40px;
            min-height: 100vh;
        }
        
        .form-title {
            color: #8B4513;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .date-input-group {
            margin-bottom: 25px;
        }
        
        .date-input {
            width: 100%;
            padding: 12px 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            background-color: white;
        }
        
        .date-input:focus {
            outline: none;
            border-color: #8B4513;
            box-shadow: 0 0 0 2px rgba(139, 69, 19, 0.2);
        }
        
        .btn-cek {
            background-color: #8B4513;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-cek:hover {
            background-color: #654321;
            transform: translateY(-2px);
        }
        
        .pricing-info {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
            border-left: 4px solid #8B4513;
        }

        .pricing-info h5 {
            color: #8B4513;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        #price-calculation {
            min-height: 60px;
        }
        
        .price-breakdown {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #8B4513;
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }        .availability-note {
            background-color: #e8f4f8;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .btn-pesan {
            background-color: #8B4513;
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            margin-top: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-pesan:hover {
            background-color: #654321;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 69, 19, 0.3);
        }
        
        .alert {
            margin-top: 20px;
            border-radius: 8px;
        }
        
        .calendar-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #8B4513;
            font-size: 20px;
            pointer-events: none;
        }
        
        .input-container {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Left Section -->
            <div class="col-lg-6 left-section">
                <h1 class="event-title">SEMINAR</h1>
                <img src="asset/gambar/gedung_seminar.jpg" alt="Gedung Seminar" class="event-image">
                
                <div class="event-details">
                    <strong>Harga Sewa <?= $event_price ?>;</strong>
                    <p>Kapasitas : 300 Orang.</p>
                    <p>Lokasi : Jl. Jenderal Sudirman No. 1, Pemalang.</p>
                    <p>Fasilitas : Panggung Utama, Halaman Parkir, Kursi, Luas, AC, Toilet.</p>
                    <p><strong>Status : Tersedia.</strong></p>
                </div>
            </div>
            
            <!-- Right Section -->
            <div class="col-lg-6 right-section">
                <div class="form-title">Tanggal Mulai*</div>
                <form method="POST" action="">
                    <div class="date-input-group">
                        <div class="input-container">
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="date-input" required value="<?= isset($_POST['tanggal_mulai']) ? htmlspecialchars($_POST['tanggal_mulai']) : '' ?>">
                            <i class="calendar-icon">📅</i>
                        </div>
                    </div>
                    
                    <div class="form-title">Tanggal Selesai*</div>
                    <div class="date-input-group">
                        <div class="input-container">
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="date-input" required value="<?= isset($_POST['tanggal_selesai']) ? htmlspecialchars($_POST['tanggal_selesai']) : '' ?>">
                            <i class="calendar-icon">📅</i>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-cek">Cek</button>
                </form>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <div class="pricing-info">
                    <h5>Rincian Harga : (Otomatis)</h5>
                    <div id="price-calculation">
                        <p>Silakan pilih tanggal untuk melihat rincian harga</p>
                    </div>
                </div>
                
                <div class="availability-note">
                    <p>Ketersediaan gedung dapat berubah sewaktu-waktu. Mohon melakukan pengecekan sebelumnya di tanggal dan waktu operasional untuk konfirmasi sebelum dan melakukan penyewaan. Pengurus gedung PT. Aneka Usaha tidak bertanggung jawab apabila terjadi pembatalan saat melakukan penyewaan gedung yang telah dikonfirmasi pengguna sebelumnya. Terimakasih</p>
                </div>
                
                <?php if (!empty($success)): ?>
                    <?php if ($user_logged_in): ?>
                        <button class="btn-pesan" onclick="pesanSekarang()">Pesan Sekarang</button>
                    <?php else: ?>
                        <div class="login-prompt">
                            <p style="color: #8B4513; font-weight: 600; margin: 20px 0 10px 0;">Untuk melanjutkan pemesanan, silakan:</p>
                            <div class="login-buttons" style="display: flex; gap: 15px; justify-content: center; margin-top: 15px;">
                                <button class="btn-login" onclick="location.href='login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>'" style="background-color: #8B4513; color: white; border: none; padding: 12px 25px; border-radius: 50px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </button>
                                <button class="btn-register" onclick="location.href='register.php'" style="background-color: #654321; color: white; border: none; padding: 12px 25px; border-radius: 50px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                                    <i class="fas fa-user-plus"></i> Daftar
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Price per day for seminar
        const pricePerDay = <?= $event_pricing ? $event_pricing['price'] : 4350000 ?>;
        const formattedPricePerDay = '<?= $event_price ?>';
        
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        const startDateInput = document.querySelector('input[name="tanggal_mulai"]');
        const endDateInput = document.querySelector('input[name="tanggal_selesai"]');
        const priceCalculationDiv = document.getElementById('price-calculation');
        
        startDateInput.setAttribute('min', today);
        endDateInput.setAttribute('min', today);
        
        // Function to calculate days between two dates
        function calculateDays(startDate, endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 to include both start and end day
            return diffDays;
        }
        
        // Function to format number as Indonesian currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }
        
        // Function to update price calculation
        function updatePriceCalculation() {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;
            
            if (startDate && endDate) {
                if (new Date(endDate) >= new Date(startDate)) {
                    const days = calculateDays(startDate, endDate);
                    const totalPrice = pricePerDay * days;
                    
                    priceCalculationDiv.innerHTML = `
                        <div class="price-breakdown">
                            <p style="margin: 0 0 8px 0; font-weight: 600; color: #8B4513;">Rincian Perhitungan:</p>
                            <p style="margin: 0 0 5px 0;">${days} hari × ${formattedPricePerDay} = <strong>${formatCurrency(totalPrice)}</strong></p>
                            <small style="color: #666;">Periode: ${new Date(startDate).toLocaleDateString('id-ID')} - ${new Date(endDate).toLocaleDateString('id-ID')}</small>
                        </div>
                    `;
                } else {
                    priceCalculationDiv.innerHTML = `
                        <p style="color: #dc3545; font-weight: 500;">Tanggal selesai harus sama atau setelah tanggal mulai</p>
                    `;
                }
            } else {
                priceCalculationDiv.innerHTML = `
                    <p>Silakan pilih tanggal untuk melihat rincian harga</p>
                `;
            }
        }
        
        // Update end date minimum when start date changes
        startDateInput.addEventListener('change', function() {
            endDateInput.setAttribute('min', this.value);
            updatePriceCalculation();
        });
        
        // Update calculation when end date changes
        endDateInput.addEventListener('change', updatePriceCalculation);
        
        // Initial calculation if dates are already set
        updatePriceCalculation();
        
        // Function untuk pesan sekarang
        function pesanSekarang() {
            const startDate = document.getElementById('tanggal_mulai').value;
            const endDate = document.getElementById('tanggal_selesai').value;
            
            if (!startDate || !endDate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanggal Belum Dipilih',
                    text: 'Silakan pilih tanggal mulai dan selesai terlebih dahulu',
                    confirmButtonColor: '#667eea'
                });
                return;
            }
            
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang mempersiapkan halaman booking',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Redirect after short delay for better UX
            setTimeout(() => {
                const url = `proses_sewa.php?acara=seminar&tanggal_mulai=${encodeURIComponent(startDate)}&tanggal_selesai=${encodeURIComponent(endDate)}`;
                window.location.href = url;
            }, 1000);
        }
    </script>
    
    <!-- SweetAlert2 for better alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
