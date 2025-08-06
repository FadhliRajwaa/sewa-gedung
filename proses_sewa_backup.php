<?php
session_start();
require_once 'config.php';
require_once 'includes/pricing.php';

// Check if user is logged in
if (!isset($_SESSION['id_penyewa'])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

// Get event type from URL parameter
$acara_type = isset($_GET['acara']) ? $_GET['acara'] : '';

// Get dates from URL parameters
$tanggal_mulai_param = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : '';
$tanggal_selesai_param = isset($_GET['tanggal_selesai']) ? $_GET['tanggal_selesai'] : '';

// Validate event type
$valid_events = ['seminar', 'pernikahan', 'rapat'];
if (!in_array($acara_type, $valid_events)) {
    header("Location: gedung.php");
    exit();
}

// Validate dates if provided
if ($tanggal_mulai_param && $tanggal_selesai_param) {
    try {
        $start_date = new DateTime($tanggal_mulai_param);
        $end_date = new DateTime($tanggal_selesai_param);
        $today = new DateTime();
        
        if ($start_date < $today) {
            $date_error = "Tanggal mulai tidak boleh kurang dari hari ini";
        } elseif ($end_date < $start_date) {
            $date_error = "Tanggal selesai harus sama atau setelah tanggal mulai";
        }
    } catch (Exception $e) {
        $date_error = "Format tanggal tidak valid";
    }
}

// Get event data from database
try {
    $event_mapping = [
        'seminar' => 3,
        'pernikahan' => 1, 
        'rapat' => 2
    ];
    
    $event_id = $event_mapping[$acara_type];
    $event_pricing = getPriceByEventId($pdo, $event_id);
    
    if (!$event_pricing) {
        throw new Exception("Event not found");
    }
    
    // Get event details
    $stmt = $pdo->prepare("SELECT * FROM acara WHERE id_acara = ? AND status = 'tersedia'");
    $stmt->execute([$event_id]);
    $event_details = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$event_details) {
        throw new Exception("Event not available");
    }
    
    // Calculate price if dates are provided
    $total_price = 0;
    $days = 0;
    if ($tanggal_mulai_param && $tanggal_selesai_param && !isset($date_error)) {
        $start_date = new DateTime($tanggal_mulai_param);
        $end_date = new DateTime($tanggal_selesai_param);
        $days = $start_date->diff($end_date)->days + 1;
        $total_price = $event_pricing['price'] * $days;
    }
    
} catch (Exception $e) {
    $error_message = "Terjadi kesalahan: " . $e->getMessage();
}

