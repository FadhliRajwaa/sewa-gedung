<?php
session_start();
require_once 'config.php';

// Get booking ID from URL
$id_pemesanan = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_pemesanan) {
    die('ID pemesanan tidak valid');
}

// Query booking data
$query = "SELECT 
            p.id_pemesanan,
            p.tanggal_sewa,
            p.tanggal_selesai,
            p.durasi,
            p.total,
            p.metode_pembayaran,
            p.tanggal_pesan,
            p.kebutuhan_tambahan,
            CASE 
                WHEN py.tipe_penyewa = 'instansi' THEN py.nama_instansi
                ELSE py.nama_lengkap
            END as nama_penyewa,
            py.email as email_penyewa,
            py.tipe_penyewa,
            py.no_telepon,
            a.nama_acara,
            a.lokasi,
            a.harga as harga_acara,
            pb.status_pembayaran,
            pb.tanggal_upload
          FROM pemesanan p
          LEFT JOIN penyewa py ON p.id_penyewa = py.id_penyewa
          LEFT JOIN acara a ON p.id_acara = a.id_acara
          LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
          WHERE p.id_pemesanan = ?";

$stmt = $pdo->prepare($query);
$stmt->execute([$id_pemesanan]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    die('Data pemesanan tidak ditemukan');
}

