<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['id_penyewa'])) {
    header('Location: login.php');
    exit;
}

$id_penyewa = $_SESSION['id_penyewa'];

// Get booking ID from URL
if (!isset($_GET['id'])) {
    header('Location: acara_saya.php');
    exit;
}

$id_pemesanan = $_GET['id'];

// Get booking details
try {
    $query = "
        SELECT p.*, a.nama_acara, a.lokasi, pb.bukti_pembayaran, pb.status_pembayaran
        FROM pemesanan p
        JOIN acara a ON p.id_acara = a.id_acara
        LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
        WHERE p.id_pemesanan = ? AND p.id_penyewa = ?
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id_pemesanan, $id_penyewa]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        header('Location: acara_saya.php');
        exit;
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Handle payment upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bukti_pembayaran'])) {
    $upload_dir = 'uploads/';
    
    // Create upload directory if not exists
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file = $_FILES['bukti_pembayaran'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
    
    if (in_array($file_extension, $allowed_extensions) && $file['size'] <= 5 * 1024 * 1024) {
        $filename = 'payment_' . $id_pemesanan . '_' . time() . '.' . $file_extension;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            try {
                // Check if payment record exists
                $check_query = "SELECT id_pembayaran FROM pembayaran WHERE id_pemesanan = ?";
                $check_stmt = $pdo->prepare($check_query);
                $check_stmt->execute([$id_pemesanan]);
                $existing_payment = $check_stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existing_payment) {
                    // Update existing payment
                    $update_query = "UPDATE pembayaran SET bukti_pembayaran = ?, tanggal_upload = NOW() WHERE id_pemesanan = ?";
                    $update_stmt = $pdo->prepare($update_query);
                    $update_stmt->execute([$filename, $id_pemesanan]);
                } else {
                    // Insert new payment
                    $insert_query = "INSERT INTO pembayaran (id_pemesanan, bukti_pembayaran, status_pembayaran, tanggal_upload) VALUES (?, ?, 'Belum Lunas', NOW())";
                    $insert_stmt = $pdo->prepare($insert_query);
                    $insert_stmt->execute([$id_pemesanan, $filename]);
                }
                
                $success_message = "Bukti pembayaran berhasil diupload. Silakan tunggu verifikasi dari admin.";
                // Refresh booking data
                $stmt->execute([$id_pemesanan, $id_penyewa]);
                $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $error_message = "Gagal menyimpan data pembayaran: " . $e->getMessage();
            }
        } else {
            $error_message = "Gagal mengupload file.";
        }
    } else {
        $error_message = "File tidak valid. Gunakan format JPG, PNG, atau PDF dengan ukuran maksimal 5MB.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - PT. Aneka Usaha</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 25px;
        }

        .nav-link:hover {
            color: #8B4513;
            background: rgba(139, 69, 19, 0.1);
            text-decoration: none;
        }

        /* Main Content */
        .main-content {
            margin-top: 100px;
            padding: 40px 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .payment-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #666;
            font-size: 1.1rem;
        }

        /* Booking Summary */
        .booking-summary {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .summary-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .summary-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .detail-label {
            font-weight: 500;
            color: #666;
        }

        .detail-value {
            font-weight: 600;
            color: #333;
        }

        .total-amount {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
            text-align: center;
        }

        .total-amount .amount {
            font-size: 1.5rem;
            font-weight: 700;
        }

        /* Payment Info */
        .payment-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .payment-info h3 {
            color: #856404;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .bank-details {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-top: 15px;
        }

        .bank-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .bank-item:last-child {
            border-bottom: none;
        }

        .bank-name {
            font-weight: 600;
            color: #333;
        }

        .bank-account {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #8B4513;
        }

        /* Upload Form */
        .upload-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .upload-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-input {
            width: 100%;
            padding: 15px;
            border: 2px dashed #ddd;
            border-radius: 10px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-input:hover {
            border-color: #8B4513;
            background: #fff;
        }

        .upload-btn {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 1rem;
        }

        .upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 69, 19, 0.3);
        }

        .upload-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Status Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #8B4513;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            color: #A0522D;
            text-decoration: none;
            transform: translateX(-5px);
        }

        /* Current Payment Status */
        .current-status {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .payment-container {
                padding: 25px 20px;
                margin: 15px;
            }

            .page-title {
                font-size: 1.8rem;
            }

            .summary-details {
                grid-template-columns: 1fr;
            }

            .bank-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .main-content {
                margin-top: 80px;
                padding: 20px 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <img src="logo.png" alt="PT. Aneka Usaha" onerror="this.style.display='none'">
                <div class="logo-text">PT. ANEKA USAHA</div>
            </div>
            
            <ul class="nav-menu">
                <li><a href="dashboard_user.php" class="nav-link"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="acara_saya.php" class="nav-link"><i class="fas fa-calendar-alt"></i> Acara Saya</a></li>
                <li><a href="akun.php" class="nav-link"><i class="fas fa-user"></i> Akun</a></li>
                <li><a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="payment-container">
                <a href="acara_saya.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Acara Saya
                </a>

                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-credit-card"></i>
                        Pembayaran Acara
                    </h1>
                    <p class="page-subtitle">
                        Silakan lakukan pembayaran sesuai dengan total biaya yang tertera
                    </p>
                </div>

                <!-- Current Payment Status -->
                <?php if ($booking['status_pembayaran']): ?>
                <div class="current-status">
                    <h4><i class="fas fa-info-circle"></i> Status Pembayaran Saat Ini</h4>
                    <span class="status-badge <?= $booking['status_pembayaran'] === 'Lunas' ? 'status-approved' : 'status-pending' ?>">
                        <?= $booking['status_pembayaran'] === 'Lunas' ? 'Pembayaran Lunas' : 'Menunggu Verifikasi' ?>
                    </span>
                </div>
                <?php endif; ?>

                <!-- Status Messages -->
                <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $success_message ?>
                </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= $error_message ?>
                </div>
                <?php endif; ?>

                <!-- Booking Summary -->
                <div class="booking-summary">
                    <h3 class="summary-title">
                        <i class="fas fa-receipt"></i>
                        Ringkasan Pesanan
                    </h3>
                    
                    <div class="summary-details">
                        <div class="detail-item">
                            <span class="detail-label">Nama Acara:</span>
                            <span class="detail-value"><?= htmlspecialchars($booking['nama_acara']) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Lokasi:</span>
                            <span class="detail-value"><?= htmlspecialchars($booking['lokasi']) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tanggal:</span>
                            <span class="detail-value"><?= date('d M Y', strtotime($booking['tanggal_sewa'])) ?> - <?= date('d M Y', strtotime($booking['tanggal_selesai'])) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Durasi:</span>
                            <span class="detail-value"><?= $booking['durasi'] ?> hari</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tipe:</span>
                            <span class="detail-value"><?= ucfirst($booking['tipe_pesanan']) ?></span>
                        </div>
                    </div>

                    <div class="total-amount">
                        <div>Total Pembayaran</div>
                        <div class="amount">Rp <?= number_format($booking['total'], 0, ',', '.') ?></div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="payment-info">
                    <h3>
                        <i class="fas fa-university"></i>
                        Informasi Pembayaran
                    </h3>
                    <p>Silakan transfer sesuai dengan total pembayaran ke salah satu rekening berikut:</p>
                    
                    <div class="bank-details">
                        <div class="bank-item">
                            <span class="bank-name">Bank BCA</span>
                            <span class="bank-account">123-456-789</span>
                        </div>
                        <div class="bank-item">
                            <span class="bank-name">Bank Mandiri</span>
                            <span class="bank-account">987-654-321</span>
                        </div>
                        <div class="bank-item">
                            <span class="bank-name">Bank BNI</span>
                            <span class="bank-account">456-789-123</span>
                        </div>
                        <div class="bank-item">
                            <span class="bank-name">Bank BRI</span>
                            <span class="bank-account">321-654-987</span>
                        </div>
                        <div class="bank-item">
                            <span>Atas Nama</span>
                            <span class="bank-account">PT. ANEKA USAHA</span>
                        </div>
                    </div>
                </div>

                <!-- Upload Payment Proof -->
                <?php if ($booking['status_pembayaran'] !== 'Lunas'): ?>
                <div class="upload-section">
                    <h3 class="upload-title">
                        <i class="fas fa-upload"></i>
                        Upload Bukti Pembayaran
                    </h3>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="form-label">Pilih File Bukti Pembayaran (JPG, PNG, PDF - Max 5MB)</label>
                            <input type="file" name="bukti_pembayaran" class="file-input" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        
                        <button type="submit" class="upload-btn">
                            <i class="fas fa-upload"></i>
                            Upload Bukti Pembayaran
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <div class="upload-section">
                    <div style="text-align: center; padding: 20px;">
                        <i class="fas fa-check-circle" style="font-size: 3rem; color: #28a745; margin-bottom: 15px;"></i>
                        <h3 style="color: #28a745; margin-bottom: 10px;">Pembayaran Sudah Lunas</h3>
                        <p style="color: #666;">Terima kasih, pembayaran Anda telah dikonfirmasi oleh admin.</p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Current Payment Proof -->
                <?php if ($booking['bukti_pembayaran']): ?>
                <div class="upload-section">
                    <h3 class="upload-title">
                        <i class="fas fa-file-image"></i>
                        Bukti Pembayaran Saat Ini
                    </h3>
                    <div style="text-align: center;">
                        <a href="uploads/<?= htmlspecialchars($booking['bukti_pembayaran']) ?>" target="_blank" class="upload-btn" style="display: inline-block; width: auto;">
                            <i class="fas fa-eye"></i>
                            Lihat Bukti Pembayaran
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        // File upload preview
        document.querySelector('.file-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSize) {
                    alert('File terlalu besar. Maksimal 5MB.');
                    this.value = '';
                    return;
                }
                
                const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Tipe file tidak diizinkan. Gunakan JPG, PNG, atau PDF.');
                    this.value = '';
                    return;
                }
            }
        });
    </script>
</body>
</html>
