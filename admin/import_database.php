<?php
// Import database from SQL file
echo "<h1>Database Import Tool</h1>";

// Database configuration
$host = 'localhost';
$dbname = 'sewa_gedung';
$user = 'root';
$pass = '';

try {
    // Connect to MySQL server (without specifying database first)
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✓ Connected to MySQL server</p>";
    
    // Read SQL file
    $sql_file = '../gedung_pt_aneka_complete.sql';
    if (file_exists($sql_file)) {
        echo "<p style='color: green;'>✓ SQL file found</p>";
        
        $sql_content = file_get_contents($sql_file);
        
        // Split SQL commands
        $commands = array_filter(array_map('trim', explode(';', $sql_content)));
        
        echo "<p style='color: blue;'>📊 Found " . count($commands) . " SQL commands</p>";
        
        $success = 0;
        $errors = 0;
        
        foreach ($commands as $command) {
            if (!empty($command) && !preg_match('/^--/', $command)) {
                try {
                    $pdo->exec($command);
                    $success++;
                } catch (PDOException $e) {
                    $errors++;
                    echo "<p style='color: orange;'>⚠️ Warning: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
            }
        }
        
        echo "<p style='color: green;'>✅ Import completed: {$success} successful, {$errors} warnings</p>";
        
        // Test connection to new database
        $pdo_test = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $tables = $pdo_test->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h3>📋 Created Tables:</h3>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>{$table}</li>";
        }
        echo "</ul>";
        
        // Show sample data
        echo "<h3>📊 Sample Data:</h3>";
        
        try {
            $pemesanan = $pdo_test->query("SELECT COUNT(*) FROM pemesanan")->fetchColumn();
            echo "<p>Pemesanan: {$pemesanan} records</p>";
            
            $penyewa = $pdo_test->query("SELECT COUNT(*) FROM penyewa")->fetchColumn();
            echo "<p>Penyewa: {$penyewa} records</p>";
            
            $admin = $pdo_test->query("SELECT COUNT(*) FROM admin")->fetchColumn();
            echo "<p>Admin: {$admin} records</p>";
            
            echo "<div style='background: #e8f5e8; padding: 1rem; border-radius: 8px; margin-top: 1rem;'>";
            echo "<h4>🔑 Login Credentials:</h4>";
            echo "<p><strong>Admin:</strong> username: admin, password: admin123</p>";
            echo "<p><strong>Sample User:</strong> username: budisantoso123, password: password123</p>";
            echo "</div>";
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error checking data: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ SQL file not found: {$sql_file}</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<br><a href='test_connection.php' style='padding: 0.5rem 1rem; background: #007bff; color: white; text-decoration: none; border-radius: 4px;'>Test Connection</a>";
echo "<a href='dashboard.php' style='margin-left: 1rem; padding: 0.5rem 1rem; background: #28a745; color: white; text-decoration: none; border-radius: 4px;'>Go to Admin Panel</a>";
?>
