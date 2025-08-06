<?php
session_start();

echo "<h2>Session Debug Information</h2>";
echo "<p><strong>Session Status:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? "Active" : "Inactive") . "</p>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>User Logged In:</strong> " . (isset($_SESSION['id_penyewa']) ? "Yes" : "No") . "</p>";

if (isset($_SESSION['id_penyewa'])) {
    echo "<p><strong>User ID:</strong> " . $_SESSION['id_penyewa'] . "</p>";
    echo "<p><strong>Username:</strong> " . ($_SESSION['username'] ?? 'Not set') . "</p>";
}

echo "<h3>All Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<p><a href='login.php'>Go to Login</a></p>";
echo "<p><a href='pernikahan_test.php'>Test Pernikahan Page (No Login Required)</a></p>";
echo "<p><a href='pernikahan.php'>Original Pernikahan Page (Login Required)</a></p>";
?>
