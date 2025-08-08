<?php
/**
 * Manual ID Reorder Tool
 * Gunakan file ini untuk mengatur ulang semua ID secara manual
 */

require_once 'config.php';
require_once 'simple_reorder.php';

// Check if action is requested
$action = $_GET['action'] ?? '';
$result = '';

if ($action) {
    switch ($action) {
        case 'penyewa':
            if (simpleReorderPenyewaIds()) {
                $result = "✅ ID Penyewa berhasil diurutkan ulang!";
            } else {
                $result = "❌ Gagal mengatur ulang ID Penyewa!";
            }
            break;
            
        case 'pemesanan':
            if (simpleReorderPemesananIds()) {
                $result = "✅ ID Pemesanan berhasil diurutkan ulang!";
            } else {
                $result = "❌ Gagal mengatur ulang ID Pemesanan!";
            }
            break;
            
        case 'pembayaran':
            if (simpleReorderPembayaranIds()) {
                $result = "✅ ID Pembayaran berhasil diurutkan ulang!";
            } else {
                $result = "❌ Gagal mengatur ulang ID Pembayaran!";
            }
            break;
            
        case 'all':
            $results = [
                'penyewa' => simpleReorderPenyewaIds(),
                'pemesanan' => simpleReorderPemesananIds(), 
                'pembayaran' => simpleReorderPembayaranIds()
            ];
            $success = true;
            $details = [];
            foreach ($results as $table => $tableResult) {
                if ($tableResult) {
                    $details[] = "✅ $table berhasil";
                } else {
                    $details[] = "❌ $table gagal";
                    $success = false;
                }
            }
            $result = $success ? "✅ Semua tabel berhasil diurutkan ulang!" : "⚠️ Beberapa tabel gagal diurutkan ulang!";
            $result .= "<br>" . implode("<br>", $details);
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual ID Reorder Tool</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #1e7e34;
        }
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background: #e0a800;
        }
        .result {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .info-box {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            color: #0d47a1;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button-group {
            text-align: center;
            margin: 30px 0;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            text-align: center;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Manual ID Reorder Tool</h1>
        
        <div class="info-box">
            <strong>📋 Cara Kerja:</strong><br>
            Tool ini akan mengatur ulang semua ID agar berurutan tanpa gap (1, 2, 3, dst.). 
            Jika Anda memiliki ID 1, 3, 5 maka akan diubah menjadi 1, 2, 3.
        </div>

        <?php if ($result): ?>
        <div class="result">
            <?php echo $result; ?>
        </div>
        <?php endif; ?>

        <h3>📊 Statistik Database Saat Ini:</h3>
        <div class="stats">
            <?php
            $tables = [
                'penyewa' => 'Penyewa',
                'pemesanan' => 'Pemesanan', 
                'pembayaran' => 'Pembayaran'
            ];
            
            foreach ($tables as $table => $label) {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                $count = $stmt->fetchColumn();
                
                $stmt = $pdo->query("SHOW TABLE STATUS LIKE '$table'");
                $info = $stmt->fetch(PDO::FETCH_ASSOC);
                $autoIncrement = $info['Auto_increment'] ?? 1;
                
                echo "<div class='stat-card'>";
                echo "<div class='stat-number'>$count</div>";
                echo "<div class='stat-label'>$label</div>";
                echo "<small>AUTO_INCREMENT: $autoIncrement</small>";
                echo "</div>";
            }
            ?>
        </div>

        <h3>🛠️ Pilih Aksi:</h3>
        <div class="button-group">
            <a href="?action=penyewa" class="btn">Urutkan ID Penyewa</a>
            <a href="?action=pemesanan" class="btn">Urutkan ID Pemesanan</a>
            <a href="?action=pembayaran" class="btn">Urutkan ID Pembayaran</a>
            <a href="?action=all" class="btn btn-success">Urutkan Semua ID</a>
        </div>

        <div class="info-box">
            <strong>⚠️ Penting:</strong><br>
            - Proses ini akan mengubah ID yang sudah ada<br>
            - Backup database sebelum menjalankan jika diperlukan<br>
            - Proses akan otomatis berjalan saat menghapus data melalui admin panel
        </div>

        <h3>📝 Contoh Sebelum dan Sesudah:</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <h4>❌ Sebelum (Ada Gap):</h4>
                <ul>
                    <li>ID: 1, 3, 5, 8</li>
                    <li>AUTO_INCREMENT: 9</li>
                    <li>Data baru akan mendapat ID: 9</li>
                </ul>
            </div>
            <div>
                <h4>✅ Sesudah (Berurutan):</h4>
                <ul>
                    <li>ID: 1, 2, 3, 4</li>
                    <li>AUTO_INCREMENT: 5</li>
                    <li>Data baru akan mendapat ID: 5</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
