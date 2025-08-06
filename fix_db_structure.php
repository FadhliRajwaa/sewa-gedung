<?php
require_once 'config.php';

echo "<h2>Database Structure Check & Fix</h2>";

try {
    // Check if tanggal_pesan column exists
    $query = "SHOW COLUMNS FROM pemesanan LIKE 'tanggal_pesan'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $column_exists = $stmt->fetch();

    if (!$column_exists) {
        echo "<p>❌ Column 'tanggal_pesan' not found. Adding it...</p>";
        
        // Add tanggal_pesan column
        $alter_query = "ALTER TABLE pemesanan ADD COLUMN tanggal_pesan DATETIME DEFAULT CURRENT_TIMESTAMP";
        $pdo->exec($alter_query);
        echo "<p>✅ Column 'tanggal_pesan' added successfully!</p>";
        
        // Update existing records
        $update_query = "UPDATE pemesanan SET tanggal_pesan = tanggal_sewa WHERE tanggal_pesan IS NULL";
        $pdo->exec($update_query);
        echo "<p>✅ Existing records updated with tanggal_pesan!</p>";
    } else {
        echo "<p>✅ Column 'tanggal_pesan' already exists!</p>";
    }

    // Check current pemesanan table structure
    echo "<h3>Current Pemesanan Table Structure:</h3>";
    $query = "DESCRIBE pemesanan";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";

    // Insert some sample data if table is empty
    $query = "SELECT COUNT(*) as count FROM pemesanan";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    if ($count == 0) {
        echo "<h3>Inserting Sample Data:</h3>";
        
        // Make sure we have penyewa and acara data first
        $check_penyewa = "SELECT COUNT(*) as count FROM penyewa";
        $stmt = $pdo->prepare($check_penyewa);
        $stmt->execute();
        $penyewa_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $check_acara = "SELECT COUNT(*) as count FROM acara";
        $stmt = $pdo->prepare($check_acara);
        $stmt->execute();
        $acara_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($penyewa_count > 0 && $acara_count > 0) {
            // Get first penyewa and acara IDs
            $get_penyewa = "SELECT id_penyewa FROM penyewa LIMIT 1";
            $stmt = $pdo->prepare($get_penyewa);
            $stmt->execute();
            $penyewa_id = $stmt->fetch(PDO::FETCH_ASSOC)['id_penyewa'];
            
            $get_acara = "SELECT id_acara FROM acara LIMIT 1";
            $stmt = $pdo->prepare($get_acara);
            $stmt->execute();
            $acara_id = $stmt->fetch(PDO::FETCH_ASSOC)['id_acara'];
            
            $sample_bookings = [
                [
                    'id_penyewa' => $penyewa_id,
                    'id_acara' => $acara_id,
                    'tanggal_sewa' => '2025-08-10',
                    'tanggal_selesai' => '2025-08-10',
                    'total' => 2500000,
                    'status' => 'confirmed',
                    'tanggal_pesan' => '2025-08-05 10:00:00'
                ],
                [
                    'id_penyewa' => $penyewa_id,
                    'id_acara' => $acara_id,
                    'tanggal_sewa' => '2025-08-15',
                    'tanggal_selesai' => '2025-08-16',
                    'total' => 5000000,
                    'status' => 'pending',
                    'tanggal_pesan' => '2025-08-05 14:30:00'
                ],
                [
                    'id_penyewa' => $penyewa_id,
                    'id_acara' => $acara_id,
                    'tanggal_sewa' => '2025-07-20',
                    'tanggal_selesai' => '2025-07-20',
                    'total' => 8000000,
                    'status' => 'completed',
                    'tanggal_pesan' => '2025-07-15 09:15:00'
                ]
            ];
            
            foreach ($sample_bookings as $booking) {
                $insert_query = "INSERT INTO pemesanan (id_penyewa, id_acara, tanggal_sewa, tanggal_selesai, total, status, tanggal_pesan) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($insert_query);
                $stmt->execute([
                    $booking['id_penyewa'],
                    $booking['id_acara'],
                    $booking['tanggal_sewa'],
                    $booking['tanggal_selesai'],
                    $booking['total'],
                    $booking['status'],
                    $booking['tanggal_pesan']
                ]);
                echo "<p>✅ Sample booking inserted: " . $booking['tanggal_sewa'] . " - " . $booking['status'] . "</p>";
            }
        } else {
            echo "<p>❌ Need penyewa and acara data first before inserting bookings</p>";
            echo "<p>Penyewa count: $penyewa_count, Acara count: $acara_count</p>";
        }
    } else {
        echo "<p>✅ Pemesanan table has $count records</p>";
    }

    // Test dashboard queries
    echo "<h3>Testing Dashboard Queries:</h3>";
    
    $queries = [
        'Jumlah Penyewa' => "SELECT COUNT(*) as count FROM penyewa",
        'Pendapatan Bulan Ini' => "SELECT SUM(total) as total FROM pemesanan WHERE MONTH(tanggal_pesan) = MONTH(CURRENT_DATE()) AND YEAR(tanggal_pesan) = YEAR(CURRENT_DATE())",
        'Pesanan Bulan Ini' => "SELECT COUNT(*) as count FROM pemesanan WHERE MONTH(tanggal_pesan) = MONTH(CURRENT_DATE()) AND YEAR(tanggal_pesan) = YEAR(CURRENT_DATE())",
        'Total Pendapatan' => "SELECT SUM(total) as total FROM pemesanan"
    ];
    
    foreach ($queries as $name => $query) {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $value = $result['count'] ?? $result['total'] ?? 0;
        echo "<p><strong>$name:</strong> " . ($name == 'Pendapatan Bulan Ini' || $name == 'Total Pendapatan' ? 'Rp ' . number_format($value, 0, ',', '.') : $value) . "</p>";
    }

} catch (PDOException $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<br><br><a href='admin/dashboard.php' style='background: #8B4513; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Dashboard</a>";
?>
