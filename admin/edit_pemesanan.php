<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$id_pemesanan = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_pemesanan) {
    header("Location: data_pemesanan.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $kebutuhan_tambahan = $_POST['kebutuhan_tambahan'] ?? '';
        $tanggal_sewa = $_POST['tanggal_sewa'];
        $tanggal_selesai = $_POST['tanggal_selesai'];
        
        // Calculate new duration
        $date1 = new DateTime($tanggal_sewa);
        $date2 = new DateTime($tanggal_selesai);
        $durasi = $date2->diff($date1)->days + 1;
        
        // Get event price
        $price_query = "SELECT a.harga FROM pemesanan p 
                       JOIN acara a ON p.id_acara = a.id_acara 
                       WHERE p.id_pemesanan = ?";
        $price_stmt = $pdo->prepare($price_query);
        $price_stmt->execute([$id_pemesanan]);
        $price_data = $price_stmt->fetch(PDO::FETCH_ASSOC);
        
        $new_total = $price_data['harga'] * $durasi;
        
        // Update pemesanan
        $update_query = "UPDATE pemesanan SET 
                        tanggal_sewa = ?, 
                        tanggal_selesai = ?, 
                        durasi = ?, 
                        total = ?, 
                        kebutuhan_tambahan = ? 
                        WHERE id_pemesanan = ?";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->execute([$tanggal_sewa, $tanggal_selesai, $durasi, $new_total, $kebutuhan_tambahan, $id_pemesanan]);
        
        $success_message = "Data pemesanan berhasil diupdate!";
        
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Get booking data
$query = "SELECT 
            p.*,
            CASE 
                WHEN py.tipe_penyewa = 'instansi' THEN py.nama_instansi
                ELSE py.nama_lengkap
            END as nama_penyewa,
            py.email,
            a.nama_acara,
            a.lokasi
          FROM pemesanan p
          LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa
          LEFT JOIN acara a ON p.id_acara = a.id_acara
          WHERE p.id_pemesanan = ?";

$stmt = $pdo->prepare($query);
$stmt->execute([$id_pemesanan]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header("Location: data_pemesanan.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pemesanan #<?= $booking['id_pemesanan'] ?> - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --gray: #64748b;
            --gray-light: #f1f5f9;
            --white: #ffffff;
            --border: #e2e8f0;
            --shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            --radius: 12px;
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
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 30px;
            margin-bottom: 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .page-subtitle {
            color: var(--gray);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark);
            font-weight: 600;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 14px;
            background: var(--white);
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-textarea {
            min-height: 100px;
            resize: vertical;
            font-family: inherit;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            background: var(--gray-light);
            padding: 15px;
            border-radius: var(--radius);
        }

        .info-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--gray);
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 14px;
            font-weight: 500;
            color: var(--dark);
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
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--gray);
            color: var(--white);
        }

        .btn-secondary:hover {
            background: #475569;
            transform: translateY(-1px);
        }

        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #059669;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #dc2626;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .card {
                padding: 20px;
            }
            
            .btn-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="page-header">
                <h1 class="page-title">Edit Pemesanan #<?= $booking['id_pemesanan'] ?></h1>
                <p class="page-subtitle">Ubah data pemesanan dan kebutuhan tambahan</p>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= $success_message ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $error_message ?>
                </div>
            <?php endif; ?>

            <!-- Info Pemesanan -->
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nama Penyewa</div>
                    <div class="info-value"><?= htmlspecialchars($booking['nama_penyewa']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= htmlspecialchars($booking['email']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Acara</div>
                    <div class="info-value"><?= htmlspecialchars($booking['nama_acara']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Lokasi</div>
                    <div class="info-value"><?= htmlspecialchars($booking['lokasi']) ?></div>
                </div>
            </div>

            <!-- Form Edit -->
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-input" name="tanggal_sewa" value="<?= $booking['tanggal_sewa'] ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" class="form-input" name="tanggal_selesai" value="<?= $booking['tanggal_selesai'] ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Kebutuhan Tambahan</label>
                    <textarea class="form-input form-textarea" name="kebutuhan_tambahan" placeholder="Tuliskan kebutuhan khusus untuk acara ini..."><?= htmlspecialchars($booking['kebutuhan_tambahan']) ?></textarea>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update Pemesanan
                    </button>
                    <a href="data_pemesanan.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