// Process booking form
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? $tanggal_mulai_param;
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? $tanggal_selesai_param;
    $jumlah_orang = $_POST['jumlah_orang'] ?? 1;
    
    try {
        // Validate dates
        $start_date = new DateTime($tanggal_mulai);
        $end_date = new DateTime($tanggal_selesai);
        $today = new DateTime();
        
        if ($start_date < $today) {
            throw new Exception("Tanggal mulai tidak boleh kurang dari hari ini");
        }
        
        if ($end_date < $start_date) {
            throw new Exception("Tanggal selesai harus sama atau setelah tanggal mulai");
        }
        
        // Check availability
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as conflict FROM pemesanan 
            WHERE id_acara = ? AND 
            ((tanggal_sewa <= ? AND tanggal_selesai >= ?) OR 
             (tanggal_sewa <= ? AND tanggal_selesai >= ?))
        ");
        $stmt->execute([$event_id, $tanggal_mulai, $tanggal_mulai, $tanggal_selesai, $tanggal_selesai]);
        $conflict = $stmt->fetch(PDO::FETCH_ASSOC)['conflict'];
        
        if ($conflict > 0) {
            throw new Exception("Tanggal yang dipilih sudah dipesan. Silakan pilih tanggal lain.");
        }
        
        // Calculate total price
        $days = $start_date->diff($end_date)->days + 1;
        $total_price = $event_pricing['price'] * $days;
        
        // Insert booking
        $stmt = $pdo->prepare("
            INSERT INTO pemesanan (id_penyewa, id_acara, tanggal_sewa, tanggal_selesai, jumlah_orang, total, tanggal_pesan, status_pemesanan) 
            VALUES (?, ?, ?, ?, ?, ?, NOW(), 'pending')
        ");
        
        $stmt->execute([
            $_SESSION['id_penyewa'], 
            $event_id, 
            $tanggal_mulai, 
            $tanggal_selesai, 
            $jumlah_orang, 
            $total_price
        ]);
        
        $booking_id = $pdo->lastInsertId();
        
        // Redirect to payment page
        header("Location: pembayaran.php?id=" . $booking_id);
        exit();
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Booking - <?= ucfirst($acara_type) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .booking-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .booking-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
        }
        
        .booking-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        }
        
        .booking-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        
        .booking-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-top: 20px solid #764ba2;
        }
        
        .booking-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .booking-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 400;
        }
        
        .booking-body {
            padding: 50px 40px;
        }
        
        .summary-section {
            background: linear-gradient(145deg, #f8f9ff 0%, #e8eeff 100%);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .event-info {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid rgba(102, 126, 234, 0.1);
        }
        
        .event-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            color: white;
            font-size: 1.5rem;
        }
        
        .event-details h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .event-price {
            font-size: 1.1rem;
            color: #667eea;
            font-weight: 600;
        }
        
        .date-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .date-item {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 2px solid rgba(102, 126, 234, 0.1);
        }
        
        .date-item i {
            font-size: 1.5rem;
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .date-item h5 {
            font-size: 0.9rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .date-item .date-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
        }
        
        .price-breakdown {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 2px solid rgba(102, 126, 234, 0.1);
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        
        .price-row:last-child {
            border-bottom: none;
            border-top: 2px solid #667eea;
            margin-top: 15px;
            padding-top: 20px;
            font-size: 1.2rem;
            font-weight: 700;
        }
        
        .price-label {
            color: #666;
            font-weight: 500;
        }
        
        .price-value {
            font-weight: 600;
            color: #333;
        }
        
        .total-value {
            color: #28a745;
            font-size: 1.4rem;
        }
        
        .form-section {
            margin-top: 30px;
        }
        
        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }
        
        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            padding: 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 15px 30px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            flex: 1;
            min-width: 200px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 12px;
            padding: 15px 30px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3);
        }
        
        .alert {
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 25px;
            border: none;
            font-weight: 500;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a5a);
            color: white;
        }
        
        .no-dates-message {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .no-dates-message i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .booking-body {
                padding: 30px 20px;
            }
            
            .booking-header {
                padding: 30px 20px;
            }
            
            .booking-header h1 {
                font-size: 1.8rem;
            }
            
            .date-summary {
                grid-template-columns: 1fr;
            }
            
            .event-info {
                flex-direction: column;
                text-align: center;
            }
            
            .event-icon {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-primary, .btn-secondary {
                min-width: auto;
                width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .booking-container {
                padding: 0 10px;
            }
            
            .summary-section, .booking-body {
                padding: 20px 15px;
            }
            
            .date-item {
                padding: 15px;
            }
            
            .price-breakdown {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <div class="booking-card">
            <div class="booking-header">
                <h1><i class="fas fa-calendar-check"></i> Konfirmasi Booking</h1>
                <p>Periksa detail pemesanan Anda sebelum melanjutkan</p>
            </div>
            
            <div class="booking-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($date_error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($date_error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($event_details)): ?>
                    <div class="summary-section">
                        <!-- Event Information -->
                        <div class="event-info">
                            <div class="event-icon">
                                <?php
                                $icons = [
                                    'seminar' => 'fas fa-chalkboard-teacher',
                                    'pernikahan' => 'fas fa-heart',
                                    'rapat' => 'fas fa-users'
                                ];
                                ?>
                                <i class="<?= $icons[$acara_type] ?? 'fas fa-calendar' ?>"></i>
                            </div>
                            <div class="event-details">
                                <h3><?= htmlspecialchars($event_details['nama_acara']) ?></h3>
                                <div class="event-price">Rp <?= number_format($event_pricing['price'], 0, ',', '.') ?> per hari</div>
                            </div>
                        </div>
                        
                        <?php if ($tanggal_mulai_param && $tanggal_selesai_param && !isset($date_error)): ?>
                            <!-- Date Summary -->
                            <div class="date-summary">
                                <div class="date-item">
                                    <i class="fas fa-calendar-day"></i>
                                    <h5>Tanggal Mulai</h5>
                                    <div class="date-value"><?= date('d M Y', strtotime($tanggal_mulai_param)) ?></div>
                                </div>
                                <div class="date-item">
                                    <i class="fas fa-calendar-check"></i>
                                    <h5>Tanggal Selesai</h5>
                                    <div class="date-value"><?= date('d M Y', strtotime($tanggal_selesai_param)) ?></div>
                                </div>
                            </div>
                            
                            <!-- Price Breakdown -->
                            <div class="price-breakdown">
                                <div class="price-row">
                                    <span class="price-label">Harga per hari</span>
                                    <span class="price-value">Rp <?= number_format($event_pricing['price'], 0, ',', '.') ?></span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Durasi</span>
                                    <span class="price-value"><?= $days ?> hari</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Subtotal</span>
                                    <span class="price-value">Rp <?= number_format($total_price, 0, ',', '.') ?></span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Total Pembayaran</span>
                                    <span class="price-value total-value">Rp <?= number_format($total_price, 0, ',', '.') ?></span>
                                </div>
                            </div>
                            
                            <!-- Booking Form -->
                            <form method="POST" class="form-section">
                                <input type="hidden" name="tanggal_mulai" value="<?= htmlspecialchars($tanggal_mulai_param) ?>">
                                <input type="hidden" name="tanggal_selesai" value="<?= htmlspecialchars($tanggal_selesai_param) ?>">
                                
                                <div class="form-group">
                                    <label for="jumlah_orang">
                                        <i class="fas fa-users"></i> Jumlah Orang
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="jumlah_orang" 
                                           name="jumlah_orang" 
                                           min="1" 
                                           value="<?= isset($_POST['jumlah_orang']) ? htmlspecialchars($_POST['jumlah_orang']) : '1' ?>"
                                           placeholder="Masukkan jumlah orang yang akan hadir"
                                           required>
                                </div>
                                
                                <div class="action-buttons">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-credit-card"></i> Lanjut ke Pembayaran
                                    </button>
                                    <a href="<?= $acara_type ?>.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Ubah Tanggal
                                    </a>
                                </div>
                            </form>
                        <?php else: ?>
                            <!-- No Dates Selected -->
                            <div class="no-dates-message">
                                <i class="fas fa-calendar-times"></i>
                                <h4>Tanggal Belum Dipilih</h4>
                                <p>Silakan kembali dan pilih tanggal terlebih dahulu</p>
                                <a href="<?= $acara_type ?>.php" class="btn btn-primary mt-3">
                                    <i class="fas fa-arrow-left"></i> Pilih Tanggal
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="no-dates-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h4>Event Tidak Ditemukan</h4>
                        <p>Event tidak ditemukan atau tidak tersedia.</p>
                        <a href="gedung.php" class="btn btn-primary mt-3">
                            <i class="fas fa-arrow-left"></i> Kembali ke Pilihan Acara
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
</body>
</html>
