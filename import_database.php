<?php
// Import database script
require_once 'config.php';

echo "<h2>🔄 IMPORTING DATABASE...</h2>";

// Read SQL file
$sql_file = 'gedung_pt_aneka_complete.sql';
$sql_content = file_get_contents($sql_file);

if ($sql_content === false) {
    die("❌ Error: Could not read SQL file");
}

// Split into individual statements
$statements = explode(';', $sql_content);

$success_count = 0;
$error_count = 0;

foreach ($statements as $statement) {
    $statement = trim($statement);
    if (empty($statement) || substr($statement, 0, 2) === '--') {
        continue;
    }
    
    if (mysqli_query($conn, $statement)) {
        $success_count++;
    } else {
        $error_count++;
        echo "<p style='color: red;'>❌ Error: " . mysqli_error($conn) . "</p>";
        echo "<pre style='background: #ffeeee; padding: 10px;'>" . substr($statement, 0, 200) . "...</pre>";
    }
}

echo "<h3>✅ IMPORT COMPLETED!</h3>";
echo "<p>✅ Successful statements: $success_count</p>";
echo "<p>❌ Failed statements: $error_count</p>";

// Test data
echo "<h3>📊 DATABASE TEST:</h3>";

$tables = ['admin', 'acara', 'penyewa', 'pemesanan', 'pembayaran'];
foreach ($tables as $table) {
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "<p>📋 Table '$table': {$row['count']} records</p>";
    }
}

echo "<br><a href='admin/dashboard.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Go to Admin Dashboard</a>";
?>
