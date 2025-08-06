<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Kami - PT Aneka Usaha</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary-gold: #B8860B;
            --secondary-gold: #DAA520;
            --light-gold: #F4E4BC;
            --dark-gray: #2c3e50;
            --light-gray: #f8f9fa;
            --white: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.15);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, var(--light-gray) 0%, var(--white) 50%, var(--light-gray) 100%);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            line-height: 1.6;
            min-height: 100vh;
        }
        
        /* Improve touch targets for mobile */
        button, a, .btn {
            min-height: 44px;
            min-width: 44px;
        }
        
        /* Header */
        .navbar {
            background: linear-gradient(135deg, var(--primary-gold) 0%, var(--secondary-gold) 100%) !important;
            padding: 20px 0;
            box-shadow: var(--shadow-lg);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        
        .navbar-brand {
            display: flex;
            align-items: center;
            transition: var(--transition);
        }
        
        .navbar-brand:hover {
            transform: scale(1.05);
        }
        
        .navbar-brand img {
            width: 45px;
            height: 45px;
            margin-right: 12px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .navbar-brand span {
            color: var(--white);
            font-weight: 700;
            font-size: 20px;
            letter-spacing: -0.5px;
        }
        
        .navbar-nav .nav-link {
            color: var(--white) !important;
            font-weight: 500;
            margin: 0 8px;
            padding: 10px 16px !important;
            border-radius: 25px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }
        
        .navbar-nav .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: var(--transition);
        }
        
        .navbar-nav .nav-link:hover::before {
            left: 100%;
        }
        
        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        
        /* Modern Hamburger Menu */
        .navbar-toggler {
            border: none;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 8px;
            backdrop-filter: blur(10px);
            transition: var(--transition);
        }
        
        .navbar-toggler:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }
        
        .navbar-toggler:focus {
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
        }
        
        /* Custom hamburger icon - Fixed */
        .navbar-toggler-icon {
            background-image: none;
            width: 24px;
            height: 18px;
            position: relative;
            display: block;
        }
        
        .navbar-toggler-icon,
        .navbar-toggler-icon::before,
        .navbar-toggler-icon::after {
            display: block;
            height: 3px;
            background: var(--white);
            border-radius: 2px;
            transition: all 0.3s ease-in-out;
        }
        
        .navbar-toggler-icon::before,
        .navbar-toggler-icon::after {
            content: '';
            position: absolute;
            width: 100%;
        }
        
        .navbar-toggler-icon::before {
            top: -7px;
        }
        
        .navbar-toggler-icon::after {
            top: 7px;
        }
        
        /* Hamburger animation when active */
        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon {
            background: transparent;
        }
        
        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon::before {
            transform: rotate(45deg);
            top: 0;
        }
        
        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon::after {
            transform: rotate(-45deg);
            top: 0;
        }
        
        .navbar-collapse {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.95), rgba(44, 62, 80, 0.95));
            margin-top: 15px;
            border-radius: var(--border-radius);
            padding: 25px;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(184, 134, 11, 0.3);
            box-shadow: var(--shadow-lg);
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, rgba(0,0,0,0.6), rgba(184, 134, 11, 0.3)), url('asset/gambar/bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 120px 0;
            text-align: center;
            color: var(--white);
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(184, 134, 11, 0.1) 70%);
        }
        
        .page-header-content {
            position: relative;
            z-index: 2;
            animation: fadeInUp 1s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .page-header h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 2px 4px 8px rgba(0,0,0,0.6);
            letter-spacing: -1px;
        }
        
        .page-header p {
            font-size: 1.2rem;
            text-shadow: 1px 2px 4px rgba(0,0,0,0.5);
            opacity: 0.9;
        }
        
        /* Contact Section */
        .contact-section {
            padding: 100px 0;
            position: relative;
        }
        
        .contact-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23B8860B" opacity="0.02"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }
        
        .contact-container {
            background: var(--white);
            border-radius: 24px;
            padding: 50px;
            box-shadow: var(--shadow-lg);
            position: relative;
            z-index: 2;
            border: 1px solid rgba(184, 134, 11, 0.1);
        }
        
        .contact-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-gold), var(--secondary-gold));
            border-radius: 24px 24px 0 0;
        }
        
        .contact-container h2 {
            text-align: center;
            margin-bottom: 40px;
            color: var(--primary-gold);
            font-size: 2.5rem;
            font-weight: 800;
            position: relative;
        }
        
        .contact-container h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-gold), var(--secondary-gold));
            border-radius: 2px;
        }
        
        .form-group {
            margin-bottom: 30px;
        }
        
        .form-group label {
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-group label i {
            color: var(--primary-gold);
            font-size: 18px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px 20px;
            font-size: 16px;
            transition: var(--transition);
            background: var(--light-gray);
        }
        
        .form-control:focus {
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 3px rgba(184, 134, 11, 0.1);
            background: var(--white);
        }
        
        .form-control::placeholder {
            color: #6c757d;
            opacity: 0.8;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-gold) 0%, var(--secondary-gold) 100%);
            border: none;
            color: var(--white);
            padding: 18px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 50px;
            transition: var(--transition);
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
            width: 100%;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: var(--transition);
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(184, 134, 11, 0.4);
        }

        
        /* Contact Info Cards */
        .contact-info {
            margin-top: 60px;
        }
        
        .contact-card {
            background: var(--white);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
            margin-bottom: 30px;
            border: 1px solid rgba(184, 134, 11, 0.1);
        }
        
        .contact-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }
        
        .contact-card-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, var(--primary-gold) 0%, var(--secondary-gold) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 32px;
            box-shadow: var(--shadow);
        }
        
        .contact-card h4 {
            color: var(--primary-gold);
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .contact-card p {
            color: #666;
            margin: 0;
            line-height: 1.6;
        }
        
        /* Location Card */
        .location-section {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--primary-gold) 0%, var(--secondary-gold) 100%);
        }
        
        .location-card {
            background: var(--white);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow-lg);
            text-align: center;
        }
        
        .location-card h3 {
            color: var(--primary-gold);
            font-weight: 700;
            margin-bottom: 30px;
            font-size: 2rem;
        }
        
        .location-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .location-info i {
            color: var(--secondary-gold);
            font-size: 32px;
        }
        
        .location-details h5 {
            color: var(--primary-gold);
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .location-details p {
            color: #666;
            margin: 0;
            line-height: 1.6;
        }
        
        .btn-maps {
            background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
            color: var(--white);
            padding: 15px 30px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }
        
        .btn-maps:hover {
            color: var(--white);
            text-decoration: none;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(184, 134, 11, 0.3);
        }
        
        /* Footer */
        .footer {
            background: linear-gradient(135deg, #1a252f 0%, #2c3e50 50%, #34495e 100%);
            color: var(--white);
            padding: 60px 0 30px;
            position: relative;
            overflow: hidden;
        }
        
        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary-gold), var(--secondary-gold), var(--primary-gold));
            box-shadow: 0 2px 10px rgba(184, 134, 11, 0.3);
        }
        
        .footer p {
            margin: 0;
            color: #95a5a6;
            text-align: center;
        }
        
        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            /* Modern Mobile Navigation */
            .navbar-brand span {
                font-size: 16px;
                font-weight: 700;
            }
            
            .navbar-brand img {
                width: 35px;
                height: 35px;
            }
            
            /* Modern Mobile Menu */
            .navbar-collapse {
                background: linear-gradient(135deg, rgba(0, 0, 0, 0.95), rgba(44, 62, 80, 0.95));
                backdrop-filter: blur(25px);
                border-radius: 20px;
                margin-top: 15px;
                padding: 25px 20px;
                border: 1px solid rgba(184, 134, 11, 0.3);
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            }
            
            .navbar-nav .nav-link {
                margin: 10px 0;
                padding: 18px 25px !important;
                border-radius: 15px;
                transition: var(--transition);
                font-weight: 500;
                border: 1px solid rgba(255, 255, 255, 0.1);
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            
            .navbar-nav .nav-link::after {
                content: '→';
                opacity: 0;
                transform: translateX(-10px);
                transition: var(--transition);
            }
            
            .navbar-nav .nav-link:hover {
                background: linear-gradient(135deg, var(--primary-gold) 0%, var(--secondary-gold) 100%);
                transform: translateX(5px);
                border-color: var(--primary-gold);
                box-shadow: 0 5px 15px rgba(184, 134, 11, 0.3);
            }
            
            .navbar-nav .nav-link:hover::after {
                opacity: 1;
                transform: translateX(0);
            }
            
            /* Page Header Mobile */
            .page-header {
                padding: 80px 0;
            }
            
            .page-header h1 {
                font-size: 2.2rem;
                margin-bottom: 15px;
            }
            
            .page-header p {
                font-size: 1.1rem;
            }
            
            /* Contact Section Mobile */
            .contact-section {
                padding: 60px 0;
            }
            
            .contact-container {
                padding: 30px 25px;
                margin: 0 15px;
            }
            
            .contact-container h2 {
                font-size: 2rem;
                margin-bottom: 30px;
            }
            
            .form-group {
                margin-bottom: 25px;
            }
            
            .form-control {
                padding: 12px 15px;
                font-size: 15px;
            }
            
            .btn-primary {
                padding: 15px 30px;
                font-size: 16px;
            }
            
            /* Location Section Mobile */
            .location-section {
                padding: 60px 0;
            }
            
            .location-card {
                padding: 30px 20px;
                margin: 0 15px;
            }
            
            .location-card h3 {
                font-size: 1.6rem;
                margin-bottom: 25px;
            }
            
            .location-info {
                flex-direction: column;
                gap: 15px;
            }
            
            .location-details {
                text-align: center;
            }
            
            /* Contact Cards Mobile */
            .contact-card {
                margin-bottom: 20px;
                padding: 25px 20px;
            }
            
            .contact-card-icon {
                width: 70px;
                height: 70px;
                font-size: 28px;
                margin-bottom: 15px;
            }
        }
        
        @media (max-width: 576px) {
            .page-header h1 {
                font-size: 1.8rem;
            }
            
            .contact-container {
                padding: 25px 20px;
            }
            
            .contact-container h2 {
                font-size: 1.7rem;
            }
            
            .location-card {
                padding: 25px 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="logoAU.png" alt="PT Aneka Usaha">
                <span>PT ANEKA USAHA</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#acara">Pilih Acara</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="panduan.php">Panduan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="kontak.php">Hubungi Kami</a>
                    </li>
                    <?php if (isset($_SESSION['id_penyewa'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="akun.php">Akun</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Daftar</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <h1><i class="fas fa-envelope"></i> Hubungi Kami</h1>
                <p>Kami siap membantu Anda dengan informasi penyewaan gedung</p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-12">
                    <div class="contact-container">
                        <h2><i class="fas fa-paper-plane"></i> Kirim Pesan</h2>
                        <form action="proses_kontak.php" method="POST">
                            <div class="form-group">
                                <label for="nama">
                                    <i class="fas fa-user"></i>
                                    Nama Lengkap
                                </label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama lengkap Anda" required>
                            </div>
                            <div class="form-group">
                                <label for="email">
                                    <i class="fas fa-envelope"></i>
                                    Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan alamat email Anda" required>
                            </div>
                            <div class="form-group">
                                <label for="pesan">
                                    <i class="fas fa-comment"></i>
                                    Pesan
                                </label>
                                <textarea class="form-control" id="pesan" name="pesan" rows="6" placeholder="Tulis pesan Anda di sini..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                                Kirim Pesan
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-12">
                    <div class="contact-info">
                        <div class="contact-card">
                            <div class="contact-card-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <h4>Alamat</h4>
                            <p>Jl. Jenderal Sudirman No 1<br>Pemalang, Jawa Tengah 52312</p>
                        </div>
                        
                        <div class="contact-card">
                            <div class="contact-card-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <h4>Telepon</h4>
                            <p>0285-22198-28</p>
                        </div>
                        
                        <div class="contact-card">
                            <div class="contact-card-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h4>Email</h4>
                            <p>info@anekaperseroda.co.id</p>
                        </div>
                        
                        <div class="contact-card">
                            <div class="contact-card-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h4>Jam Operasional</h4>
                            <p>Senin - Jumat: 08.00 - 16.00<br>Sabtu: 08.00 - 12.00</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Location Section -->
    <section class="location-section">
        <div class="container">
            <div class="location-card">
                <h3><i class="fas fa-map-marked-alt"></i> Lokasi Kami</h3>
                <div class="location-info">
                    <i class="fas fa-building"></i>
                    <div class="location-details">
                        <h5>PT Aneka Usaha Gedung Serbaguna</h5>
                        <p>Gedung kami terletak di lokasi strategis di pusat kota Pemalang, mudah diakses dari berbagai arah dan dekat dengan fasilitas umum.</p>
                    </div>
                </div>
                <a href="https://maps.google.com/?q=Jl.+Jenderal+Sudirman+No+1+Pemalang" target="_blank" class="btn-maps">
                    <i class="fas fa-map"></i>
                    Buka di Google Maps
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> PT Aneka Usaha Perseroda. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
