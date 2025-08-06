<?php
// Test database connection
require_once '../config.php';

echo "<h1>Database Connection Test</h1>";

if (isset($conn)) {
    echo "<p style='color: green;'>✓ Database connection variable exists</p>";
    
    if ($conn) {
        echo "<p style='color: green;'>✓ Database connection successful</p>";
        
        // Test query
        $test_query = mysqli_query($conn, "SHOW TABLES");
        if ($test_query) {
            echo "<p style='color: green;'>✓ Can execute queries</p>";
            echo "<h3>Available Tables:</h3><ul>";
            while ($row = mysqli_fetch_array($test_query)) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: red;'>✗ Cannot execute queries: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Database connection failed</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Database connection variable not found</p>";
}

// Test config file
echo "<h3>Config file content:</h3>";
echo "<pre>";
echo htmlspecialchars(file_get_contents('../config.php'));
echo "</pre>";
?>
