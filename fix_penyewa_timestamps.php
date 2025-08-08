<?php
/**
 * Script untuk memperbaiki masalah kolom created_at dan updated_at di tabel penyewa
 * Jalankan script ini untuk menambahkan kolom yang hilang dan memperbaiki query yang error
 */

require_once 'config.php';

function addTimestampColumns() {
    global $conn;
    
    echo "<h2>Memperbaiki Struktur Tabel Penyewa</h2>";
    
    // Check if created_at column exists
    $result = mysqli_query($conn, "SHOW COLUMNS FROM penyewa LIKE 'created_at'");
    if (mysqli_num_rows($result) == 0) {
        echo "<p>Menambahkan kolom created_at...</p>";
        $sql = "ALTER TABLE `penyewa` ADD COLUMN `created_at` timestamp NOT NULL DEFAULT current_timestamp() AFTER `email_terverifikasi`";
        if (mysqli_query($conn, $sql)) {
            echo "<p>✅ Kolom created_at berhasil ditambahkan</p>";
        } else {
            echo "<p>❌ Error menambahkan created_at: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p>✅ Kolom created_at sudah ada</p>";
    }
    
    // Check if updated_at column exists
    $result = mysqli_query($conn, "SHOW COLUMNS FROM penyewa LIKE 'updated_at'");
    if (mysqli_num_rows($result) == 0) {
        echo "<p>Menambahkan kolom updated_at...</p>";
        $sql = "ALTER TABLE `penyewa` ADD COLUMN `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() AFTER `created_at`";
        if (mysqli_query($conn, $sql)) {
            echo "<p>✅ Kolom updated_at berhasil ditambahkan</p>";
        } else {
            echo "<p>❌ Error menambahkan updated_at: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p>✅ Kolom updated_at sudah ada</p>";
    }
    
    // Update existing records with current timestamp for created_at if NULL
    echo "<p>Memperbarui data existing...</p>";
    $sql = "UPDATE `penyewa` SET `created_at` = CURRENT_TIMESTAMP WHERE `created_at` IS NULL OR `created_at` = '0000-00-00 00:00:00'";
    if (mysqli_query($conn, $sql)) {
        $affected = mysqli_affected_rows($conn);
        echo "<p>✅ {$affected} record diperbarui dengan timestamp created_at</p>";
    } else {
        echo "<p>❌ Error updating existing records: " . mysqli_error($conn) . "</p>";
    }
}

function testQueries() {
    global $conn;
    
    echo "<h2>Testing Database Queries</h2>";
    
    // Test basic SELECT query
    $sql = "SELECT id_penyewa, tipe_penyewa, nama_lengkap, email, created_at, updated_at FROM penyewa LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "<p>✅ SELECT query with timestamps berhasil</p>";
        if ($row = mysqli_fetch_assoc($result)) {
            echo "<pre>Sample data:\n";
            print_r($row);
            echo "</pre>";
        }
    } else {
        echo "<p>❌ SELECT query failed: " . mysqli_error($conn) . "</p>";
    }
    
    // Test INSERT query
    $test_data = [
        'test_user_' . time(),
        'test@example.com',
        'Test User',
        'individu',
        'password123',
        '08123456789',
        'Test Address'
    ];
    
    $sql = "INSERT INTO penyewa (username, email, nama_lengkap, tipe_penyewa, password, no_telepon, alamat, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssss", ...$test_data);
        if (mysqli_stmt_execute($stmt)) {
            $test_id = mysqli_insert_id($conn);
            echo "<p>✅ INSERT query berhasil (ID: {$test_id})</p>";
            
            // Test UPDATE query
            $sql_update = "UPDATE penyewa SET nama_lengkap = 'Updated Test User', updated_at = NOW() WHERE id_penyewa = ?";
            $stmt_update = mysqli_prepare($conn, $sql_update);
            if ($stmt_update) {
                mysqli_stmt_bind_param($stmt_update, "i", $test_id);
                if (mysqli_stmt_execute($stmt_update)) {
                    echo "<p>✅ UPDATE query berhasil</p>";
                } else {
                    echo "<p>❌ UPDATE query failed: " . mysqli_error($conn) . "</p>";
                }
                mysqli_stmt_close($stmt_update);
            }
            
            // Clean up test data
            $sql_delete = "DELETE FROM penyewa WHERE id_penyewa = ?";
            $stmt_delete = mysqli_prepare($conn, $sql_delete);
            if ($stmt_delete) {
                mysqli_stmt_bind_param($stmt_delete, "i", $test_id);
                mysqli_stmt_execute($stmt_delete);
                echo "<p>✅ Test data cleaned up</p>";
                mysqli_stmt_close($stmt_delete);
            }
        } else {
            echo "<p>❌ INSERT query failed: " . mysqli_error($conn) . "</p>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<p>❌ Prepare INSERT statement failed: " . mysqli_error($conn) . "</p>";
    }
}

function showTableStructure() {
    global $conn;
    
    echo "<h2>Struktur Tabel Penyewa</h2>";
    $result = mysqli_query($conn, "DESCRIBE penyewa");
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "<td>{$row['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ Error getting table structure: " . mysqli_error($conn) . "</p>";
    }
}

// Main execution
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'fix_columns':
            addTimestampColumns();
            break;
        case 'test_queries':
            testQueries();
            break;
        case 'show_structure':
            showTableStructure();
            break;
        case 'all':
            addTimestampColumns();
            echo "<hr>";
            testQueries();
            echo "<hr>";
            showTableStructure();
            break;
    }
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Fix Penyewa Table Timestamps</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            button { padding: 10px 20px; margin: 5px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
            button:hover { background: #0056b3; }
            .container { max-width: 800px; margin: 0 auto; }
            pre { background: #f8f9fa; padding: 10px; border-radius: 5px; }
            table { width: 100%; border-collapse: collapse; margin: 10px 0; }
            th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
            th { background-color: #f2f2f2; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Fix Penyewa Table Timestamps</h1>
            <p>Script ini akan memperbaiki masalah kolom created_at dan updated_at di tabel penyewa.</p>
            
            <form method="POST" style="margin: 20px 0;">
                <button type="submit" name="action" value="fix_columns">Perbaiki Kolom Timestamp</button>
                <button type="submit" name="action" value="test_queries">Test Database Queries</button>
                <button type="submit" name="action" value="show_structure">Tampilkan Struktur Tabel</button>
                <button type="submit" name="action" value="all">Jalankan Semua</button>
            </form>
        </div>
    </body>
    </html>
    <?php
}
?>
