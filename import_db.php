<?php
require_once 'config.php';

// Read SQL file
$sql = file_get_contents('gedung_pt_aneka_complete.sql');

// Remove comments and split by semicolon
$sql = preg_replace('/--.*$/m', '', $sql);
$sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
$queries = array_filter(array_map('trim', explode(';', $sql)));

try {
    // Disable foreign key checks
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    
    echo "<h2>Importing Database...</h2>";
    
    foreach ($queries as $query) {
        if (!empty($query) && !preg_match('/^(CREATE DATABASE|USE )/i', $query)) {
            try {
                $pdo->exec($query);
                echo "✓ Executed: " . substr($query, 0, 50) . "...<br>";
            } catch (Exception $e) {
                echo "✗ Error in query: " . substr($query, 0, 50) . "...<br>";
                echo "Error: " . $e->getMessage() . "<br><br>";
            }
        }
    }
    
    // Re-enable foreign key checks
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    
    echo "<br><h3>✅ Database import completed!</h3>";
    echo "<a href='admin/login.php'>Go to Admin Login</a>";
    
} catch (Exception $e) {
    echo "Database import failed: " . $e->getMessage();
}
?>
