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
        
        // Always update pemesanan table first
        $update_query = "UPDATE pemesanan SET 
                        status = ?, 
                        catatan_admin = ?,
                        updated_at = NOW()
                        WHERE id_pemesanan = ?";
        
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "ssi", $status, $catatan_admin, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $update_success = true;
            
            // Also update pembayaran table if it exists
            $pembayaran_check = mysqli_query($conn, "SHOW TABLES LIKE 'pembayaran'");
            if (mysqli_num_rows($pembayaran_check) > 0) {
                // Check if record exists in pembayaran table
                $check_payment = mysqli_query($conn, "SELECT id_pembayaran FROM pembayaran WHERE id_pemesanan = $id");
                
                if (mysqli_num_rows($check_payment) > 0) {
                    // Update existing payment record
                    $payment_update = "UPDATE pembayaran SET status_pembayaran = ? WHERE id_pemesanan = ?";
                    $payment_stmt = mysqli_prepare($conn, $payment_update);
                    mysqli_stmt_bind_param($payment_stmt, "si", $payment_status, $id);
                    mysqli_stmt_execute($payment_stmt);
                } else {
                    // Create new payment record if doesn't exist
                    $payment_insert = "INSERT INTO pembayaran (id_pemesanan, status_pembayaran) VALUES (?, ?)";
                    $payment_stmt = mysqli_prepare($conn, $payment_insert);
                    mysqli_stmt_bind_param($payment_stmt, "is", $id, $payment_status);
                    mysqli_stmt_execute($payment_stmt);
                }
            }
        }
        
        if ($update_success) {
            $success = "Status pemesanan berhasil diupdate menjadi: " . ucfirst($status) . " (Pembayaran: $payment_status)";
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
    $select_fields[] = "'09:00' as jam_mulai";
    $select_fields[] = "'17:00' as jam_selesai";
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
                
                $query = "SELECT " . implode(', ', $select_fields) . " 
                          FROM pemesanan p 
                          LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa 
                          LEFT JOIN acara a ON p.id_acara = a.id_acara
                          LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
                          WHERE p.id_pemesanan = ?";
            } else {
                $select_fields[] = "'Belum Lunas' as status_pembayaran";
                $query = "SELECT " . implode(', ', $select_fields) . " 
                          FROM pemesanan p 
                          LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa 
                          LEFT JOIN acara a ON p.id_acara = a.id_acara
                          WHERE p.id_pemesanan = ?";
            }
        } else {
            $select_fields[] = "'Event' as jenis_acara";
            $select_fields[] = "'Belum Lunas' as status_pembayaran";
            $query = "SELECT " . implode(', ', $select_fields) . " 
                      FROM pemesanan p 
                      LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa 
                      WHERE p.id_pemesanan = ?";
        }
    } else {
        $select_fields[] = "'Customer' as nama_penyewa";
        $select_fields[] = "'Event' as jenis_acara";
        $select_fields[] = "'Belum Lunas' as status_pembayaran";
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
                            <div class="info-label">Waktu</div>
                            <div class="info-value"><?= $booking['jam_mulai'] ?> - <?= $booking['jam_selesai'] ?></div>
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
                    </div>
                </div>

                <!-- Edit Form -->
                <form method="POST" action="">
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
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const status = document.getElementById('status').value;
            const catatan = document.getElementById('catatan_admin').value;
            
            if (!status) {
                e.preventDefault();
                alert('Harap pilih status pemesanan!');
                return false;
            }
            
            // Confirm before submit
            if (!confirm('Apakah Anda yakin ingin mengupdate pemesanan ini?')) {
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
