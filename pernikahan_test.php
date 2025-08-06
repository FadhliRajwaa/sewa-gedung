<?php
session_start();

// For testing - temporarily comment out login check
// require_once 'config.php';

// // Cek apakah user sudah login
// if (!isset($_SESSION['id_penyewa'])) {
//     header('Location: login.php');
//     exit;
// }

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    
    // Simple validation for testing
    if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
        $success = "Tanggal tersedia! Silakan lanjutkan ke pemesanan.";
    } else {
        $error = "Mohon pilih tanggal mulai dan selesai.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Tanggal Pernikahan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f0;
            margin: 0;
            padding: 0;
        }
        
        .container-fluid {
            padding: 0;
            min-height: 100vh;
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
            background-color: #654321;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
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
        
        .availability-note {
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
            width: 100%;
        }
        
        .btn-pesan:hover {
            background-color: #654321;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 69, 19, 0.3);
        }
        
        .alert {
            margin-top: 20px;
            border-radius: 8px;
            padding: 15px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
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
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .left-section, .right-section {
                min-height: auto;
                padding: 20px;
            }
            
            .event-title {
                font-size: 36px;
                margin-bottom: 20px;
            }
            
            .event-image {
                height: 200px;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Left Section -->
            <div class="col-lg-6 left-section">
                <h1 class="event-title">PERNIKAHAN</h1>
                <div class="event-image">
                    <img src="asset/gambar/gedung_pernikahan.jpg" alt="Gedung Pernikahan" style="width: 100%; height: 100%; object-fit: cover; border-radius: 15px;" onerror="this.style.display='none'; this.parentElement.innerHTML='Gambar Gedung Pernikahan';">
                </div>
                
                <div class="event-details">
                    <strong>Harga Sewa Rp 6.150.000;</strong>
                    <p>Kapasitas : 1.000 Orang.</p>
                    <p>Lokasi : Jl. Jenderal Sudirman No. 1, Pemalang.</p>
                    <p>Fasilitas : Panggung Utama, Halaman Parkir Luas, Kipas Angin, Toilet.</p>
                    <p><strong>Status : Tersedia.</strong></p>
                </div>
            </div>
            
            <!-- Right Section -->
            <div class="col-lg-6 right-section">
                <div class="form-title">Tanggal Mulai*</div>
                <form method="POST" action="">
                    <div class="date-input-group">
                        <div class="input-container">
                            <input type="date" name="tanggal_mulai" class="date-input" required>
                            <i class="calendar-icon">📅</i>
                        </div>
                    </div>
                    
                    <div class="form-title">Tanggal Selesai*</div>
                    <div class="date-input-group">
                        <div class="input-container">
                            <input type="date" name="tanggal_selesai" class="date-input" required>
                            <i class="calendar-icon">📅</i>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-cek">Cek</button>
                </form>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                
                <div class="pricing-info">
                    <h5>Rincian Harga : (Otomatis)</h5>
                    <p>Hari yang ditentukan X 1 Hari harga sewa pernikahan</p>
                </div>
                
                <div class="availability-note">
                    <p>Ketersediaan gedung dapat berubah sewaktu-waktu. Mohon melakukan pengecekan sebelumnya di tanggal dan waktu operasional untuk konfirmasi sebelum dan melakukan penyewaan. Pengurus gedung PT. Aneka Usaha tidak bertanggung jawab apabila terjadi pembatalan saat melakukan penyewaan gedung yang telah dikonfirmasi pengguna sebelumnya. Terimakasih</p>
                </div>
                
                <?php if (!empty($success)): ?>
                    <button class="btn-pesan" onclick="alert('Silakan login terlebih dahulu untuk melanjutkan pemesanan.')">Pesan Sekarang</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        const startDateInput = document.querySelector('input[name="tanggal_mulai"]');
        const endDateInput = document.querySelector('input[name="tanggal_selesai"]');
        
        if (startDateInput) {
            startDateInput.setAttribute('min', today);
        }
        if (endDateInput) {
            endDateInput.setAttribute('min', today);
        }
        
        // Update end date minimum when start date changes
        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', function() {
                endDateInput.setAttribute('min', this.value);
            });
        }
    </script>
</body>
</html>
