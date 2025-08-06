<?php
// Debug database connection and data
require_once 'config.php';

echo "<h2>Database Debug Information</h2>";

try {
    // Test database connection
    echo "<h3>1. Database Connection Test</h3>";
    if ($pdo) {
        echo "✅ PDO Connection: SUCCESS<br>";
        echo "Database: " . $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "<br><br>";
    } else {
        echo "❌ PDO Connection: FAILED<br><br>";
    }
    
    // Check tables existence
    echo "<h3>2. Tables Check</h3>";
    $tables = ['penyewa', 'acara', 'pemesanan'];
    foreach ($tables as $table) {
        $query = "SHOW TABLES LIKE '$table'";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        if ($stmt->fetch()) {
            echo "✅ Table '$table': EXISTS<br>";
        } else {
            echo "❌ Table '$table': NOT FOUND<br>";
        }
    }
    echo "<br>";
    
    // Check data in each table
    echo "<h3>3. Data Count in Tables</h3>";
    foreach ($tables as $table) {
        try {
            $query = "SELECT COUNT(*) as count FROM $table";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "Table '$table': " . $result['count'] . " records<br>";
        } catch (PDOException $e) {
            echo "❌ Error checking table '$table': " . $e->getMessage() . "<br>";
        }
    }
    echo "<br>";
    
    // Check specific queries used in dashboard
    echo "<h3>4. Dashboard Queries Test</h3>";
    
    // Jumlah penyewa
    $query = "SELECT COUNT(*) as jumlah_penyewa FROM penyewa";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $jumlah_penyewa = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Jumlah Penyewa: " . ($jumlah_penyewa['jumlah_penyewa'] ?? 0) . "<br>";
    
    // Pendapatan bulanan
    $query = "SELECT SUM(total) as total_pendapatan FROM pemesanan WHERE MONTH(tanggal_pesan) = MONTH(CURRENT_DATE()) AND YEAR(tanggal_pesan) = YEAR(CURRENT_DATE())";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $pendapatan = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Pendapatan Bulan Ini: Rp " . number_format($pendapatan['total_pendapatan'] ?? 0, 0, ',', '.') . "<br>";
    
    // Jumlah pesanan
    $query = "SELECT COUNT(*) as jumlah_pesanan FROM pemesanan WHERE MONTH(tanggal_pesan) = MONTH(CURRENT_DATE()) AND YEAR(tanggal_pesan) = YEAR(CURRENT_DATE())";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $pesanan = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Jumlah Pesanan Bulan Ini: " . ($pesanan['jumlah_pesanan'] ?? 0) . "<br><br>";
    
    // Check pemesanan table structure
    echo "<h3>5. Pemesanan Table Structure</h3>";
    $query = "DESCRIBE pemesanan";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "Column: " . $column['Field'] . " (" . $column['Type'] . ")<br>";
    }
    echo "<br>";
    
    // Sample data from pemesanan
    echo "<h3>6. Sample Pemesanan Data</h3>";
    $query = "SELECT * FROM pemesanan LIMIT 5";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $sample_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($sample_data)) {
        echo "❌ No data found in pemesanan table<br>";
        echo "<strong>Creating sample data...</strong><br>";
        
        // Insert sample data
        $sample_pemesanan = [
            [
                'id_penyewa' => 1,
                'id_acara' => 1,
                'tanggal_sewa' => '2025-08-10',
                'tanggal_selesai' => '2025-08-10',
                'total' => 2500000,
                'status' => 'confirmed',
                'tanggal_pesan' => '2025-08-05'
            ],
            [
                'id_penyewa' => 2,
                'id_acara' => 2,
                'tanggal_sewa' => '2025-08-15',
                'tanggal_selesai' => '2025-08-15',
                'total' => 8000000,
                'status' => 'pending',
                'tanggal_pesan' => '2025-08-05'
            ]
        ];
        
        foreach ($sample_pemesanan as $data) {
            try {
                $insert_query = "INSERT INTO pemesanan (id_penyewa, id_acara, tanggal_sewa, tanggal_selesai, total, status, tanggal_pesan) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $insert_stmt = $pdo->prepare($insert_query);
                $insert_stmt->execute([
                    $data['id_penyewa'],
                    $data['id_acara'],
                    $data['tanggal_sewa'],
                    $data['tanggal_selesai'],
                    $data['total'],
                    $data['status'],
                    $data['tanggal_pesan']
                ]);
                echo "✅ Sample data inserted<br>";
            } catch (PDOException $e) {
                echo "❌ Error inserting sample data: " . $e->getMessage() . "<br>";
            }
        }
    } else {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr>";
        foreach (array_keys($sample_data[0]) as $key) {
            echo "<th>$key</th>";
        }
        echo "</tr>";
        
        foreach ($sample_data as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "<br>";
    
    // Check penyewa data
    echo "<h3>7. Sample Penyewa Data</h3>";
    $query = "SELECT * FROM penyewa LIMIT 3";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $penyewa_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($penyewa_data)) {
        echo "❌ No data found in penyewa table<br>";
        echo "<strong>Creating sample penyewa data...</strong><br>";
        
        $sample_penyewa = [
            [
                'nama' => 'John Doe',
                'email' => 'john@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'telepon' => '08123456789',
                'alamat' => 'Jl. Contoh No. 123',
                'jenis_penyewa' => 'umum'
            ],
            [
                'nama' => 'PT. Contoh Sejahtera',
                'email' => 'admin@contoh.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'telepon' => '08198765432',
                'alamat' => 'Jl. Perusahaan No. 456',
                'jenis_penyewa' => 'instansi'
            ]
        ];
        
        foreach ($sample_penyewa as $data) {
            try {
                $insert_query = "INSERT INTO penyewa (nama, email, password, telepon, alamat, jenis_penyewa) VALUES (?, ?, ?, ?, ?, ?)";
                $insert_stmt = $pdo->prepare($insert_query);
                $insert_stmt->execute([
                    $data['nama'],
                    $data['email'],
                    $data['password'],
                    $data['telepon'],
                    $data['alamat'],
                    $data['jenis_penyewa']
                ]);
                echo "✅ Sample penyewa inserted: " . $data['nama'] . "<br>";
            } catch (PDOException $e) {
                echo "❌ Error inserting penyewa: " . $e->getMessage() . "<br>";
            }
        }
    } else {
        foreach ($penyewa_data as $penyewa) {
            echo "ID: " . $penyewa['id_penyewa'] . " - " . $penyewa['nama'] . " (" . $penyewa['email'] . ")<br>";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage();
}

echo "<br><br><a href='admin/dashboard.php'>Go to Admin Dashboard</a>";
?>
