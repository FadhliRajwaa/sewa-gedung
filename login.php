<?php
// File: login.php
session_start();
require_once 'config.php'; // pastikan file ini mengatur koneksi $pdo

function sanitize($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM penyewa WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Untuk development, skip email verification atau set default true
            // if (!$user['email_terverifikasi']) {
            //     $error = "Akun belum diverifikasi. Silakan cek email Anda.";
            // } else {
                $_SESSION['id_penyewa'] = $user['id_penyewa'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['tipe_penyewa'] = $user['tipe_penyewa'];
                $_SESSION['username'] = $user['username'];
                
                // Check if there's a redirect URL
                if (isset($_GET['redirect'])) {
                    $redirect_url = $_GET['redirect'];
                    header('Location: ' . $redirect_url);
                } elseif (isset($_SESSION['redirect_after_login'])) {
                    $redirect_url = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    header('Location: ' . $redirect_url);
                } else {
                    header('Location: dashboard_user.php');
                }
                exit;
            // }
        } else {
            $error = "Username atau password salah.";
        }
    } catch (PDOException $e) {
        $error = "Terjadi kesalahan saat login.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Penyewa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: #B8860B;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #fff;
        }

        .container {
            max-width: 400px;
            width: 100%;
            padding: 40px;
            background-color: #B8860B;
            border-radius: 15px;
            color: white;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 60px;
            height: auto;
            background: white;
            padding: 10px;
            border-radius: 10px;
        }

        .title {
            text-align: center;
            color: white;
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .tabs {
            display: flex;
            margin-bottom: 30px;
            border-bottom: 2px solid rgba(255,255,255,0.3);
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 15px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            position: relative;
        }

        .tab.active {
            border-bottom: 3px solid white;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: white;
            font-size: 32px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 15px 20px;
            font-size: 16px;
            border: none;
            border-radius: 50px;
            background-color: white;
            color: #333;
            box-sizing: border-box;
            outline: none;
        }

        input::placeholder {
            color: #999;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background-color: rgba(0,0,0,0.3);
            border: none;
            border-radius: 50px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .btn-login:hover {
            background-color: rgba(0,0,0,0.5);
            transform: translateY(-2px);
        }

        .error {
            background-color: rgba(220, 53, 69, 0.8);
            color: white;
            padding: 10px 15px;
            border-radius: 25px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: white;
            text-decoration: underline;
            font-weight: 500;
        }

        .register-link a:hover {
            color: #FFD700;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="logoAU.png" alt="Logo">
        </div>
        <div class="title">Sistem Persewaan Gedung Serbaguna</div>
        
        <div class="tabs">
            <a href="login.php" class="tab active">Login</a>
            <a href="register.php" class="tab">Registrasi</a>
        </div>
        
        <?php if (!empty($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <input type="text" name="username" required placeholder="Email">
            </div>
            <div class="form-group">
                <input type="password" name="password" required placeholder="Password">
            </div>
            <button type="submit" class="btn-login">MASUK</button>
        </form>
        
        <div class="register-link">
            <p>Sudah punya akun? <a href="register.php">Daftar</a></p>
        </div>
    </div>
</body>
</html>
