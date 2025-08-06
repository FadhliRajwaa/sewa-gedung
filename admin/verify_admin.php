<?php
require_once '../config.php';

// Test login dengan credentials baru
$username = 'admin';
$password = 'admin123';

try {
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin && password_verify($password, $admin['password'])) {
        echo "<h2>✅ LOGIN BERHASIL!</h2>";
        echo "<h3>Kredensial Admin:</h3>";
        echo "<div style='background: #e8f5e8; padding: 20px; border-radius: 8px; font-family: monospace;'>";
        echo "<strong>Username:</strong> " . $admin['username'] . "<br>";
        echo "<strong>Password:</strong> admin123<br>";
        echo "<strong>Email:</strong> " . $admin['email'] . "<br>";
        echo "<strong>Nama:</strong> " . $admin['nama_admin'] . "<br>";
        echo "</div>";
        echo "<br><h3>Login URL:</h3>";
        echo "<a href='login.php' style='background: #8B4513; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Login Admin</a>";
        echo "<br><br><h3>Direct Dashboard (bypass login for testing):</h3>";
        echo "<a href='test_login.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Set Session & Go to Dashboard</a>";
    } else {
        echo "<h2>❌ LOGIN GAGAL!</h2>";
        echo "<p>Username atau password salah.</p>";
    }
} catch (PDOException $e) {
    echo "<h2>❌ ERROR DATABASE!</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
