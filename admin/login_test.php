<?php
session_start();
require_once '../config.php';

if ($_POST) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Quick login for testing - bypass authentication
    if ($username == 'admin') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = 1;
        $_SESSION['username'] = 'admin';
        
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Login failed";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - Test</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            width: 100%;
            padding: 0.75rem;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: #5a67d8;
        }
        .error {
            color: #e53e3e;
            text-align: center;
            margin-top: 1rem;
        }
        .info {
            background: #e8f4fd;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            border-left: 4px solid #3182ce;
        }
        .info h4 {
            margin: 0 0 0.5rem 0;
            color: #2c5282;
        }
        .info p {
            margin: 0;
            color: #2c5282;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>🔐 Admin Login Test</h1>
        
        <div class="info">
            <h4>Test Credentials:</h4>
            <p><strong>Username:</strong> admin</p>
            <p><strong>Password:</strong> (any password)</p>
            <p style="margin-top: 0.5rem; font-style: italic;">This is a test login - authentication is bypassed</p>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="admin" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" value="admin123" required>
            </div>
            
            <button type="submit" class="btn">Login to Admin Panel</button>
            
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
        </form>
        
        <div style="text-align: center; margin-top: 2rem;">
            <a href="import_database.php" style="color: #667eea; text-decoration: none;">🗄️ Import Database First</a> |
            <a href="test_connection.php" style="color: #667eea; text-decoration: none;">🔧 Test Connection</a>
        </div>
    </div>
</body>
</html>
