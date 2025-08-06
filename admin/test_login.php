<?php
session_start();

// Simulasi login admin untuk testing
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_username'] = 'admin';

echo "Admin session set successfully!<br>";
echo '<a href="dashboard.php">Go to Dashboard</a>';
?>
