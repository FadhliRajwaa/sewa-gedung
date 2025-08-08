<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Get booking ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_updated = isset($_GET['updated']) ? true : false;

if ($id <= 0) {
    header("Location: data_pemesanan.php?error=Invalid booking ID");
    exit;
}

// Show success message if redirected after update
if ($is_updated) {
    $success = "Status pemesanan berhasil diupdate!";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $status = $_POST['status'] ?? '';
        $catatan_admin = $_POST['catatan_admin'] ?? '';
        $status_pembayaran_manual = $_POST['status_pembayaran'] ?? '';
        $kebutuhan_tambahan = $_POST['kebutuhan_tambahan'] ?? '';
        
        // Handle file upload for bukti pembayaran
        $bukti_pembayaran_path = '';
        if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_tmp = $_FILES['bukti_pembayaran']['tmp_name'];
            $file_name = $_FILES['bukti_pembayaran']['name'];
            $file_size = $_FILES['bukti_pembayaran']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validate file
            $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($file_ext, $allowed_types)) {
                throw new Exception("Format file tidak didukung. Gunakan JPG, PNG, atau PDF.");
            }
            
            if ($file_size > $max_size) {
                throw new Exception("Ukuran file terlalu besar. Maksimal 5MB.");
            }
            
            // Generate unique filename
            $new_filename = 'bukti_' . $id . '_' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $bukti_pembayaran_path = $new_filename;
            } else {
                throw new Exception("Gagal mengupload file bukti pembayaran.");
            }
        }
        
        // Update booking - need to check what columns exist for updates
        $update_success = false;
        $payment_status = '';
        
        // Map form status to payment status
        switch ($status) {
            case 'confirmed':
            case 'completed':
                $payment_status = 'Lunas';
                break;
            case 'pending':
            case 'cancelled':
            default:
                $payment_status = 'Belum Lunas';
                break;
        }
        
        // Try to add status columns to pemesanan if not exists
        // First check if columns already exist
        $status_exists = false;
        $catatan_exists = false;
        $updated_exists = false;
        
        $check_columns = mysqli_query($conn, "SHOW COLUMNS FROM pemesanan");
        while ($col = mysqli_fetch_assoc($check_columns)) {
            if ($col['Field'] === 'status') $status_exists = true;
            if ($col['Field'] === 'catatan_admin') $catatan_exists = true;
            if ($col['Field'] === 'updated_at') $updated_exists = true;
        }
        
        // Add columns if they don't exist
        if (!$status_exists) {
            $result = mysqli_query($conn, "ALTER TABLE pemesanan ADD COLUMN status varchar(50) DEFAULT 'pending'");
            if (!$result) {
                error_log("Failed to add status column: " . mysqli_error($conn));
            }
        }
        if (!$catatan_exists) {
            $result = mysqli_query($conn, "ALTER TABLE pemesanan ADD COLUMN catatan_admin text");
            if (!$result) {
                error_log("Failed to add catatan_admin column: " . mysqli_error($conn));
            }
        }
        if (!$updated_exists) {
            $result = mysqli_query($conn, "ALTER TABLE pemesanan ADD COLUMN updated_at timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP");
            if (!$result) {
                error_log("Failed to add updated_at column: " . mysqli_error($conn));
            }
        }
        
        // Determine final payment status - manual override takes precedence
        $payment_status = 'belum lunas'; // default
        if (!empty($status_pembayaran_manual)) {
            $payment_status = $status_pembayaran_manual;
        } elseif ($bukti_pembayaran_path && $status === 'dikonfirmasi') {
            $payment_status = 'lunas';
        }
        
        // Always update pemesanan table first
        $update_query = "UPDATE pemesanan SET 
                        status = ?, 
                        catatan_admin = ?,
                        kebutuhan_tambahan = ?,
                        updated_at = NOW()
                        WHERE id_pemesanan = ?";
        
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "sssi", $status, $catatan_admin, $kebutuhan_tambahan, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $update_success = true;
            
            // Also update pembayaran table if it exists
            $pembayaran_check = mysqli_query($conn, "SHOW TABLES LIKE 'pembayaran'");
            if (mysqli_num_rows($pembayaran_check) > 0) {
                // Check if record exists in pembayaran table
                $check_payment = mysqli_query($conn, "SELECT id_pembayaran FROM pembayaran WHERE id_pemesanan = $id");
                
                if (mysqli_num_rows($check_payment) > 0) {
                    // Update existing payment record
                    if ($bukti_pembayaran_path) {
                        $payment_update = "UPDATE pembayaran SET status_pembayaran = ?, bukti_pembayaran = ?, tanggal_upload = NOW() WHERE id_pemesanan = ?";
                        $payment_stmt = mysqli_prepare($conn, $payment_update);
                        mysqli_stmt_bind_param($payment_stmt, "ssi", $payment_status, $bukti_pembayaran_path, $id);
                    } else {
                        $payment_update = "UPDATE pembayaran SET status_pembayaran = ? WHERE id_pemesanan = ?";
                        $payment_stmt = mysqli_prepare($conn, $payment_update);
                        mysqli_stmt_bind_param($payment_stmt, "si", $payment_status, $id);
                    }
                    mysqli_stmt_execute($payment_stmt);
                } else {
                    // Create new payment record if doesn't exist
                    if ($bukti_pembayaran_path) {
                        $payment_insert = "INSERT INTO pembayaran (id_pemesanan, status_pembayaran, bukti_pembayaran, tanggal_upload) VALUES (?, ?, ?, NOW())";
                        $payment_stmt = mysqli_prepare($conn, $payment_insert);
                        mysqli_stmt_bind_param($payment_stmt, "iss", $id, $payment_status, $bukti_pembayaran_path);
                    } else {
                        $payment_insert = "INSERT INTO pembayaran (id_pemesanan, status_pembayaran) VALUES (?, ?)";
                        $payment_stmt = mysqli_prepare($conn, $payment_insert);
                        mysqli_stmt_bind_param($payment_stmt, "is", $id, $payment_status);
                    }
                    mysqli_stmt_execute($payment_stmt);
                }
            }
        }
        
        if ($update_success) {
            $success_msg = "Status pemesanan berhasil diupdate menjadi: " . ucfirst($status) . " (Pembayaran: $payment_status)";
            if ($bukti_pembayaran_path) {
                $success_msg .= " dan bukti pembayaran berhasil diupload.";
            }
            $success = $success_msg;
            // Reload the page to show updated data
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'pemesanan_edit.php?id=$id&updated=1';
                }, 2000);
            </script>";
        } else {
            $error = "Failed to update booking: " . mysqli_error($conn);
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Fetch booking data
$booking = null;
try {
    // Check available columns
    $columns_check = mysqli_query($conn, "SHOW COLUMNS FROM pemesanan");
    $columns = [];
    while ($col = mysqli_fetch_assoc($columns_check)) {
        $columns[] = $col['Field'];
    }
    
    // Build query based on available columns
    $select_fields = [];
    $select_fields[] = 'p.id_pemesanan as id';
    $select_fields[] = 'p.id_penyewa';
    $select_fields[] = 'p.id_acara';
    $select_fields[] = 'p.tanggal_sewa';
    $select_fields[] = 'p.tanggal_selesai';
    $select_fields[] = 'p.durasi';
    $select_fields[] = 'p.kebutuhan_tambahan';
    $select_fields[] = 'p.total';
    $select_fields[] = 'p.metode_pembayaran';
    $select_fields[] = 'p.tanggal_pesan';
    $select_fields[] = 'p.tipe_pesanan';
    
    // Add virtual columns for compatibility - check if columns exist first
    $select_fields[] = "COALESCE(p.kebutuhan_tambahan, 'None') as fasilitas_tambahan";
    $select_fields[] = "p.total as total_biaya";
    $select_fields[] = "DATE(p.tanggal_sewa) as tanggal_acara";
    $select_fields[] = "0 as jumlah_tamu";
    
    // Check if status column exists before using it
    $has_status = in_array('status', $columns);
    $has_catatan_admin = in_array('catatan_admin', $columns);
    
    if ($has_status) {
        $select_fields[] = "COALESCE(p.status, 'pending') as status";
    } else {
        $select_fields[] = "'pending' as status";
    }
    
    if ($has_catatan_admin) {
        $select_fields[] = "COALESCE(p.catatan_admin, '') as catatan_admin";
    } else {
        $select_fields[] = "'' as catatan_admin";
    }
    
    $select_fields[] = "p.tanggal_pesan as created_at";
    
    $query = "SELECT " . implode(', ', $select_fields) . " FROM pemesanan p WHERE p.id_pemesanan = ?";
    
    // Try to join with penyewa table if possible
    $penyewa_check = mysqli_query($conn, "SHOW TABLES LIKE 'penyewa'");
    if (mysqli_num_rows($penyewa_check) > 0) {
        $penyewa_columns_check = mysqli_query($conn, "SHOW COLUMNS FROM penyewa");
        $penyewa_columns = [];
        while ($col = mysqli_fetch_assoc($penyewa_columns_check)) {
            $penyewa_columns[] = $col['Field'];
        }
        
        // Check for acara table too
        $acara_check = mysqli_query($conn, "SHOW TABLES LIKE 'acara'");
        $acara_exists = mysqli_num_rows($acara_check) > 0;
        
        // Add penyewa fields
        $select_fields[] = "COALESCE(py.nama_lengkap, py.nama_instansi, 'Customer') as nama_penyewa";
        
        if ($acara_exists) {
            // Add acara fields
            $select_fields[] = "COALESCE(a.nama_acara, 'Event') as jenis_acara";
            
            // Check for pembayaran table and get payment status
            $pembayaran_check = mysqli_query($conn, "SHOW TABLES LIKE 'pembayaran'");
            if (mysqli_num_rows($pembayaran_check) > 0) {
                $select_fields[] = "COALESCE(pb.status_pembayaran, 'Belum Lunas') as status_pembayaran";
                $select_fields[] = "COALESCE(pb.bukti_pembayaran, '') as bukti_pembayaran";
                $select_fields[] = "pb.tanggal_upload";
                
                $query = "SELECT " . implode(', ', $select_fields) . " 
                          FROM pemesanan p 
                          LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa 
                          LEFT JOIN acara a ON p.id_acara = a.id_acara
                          LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
                          WHERE p.id_pemesanan = ?";
            } else {
                $select_fields[] = "'Belum Lunas' as status_pembayaran";
                $select_fields[] = "'' as bukti_pembayaran";
                $select_fields[] = "NULL as tanggal_upload";
                $query = "SELECT " . implode(', ', $select_fields) . " 
                          FROM pemesanan p 
                          LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa 
                          LEFT JOIN acara a ON p.id_acara = a.id_acara
                          WHERE p.id_pemesanan = ?";
            }
        } else {
            $select_fields[] = "'Event' as jenis_acara";
            $select_fields[] = "'Belum Lunas' as status_pembayaran";
            $select_fields[] = "'' as bukti_pembayaran";
            $select_fields[] = "NULL as tanggal_upload";
            $query = "SELECT " . implode(', ', $select_fields) . " 
                      FROM pemesanan p 
                      LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa 
                      WHERE p.id_pemesanan = ?";
        }
    } else {
        $select_fields[] = "'Customer' as nama_penyewa";
        $select_fields[] = "'Event' as jenis_acara";
        $select_fields[] = "'Belum Lunas' as status_pembayaran";
        $select_fields[] = "'' as bukti_pembayaran";
        $select_fields[] = "NULL as tanggal_upload";
    }
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($booking = mysqli_fetch_assoc($result)) {
        // Data found successfully
    } else {
        $error = "Data pemesanan dengan ID #$id tidak ditemukan.";
    }
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage() . "\nData pemesanan tidak ditemukan.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pemesanan - Admin</title>
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
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: var(--white);
            padding: 32px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header p {
            opacity: 0.9;
            font-size: 16px;
        }

        .content {
            padding: 32px;
        }

        .booking-info {
            background: var(--gray-light);
            padding: 24px;
            border-radius: var(--radius);
            margin-bottom: 32px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .info-item {
            background: var(--white);
            padding: 16px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }

        .info-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gray);
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
            max-width: 100%;
        }

        .form-section {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            margin-bottom: 24px;
        }

        .form-section h3 {
            color: var(--dark);
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: var(--radius);
            font-size: 14px;
            transition: all 0.2s ease;
            background: var(--white);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        select.form-control {
            cursor: pointer;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .form-text {
            font-size: 12px;
            margin-top: 5px;
            opacity: 0.7;
        }

        .text-muted {
            color: var(--gray);
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

        .btn-secondary {
            background: var(--gray-light);
            color: var(--gray);
            border: 1px solid var(--border);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        .alert {
            padding: 16px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            border: 1px solid;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border-color: var(--danger);
            color: var(--danger);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: var(--success);
            color: var(--success);
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

        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .badge-info {
            background: rgba(6, 182, 212, 0.1);
            color: var(--accent);
        }

        .file-upload {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-upload input[type=file] {
            position: absolute;
            left: -9999px;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border: 2px dashed var(--border);
            border-radius: var(--radius);
            background: var(--gray-light);
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }

        .file-upload-label:hover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
        }

        .file-upload-label.dragover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.1);
        }

        .file-upload-icon {
            font-size: 24px;
            color: var(--gray);
            margin-bottom: 8px;
        }

        .file-upload-text {
            color: var(--gray);
            font-size: 14px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .header {
                padding: 24px 20px;
            }

            .header h1 {
                font-size: 24px;
            }

            .content {
                padding: 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .btn-actions {
                flex-direction: column;
            }

            .btn {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-edit"></i> Edit Pemesanan</h1>
            <p>Kelola status dan informasi pemesanan</p>
        </div>

        <div class="content">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
                
                <div class="btn-actions">
                    <a href="data_pemesanan.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Data Pemesanan
                    </a>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($booking) && $booking): ?>
                <!-- Booking Information -->
                <div class="booking-info">
                    <h3 style="margin-bottom: 20px; color: var(--dark);">
                        <i class="fas fa-info-circle"></i> Informasi Pemesanan
                    </h3>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">ID Pemesanan</div>
                            <div class="info-value">#<?= $booking['id'] ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Nama Penyewa</div>
                            <div class="info-value"><?= htmlspecialchars($booking['nama_penyewa']) ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Jenis Acara</div>
                            <div class="info-value"><?= htmlspecialchars($booking['jenis_acara']) ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Tanggal Acara</div>
                            <div class="info-value"><?= date('d/m/Y', strtotime($booking['tanggal_sewa'] ?? $booking['tanggal_acara'])) ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Tanggal Selesai</div>
                            <div class="info-value"><?= isset($booking['tanggal_selesai']) ? date('d/m/Y', strtotime($booking['tanggal_selesai'])) : 'Tidak tersedia' ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Durasi</div>
                            <div class="info-value"><?= isset($booking['durasi']) ? $booking['durasi'] . ' hari' : 'Tidak tersedia' ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Total Biaya</div>
                            <div class="info-value">Rp <?= number_format($booking['total'], 0, ',', '.') ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Metode Pembayaran</div>
                            <div class="info-value"><?= isset($booking['metode_pembayaran']) ? ucfirst(str_replace('_', ' ', $booking['metode_pembayaran'])) : 'Tidak tersedia' ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Kebutuhan Tambahan</div>
                            <div class="info-value"><?= isset($booking['kebutuhan_tambahan']) && $booking['kebutuhan_tambahan'] ? htmlspecialchars($booking['kebutuhan_tambahan']) : 'Tidak ada' ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Status Saat Ini</div>
                            <div class="info-value">
                                <?php
                                $status_class = 'badge-info';
                                switch (strtolower($booking['status'])) {
                                    case 'confirmed':
                                    case 'completed':
                                        $status_class = 'badge-success';
                                        break;
                                    case 'cancelled':
                                        $status_class = 'badge-danger';
                                        break;
                                    case 'pending':
                                        $status_class = 'badge-warning';
                                        break;
                                }
                                ?>
                                <span class="badge <?= $status_class ?>">
                                    <?= htmlspecialchars($booking['status']) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Status Pembayaran</div>
                            <div class="info-value">
                                <?php
                                $payment_class = $booking['status_pembayaran'] === 'Lunas' ? 'badge-success' : 'badge-warning';
                                ?>
                                <span class="badge <?= $payment_class ?>">
                                    <?= htmlspecialchars($booking['status_pembayaran']) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Tanggal Dibuat</div>
                            <div class="info-value"><?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?></div>
                        </div>
                        
                        <?php if (!empty($booking['bukti_pembayaran'])): ?>
                        <div class="info-item">
                            <div class="info-label">Bukti Pembayaran</div>
                            <div class="info-value">
                                <?php
                                $file_ext = strtolower(pathinfo($booking['bukti_pembayaran'], PATHINFO_EXTENSION));
                                $file_path = '../uploads/' . $booking['bukti_pembayaran'];
                                ?>
                                <div style="margin-top: 8px;">
                                    <?php if (in_array($file_ext, ['jpg', 'jpeg', 'png'])): ?>
                                        <img src="<?= $file_path ?>" alt="Bukti Pembayaran" 
                                             style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 1px solid var(--border);">
                                    <?php else: ?>
                                        <i class="fas fa-file-pdf" style="font-size: 24px; color: var(--danger);"></i>
                                        <span style="margin-left: 8px;"><?= htmlspecialchars($booking['bukti_pembayaran']) ?></span>
                                    <?php endif; ?>
                                    <br>
                                    <a href="<?= $file_path ?>" target="_blank" class="btn btn-sm" style="margin-top: 8px; padding: 6px 12px; background: var(--accent); color: white; text-decoration: none; border-radius: 6px; font-size: 12px;">
                                        <i class="fas fa-external-link-alt"></i> Lihat File
                                    </a>
                                    <?php if (!empty($booking['tanggal_upload'])): ?>
                                        <br><small style="color: var(--gray); margin-top: 4px; display: block;">
                                            Diupload: <?= date('d/m/Y H:i', strtotime($booking['tanggal_upload'])) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Edit Form -->
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-section">
                        <h3><i class="fas fa-cogs"></i> Update Status & Catatan</h3>
                        
                        <div class="form-group">
                            <label for="status" class="form-label">
                                <i class="fas fa-flag"></i> Status Pemesanan
                            </label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending" <?= $booking['status'] === 'pending' ? 'selected' : '' ?>>Pending (Belum Lunas)</option>
                                <option value="confirmed" <?= $booking['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed (Lunas)</option>
                                <option value="completed" <?= $booking['status'] === 'completed' ? 'selected' : '' ?>>Completed (Selesai - Lunas)</option>
                                <option value="cancelled" <?= $booking['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled (Dibatalkan)</option>
                            </select>
                            <small class="form-text text-muted">
                                Status "Confirmed" dan "Completed" akan mengubah pembayaran menjadi "Lunas"
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="catatan_admin" class="form-label">
                                <i class="fas fa-sticky-note"></i> Catatan Admin
                            </label>
                            <textarea name="catatan_admin" id="catatan_admin" class="form-control" 
                                      placeholder="Tambahkan catatan atau keterangan untuk pemesanan ini..."><?= htmlspecialchars($booking['catatan_admin'] === 'None' || $booking['catatan_admin'] === '' ? '' : $booking['catatan_admin']) ?></textarea>
                        </div>
                        
                        <!-- Edit Kebutuhan Tambahan -->
                        <div class="form-group">
                            <label for="kebutuhan_tambahan" class="form-label">
                                <i class="fas fa-clipboard-list"></i> Kebutuhan Tambahan
                            </label>
                            <textarea name="kebutuhan_tambahan" id="kebutuhan_tambahan" class="form-control" rows="4" 
                                      placeholder="Kebutuhan khusus untuk acara (dekorasi, catering, sound system, dll.)"><?= htmlspecialchars($booking['kebutuhan_tambahan'] ?? '') ?></textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Contoh: Dekorasi khusus, catering, sound system tambahan, dll.
                            </small>
                        </div>
                        
                        <!-- Status Pembayaran Manual -->
                        <div class="form-group">
                            <label for="status_pembayaran" class="form-label">
                                <i class="fas fa-credit-card"></i> Status Pembayaran
                            </label>
                            <select name="status_pembayaran" id="status_pembayaran" class="form-control">
                                <option value="Belum Lunas" <?= $booking['status_pembayaran'] === 'Belum Lunas' ? 'selected' : '' ?>>Belum Lunas</option>
                                <option value="Lunas" <?= $booking['status_pembayaran'] === 'Lunas' ? 'selected' : '' ?>>Lunas</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Ubah status pembayaran secara manual jika diperlukan
                            </small>
                        </div>
                    </div>

                    <!-- Upload Bukti Pembayaran Section -->
                    <div class="form-section">
                        <h3><i class="fas fa-receipt"></i> Upload Bukti Pembayaran</h3>
                        
                        <?php if (!empty($booking['bukti_pembayaran'])): ?>
                        <div class="alert" style="background: rgba(6, 182, 212, 0.1); border-color: var(--accent); color: var(--accent); margin-bottom: 20px;">
                            <i class="fas fa-info-circle"></i>
                            Bukti pembayaran sudah ada. Upload file baru untuk mengganti yang lama.
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="bukti_pembayaran" class="form-label">
                                <i class="fas fa-upload"></i> Pilih File Bukti Pembayaran
                            </label>
                            <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" 
                                   class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            <small class="form-text text-muted">
                                <i class="fas fa-exclamation-circle"></i>
                                Format yang didukung: JPG, PNG, PDF | Maksimal ukuran: 5MB
                            </small>
                        </div>
                        
                        <div id="file-preview" style="margin-top: 15px; display: none;">
                            <div style="padding: 12px; background: var(--gray-light); border-radius: var(--radius); border: 1px solid var(--border);">
                                <i class="fas fa-file"></i>
                                <span id="file-name"></span>
                                <span id="file-size" style="color: var(--gray); margin-left: 10px;"></span>
                                <button type="button" id="remove-file" style="background: none; border: none; color: var(--danger); margin-left: 10px; cursor: pointer;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="btn-actions">
                        <a href="data_pemesanan.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Kembali
                        </a>
                        <a href="pemesanan_view.php?id=<?= $booking['id'] ?>" class="btn btn-secondary">
                            <i class="fas fa-eye"></i>
                            Lihat Detail
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Update Pemesanan
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // File upload handling
        const fileInput = document.getElementById('bukti_pembayaran');
        const filePreview = document.getElementById('file-preview');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');
        const removeFileBtn = document.getElementById('remove-file');

        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Format file tidak didukung. Gunakan JPG, PNG, atau PDF.');
                        this.value = '';
                        filePreview.style.display = 'none';
                        return;
                    }

                    // Validate file size (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Ukuran file terlalu besar. Maksimal 5MB.');
                        this.value = '';
                        filePreview.style.display = 'none';
                        return;
                    }

                    // Show file preview
                    fileName.textContent = file.name;
                    fileSize.textContent = '(' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
                    filePreview.style.display = 'block';
                } else {
                    filePreview.style.display = 'none';
                }
            });

            removeFileBtn.addEventListener('click', function() {
                fileInput.value = '';
                filePreview.style.display = 'none';
            });
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const status = document.getElementById('status').value;
            const catatan = document.getElementById('catatan_admin').value;
            
            if (!status) {
                e.preventDefault();
                alert('Harap pilih status pemesanan!');
                return false;
            }
            
            // Check if file is selected and valid
            if (fileInput && fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                
                if (!allowedTypes.includes(file.type)) {
                    e.preventDefault();
                    alert('Format file bukti pembayaran tidak didukung!');
                    return false;
                }
                
                if (file.size > 5 * 1024 * 1024) {
                    e.preventDefault();
                    alert('Ukuran file bukti pembayaran terlalu besar!');
                    return false;
                }
            }
            
            // Confirm before submit
            let confirmMsg = 'Apakah Anda yakin ingin mengupdate pemesanan ini?';
            if (fileInput && fileInput.files.length > 0) {
                confirmMsg += '\n\nFile bukti pembayaran akan diupload.';
            }
            
            if (!confirm(confirmMsg)) {
                e.preventDefault();
                return false;
            }
        });

        // Status change handling
        document.getElementById('status').addEventListener('change', function() {
            const status = this.value;
            const catatanField = document.getElementById('catatan_admin');
            
            // Auto-suggest notes based on status
            if (status === 'cancelled' && !catatanField.value.trim()) {
                catatanField.placeholder = 'Alasan pembatalan pemesanan...';
            } else if (status === 'confirmed' && !catatanField.value.trim()) {
                catatanField.placeholder = 'Pemesanan dikonfirmasi. Silakan persiapkan fasilitas sesuai permintaan.';
            } else if (status === 'completed' && !catatanField.value.trim()) {
                catatanField.placeholder = 'Acara telah selesai dilaksanakan.';
            }
        });
    </script>
</body>
</html>
