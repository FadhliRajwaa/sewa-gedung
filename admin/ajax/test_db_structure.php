<?php
require_once '../../config.php';

echo "<h1>Database Structure Test</h1>";

try {
    // Test tabel pemesanan
    echo "<h2>Tabel Pemesanan:</h2>";
    $stmt = $pdo->query("DESCRIBE pemesanan");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test tabel pembayaran
    echo "<h2>Tabel Pembayaran:</h2>";
    $stmt = $pdo->query("DESCRIBE pembayaran");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test tabel acara
    echo "<h2>Tabel Acara:</h2>";
    $stmt = $pdo->query("DESCRIBE acara");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test tabel penyewa
    echo "<h2>Tabel Penyewa:</h2>";
    $stmt = $pdo->query("DESCRIBE penyewa");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test data sample
    echo "<h2>Sample Data:</h2>";
    
    echo "<h3>Pemesanan Sample:</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM pemesanan");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total pemesanan: " . $count['count'] . "<br>";
    
    echo "<h3>Pembayaran Sample:</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM pembayaran");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total pembayaran: " . $count['count'] . "<br>";
    
    echo "<h3>Acara Sample:</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM acara");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total acara: " . $count['count'] . "<br>";
    
    echo "<h3>Penyewa Sample:</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM penyewa");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total penyewa: " . $count['count'] . "<br>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