$status_pembayaran = $booking['status_pembayaran'] ?: 'Belum Lunas';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pembayaran #<?= $booking['id_pemesanan'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .nota-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
        }

        .nota-number {
            font-size: 20px;
            font-weight: 700;
            color: #8B4513;
        }

        .nota-date {
            color: #64748b;
            font-size: 14px;
        }

        .content {
            padding: 30px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #8B4513;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 500;
            color: #1e293b;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-lunas {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .total-section {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #8B4513;
        }

        .total-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .total-item:last-child {
            margin-bottom: 0;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-weight: 700;
            font-size: 18px;
        }

        .footer {
            background: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-lunas {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #8B4513;
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(139, 69, 19, 0.3);
            transition: all 0.3s ease;
        }

        .print-btn:hover {
            background: #A0522D;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 69, 19, 0.4);
        }

        @media print {
            @page {
                size: A4;
                margin: 0.2in;
            }
            
            body {
                background: white;
                font-size: 10px;
                line-height: 1.2;
                color: #000;
            }
            
            .container {
                box-shadow: none;
                margin: 0;
                max-width: none;
                border-radius: 0;
                page-break-inside: avoid;
                height: auto;
                transform: scale(0.85);
                transform-origin: top left;
                width: 118%; /* Compensate for scale */
            }
            
            .header {
                padding: 8px 15px;
                page-break-inside: avoid;
                background: #8B4513 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .header h1 {
                font-size: 18px;
                margin-bottom: 2px;
            }
            
            .header p {
                font-size: 11px;
            }
            
            .nota-info {
                padding: 8px 15px;
                page-break-inside: avoid;
                background: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
            }
            
            .nota-number {
                font-size: 14px;
            }
            
            .nota-date {
                font-size: 10px;
            }
            
            .content {
                padding: 12px 15px;
            }
            
            .section {
                margin-bottom: 8px;
                page-break-inside: avoid;
            }
            
            .section-title {
                font-size: 12px;
                margin-bottom: 6px;
                padding-bottom: 2px;
            }
            
            .info-grid {
                gap: 8px;
                display: grid;
                grid-template-columns: repeat(2, 1fr);
            }
            
            .info-item {
                margin-bottom: 3px;
            }
            
            .info-label {
                font-size: 8px;
                margin-bottom: 1px;
            }
            
            .info-value {
                font-size: 10px;
                line-height: 1.1;
            }
            
            .total-section {
                margin-top: 8px;
                padding: 8px;
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact;
            }
            
            .total-item {
                padding: 2px 0;
                font-size: 10px;
                margin-bottom: 3px;
            }
            
            .total-item:last-child {
                font-size: 12px;
                font-weight: bold;
                padding-top: 4px;
            }
            
            .footer {
                padding: 8px 15px;
                font-size: 9px;
                page-break-inside: avoid;
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact;
            }
            
            .status-badge {
                font-size: 9px;
                padding: 2px 6px;
            }
            
            .print-btn {
                display: none;
            }
            
            /* Reduce margins for kebutuhan tambahan */
            .section div[style*="margin-top: 20px"],
            .section div[style*="margin-top: 12px"] {
                margin-top: 4px !important;
            }
            
            /* Ensure no page breaks */
            * {
                page-break-inside: avoid;
            }
            
            .container * {
                max-height: none;
            }
            
            /* Force content to fit in one page */
            html, body {
                height: auto;
                overflow: visible;
            }
            
            .container {
                height: auto;
                min-height: auto;
                max-height: 9.5in; /* Force content to stay within page */
                overflow: hidden;
            }
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 8px;
            }
            
            .header, .content {
                padding: 20px;
            }
            
            .nota-info {
                padding: 15px 20px;
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PT. Aneka Usaha</h1>
            <p>Nota Pembayaran Sewa Gedung</p>
        </div>

        <div class="nota-info">
            <div class="nota-number">Nota #<?= str_pad($booking['id_pemesanan'], 6, '0', STR_PAD_LEFT) ?></div>
            <div class="nota-date">Tanggal: <?= date('d M Y', strtotime($booking['tanggal_pesan'])) ?></div>
        </div>

        <div class="content">
            <!-- Customer Information -->
            <div class="section">
                <h3 class="section-title">Informasi Penyewa</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nama Penyewa</div>
                        <div class="info-value"><?= htmlspecialchars($booking['nama_penyewa']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tipe Penyewa</div>
                        <div class="info-value"><?= ucfirst($booking['tipe_penyewa']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($booking['email_penyewa']) ?></div>
                    </div>
                    <?php if ($booking['no_telepon']): ?>
                    <div class="info-item">
                        <div class="info-label">No. Telepon</div>
                        <div class="info-value"><?= htmlspecialchars($booking['no_telepon']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Event Information -->
            <div class="section">
                <h3 class="section-title">Detail Acara</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nama Acara</div>
                        <div class="info-value"><?= htmlspecialchars($booking['nama_acara']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Lokasi</div>
                        <div class="info-value"><?= htmlspecialchars($booking['lokasi']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tanggal Mulai</div>
                        <div class="info-value"><?= date('d M Y', strtotime($booking['tanggal_sewa'])) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tanggal Selesai</div>
                        <div class="info-value"><?= date('d M Y', strtotime($booking['tanggal_selesai'])) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Durasi</div>
                        <div class="info-value"><?= $booking['durasi'] ?> hari</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Metode Pembayaran</div>
                        <div class="info-value"><?= htmlspecialchars($booking['metode_pembayaran']) ?></div>
                    </div>
                </div>
                
                <?php if ($booking['kebutuhan_tambahan']): ?>
                <div style="margin-top: 12px;">
                    <div class="info-label">Kebutuhan Tambahan</div>
                    <div class="info-value"><?= nl2br(htmlspecialchars($booking['kebutuhan_tambahan'])) ?></div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Payment Summary -->
            <div class="section">
                <h3 class="section-title">Rincian Pembayaran</h3>
                <div class="total-section">
                    <div class="total-item">
                        <span>Harga per hari</span>
                        <span>Rp <?= number_format($booking['harga_acara'], 0, ',', '.') ?></span>
                    </div>
                    <div class="total-item">
                        <span>Durasi (<?= $booking['durasi'] ?> hari)</span>
                        <span>Rp <?= number_format($booking['harga_acara'] * $booking['durasi'], 0, ',', '.') ?></span>
                    </div>
                    <div class="total-item">
                        <span>Total Pembayaran</span>
                        <span>Rp <?= number_format($booking['total'], 0, ',', '.') ?></span>
                    </div>
                </div>
                
                <div style="margin-top: 12px;">
                    <div class="info-item">
                        <div class="info-label">Status Pembayaran</div>
                        <div class="info-value">
                            <span class="status-badge <?= $status_pembayaran == 'Lunas' ? 'status-lunas' : 'status-pending' ?>">
                                <?= $status_pembayaran ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Terima kasih telah mempercayai PT. Aneka Usaha untuk kebutuhan sewa gedung Anda.</p>
            <p>Nota ini merupakan bukti sah transaksi pembayaran.</p>
        </div>
    </div>

    <button class="print-btn" onclick="window.print()">
        <i class="fas fa-print"></i> Cetak Nota
    </button>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
