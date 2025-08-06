<?php
require_once 'config.php';

echo "<h2>Populate Sample Data for Dashboard</h2>";

try {
    // First, let's check what we have
    echo "<h3>Current Data Status:</h3>";
    
    $tables_check = [
        'penyewa' => "SELECT COUNT(*) as count FROM penyewa",
        'acara' => "SELECT COUNT(*) as count FROM acara", 
        'pemesanan' => "SELECT COUNT(*) as count FROM pemesanan"
    ];
    
    foreach ($tables_check as $table => $query) {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>$table: $count records</p>";
    }
    
    // Add sample penyewa if needed
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM penyewa");
    $stmt->execute();
    $penyewa_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($penyewa_count < 3) {
        echo "<h3>Adding Sample Penyewa:</h3>";
        
        $sample_penyewa = [
            ['nama' => 'Ahmad Wijaya', 'email' => 'ahmad@email.com', 'telepon' => '08123456789', 'alamat' => 'Jl. Merdeka No. 123', 'jenis_penyewa' => 'umum'],
            ['nama' => 'Siti Nurhaliza', 'email' => 'siti@email.com', 'telepon' => '08198765432', 'alamat' => 'Jl. Sudirman No. 456', 'jenis_penyewa' => 'umum'],
            ['nama' => 'PT. Maju Jaya', 'email' => 'admin@majujaya.com', 'telepon' => '02134567890', 'alamat' => 'Jl. Thamrin No. 789', 'jenis_penyewa' => 'instansi'],
            ['nama' => 'CV. Berkah Sejahtera', 'email' => 'info@berkah.com', 'telepon' => '02198765432', 'alamat' => 'Jl. Gatot Subroto No. 321', 'jenis_penyewa' => 'instansi']
        ];
        
        foreach ($sample_penyewa as $penyewa) {
            // Check if email already exists
            $check_stmt = $pdo->prepare("SELECT id_penyewa FROM penyewa WHERE email = ?");
            $check_stmt->execute([$penyewa['email']]);
            
            if (!$check_stmt->fetch()) {
                $password = password_hash('password123', PASSWORD_DEFAULT);
                $insert_stmt = $pdo->prepare("INSERT INTO penyewa (nama, email, password, telepon, alamat, jenis_penyewa) VALUES (?, ?, ?, ?, ?, ?)");
                $insert_stmt->execute([
                    $penyewa['nama'],
                    $penyewa['email'], 
                    $password,
                    $penyewa['telepon'],
                    $penyewa['alamat'],
                    $penyewa['jenis_penyewa']
                ]);
                echo "<p>✅ Added: " . $penyewa['nama'] . "</p>";
            }
        }
    }
    
    // Add sample pemesanan with current month data
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM pemesanan WHERE MONTH(tanggal_pesan) = MONTH(CURRENT_DATE()) AND YEAR(tanggal_pesan) = YEAR(CURRENT_DATE())");
    $stmt->execute();
    $current_month_bookings = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($current_month_bookings < 5) {
        echo "<h3>Adding Sample Pemesanan for Current Month:</h3>";
        
        // Get some penyewa and acara IDs
        $penyewa_stmt = $pdo->prepare("SELECT id_penyewa FROM penyewa LIMIT 3");
        $penyewa_stmt->execute();
        $penyewa_ids = $penyewa_stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $acara_stmt = $pdo->prepare("SELECT id_acara FROM acara LIMIT 3");
        $acara_stmt->execute();
        $acara_ids = $acara_stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($penyewa_ids) && !empty($acara_ids)) {
            $sample_bookings = [
                // Current month bookings (August 2025)
                [
                    'id_penyewa' => $penyewa_ids[0],
                    'id_acara' => $acara_ids[0],
                    'tanggal_sewa' => '2025-08-10',
                    'tanggal_selesai' => '2025-08-10',
                    'total' => 2500000,
                    'status' => 'confirmed',
                    'tanggal_pesan' => '2025-08-01 09:00:00'
                ],
                [
                    'id_penyewa' => $penyewa_ids[1],
                    'id_acara' => $acara_ids[1],
                    'tanggal_sewa' => '2025-08-15',
                    'tanggal_selesai' => '2025-08-16',
                    'total' => 8000000,
                    'status' => 'pending',
                    'tanggal_pesan' => '2025-08-03 14:30:00'
                ],
                [
                    'id_penyewa' => $penyewa_ids[2],
                    'id_acara' => $acara_ids[2],
                    'tanggal_sewa' => '2025-08-20',
                    'tanggal_selesai' => '2025-08-20',
                    'total' => 4000000,
                    'status' => 'confirmed',
                    'tanggal_pesan' => '2025-08-04 10:15:00'
                ],
                [
                    'id_penyewa' => $penyewa_ids[0],
                    'id_acara' => $acara_ids[1],
                    'tanggal_sewa' => '2025-08-25',
                    'tanggal_selesai' => '2025-08-25',
                    'total' => 5000000,
                    'status' => 'pending',
                    'tanggal_pesan' => '2025-08-05 16:45:00'
                ],
                // Previous months for chart data
                [
                    'id_penyewa' => $penyewa_ids[1],
                    'id_acara' => $acara_ids[0],
                    'tanggal_sewa' => '2025-07-15',
                    'tanggal_selesai' => '2025-07-15',
                    'total' => 3000000,
                    'status' => 'completed',
                    'tanggal_pesan' => '2025-07-10 11:00:00'
                ],
                [
                    'id_penyewa' => $penyewa_ids[2],
                    'id_acara' => $acara_ids[2],
                    'tanggal_sewa' => '2025-06-20',
                    'tanggal_selesai' => '2025-06-20',
                    'total' => 6000000,
                    'status' => 'completed',
                    'tanggal_pesan' => '2025-06-15 13:20:00'
                ]
            ];
            
            foreach ($sample_bookings as $booking) {
                $insert_stmt = $pdo->prepare("INSERT INTO pemesanan (id_penyewa, id_acara, tanggal_sewa, tanggal_selesai, total, status, tanggal_pesan) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $insert_stmt->execute([
                    $booking['id_penyewa'],
                    $booking['id_acara'],
                    $booking['tanggal_sewa'],
                    $booking['tanggal_selesai'],
                    $booking['total'],
                    $booking['status'],
                    $booking['tanggal_pesan']
                ]);
                echo "<p>✅ Added booking: " . $booking['tanggal_pesan'] . " - Rp " . number_format($booking['total'], 0, ',', '.') . "</p>";
            }
        } else {
            echo "<p>❌ No penyewa or acara data available to create bookings</p>";
        }
    }
    
    // Final statistics
    echo "<h3>Final Dashboard Statistics:</h3>";
    
    // Total penyewa
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM penyewa");
    $stmt->execute();
    $total_penyewa = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Current month revenue
    $stmt = $pdo->prepare("SELECT SUM(total) as total FROM pemesanan WHERE MONTH(tanggal_pesan) = MONTH(CURRENT_DATE()) AND YEAR(tanggal_pesan) = YEAR(CURRENT_DATE())");
    $stmt->execute();
    $current_month_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Current month bookings
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM pemesanan WHERE MONTH(tanggal_pesan) = MONTH(CURRENT_DATE()) AND YEAR(tanggal_pesan) = YEAR(CURRENT_DATE())");
    $stmt->execute();
    $current_month_bookings = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total revenue
    $stmt = $pdo->prepare("SELECT SUM(total) as total FROM pemesanan");
    $stmt->execute();
    $total_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h4>Dashboard akan menampilkan:</h4>";
    echo "<p><strong>Total Penyewa:</strong> $total_penyewa</p>";
    echo "<p><strong>Pendapatan Bulan Ini:</strong> Rp " . number_format($current_month_revenue, 0, ',', '.') . "</p>";
    echo "<p><strong>Pesanan Bulan Ini:</strong> $current_month_bookings</p>";
    echo "<p><strong>Total Pendapatan:</strong> Rp " . number_format($total_revenue, 0, ',', '.') . "</p>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<br><a href='admin/dashboard.php' style='background: #8B4513; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🚀 Open Dashboard</a>";
echo "&nbsp;&nbsp;";
echo "<a href='debug_db.php' style='background: #17a2b8; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;'>🔍 Debug Database</a>";
?>
