<?php
session_start();
require_once 'config.php';
require_once 'includes/pricing.php';

// Check if user is logged in
if (!isset($_SESSION['id_penyewa'])) {
    header('Location: login.php');
    exit;
}

// Get all events from database
try {
    $query = "SELECT * FROM acara WHERE status = 'tersedia' ORDER BY id_acara";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Get pricing data from database
$eventPricing = getEventPricing($pdo);

// Map events to images and prices
$eventData = [
    'Seminar' => ['image' => 'Seminar.jpg', 'price' => $eventPricing[3]['formatted_price']],
    'Pernikahan' => ['image' => 'Pernikahan.jpg', 'price' => $eventPricing[1]['formatted_price']],
    'Rapat' => ['image' => 'Rapat.jpg', 'price' => $eventPricing[2]['formatted_price']],
    'Rapat/Meeting' => ['image' => 'Rapat.jpg', 'price' => $eventPricing[2]['formatted_price']]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Acara - PT Aneka Usaha</title>
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
        
        /* Navigation Styles */
        .navbar {
            background: linear-gradient(135deg, var(--primary-gold) 0%, var(--secondary-gold) 100%);
            box-shadow: var(--shadow);
            padding: 1rem 0;
            transition: var(--transition);
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--white) !important;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .navbar-brand:hover {
            color: var(--light-gold) !important;
            transform: translateY(-1px);
        }
        
        .navbar-nav .nav-link {
            color: var(--white) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            padding: 0.75rem 1rem !important;
            border-radius: 8px;
            transition: var(--transition);
            position: relative;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: var(--light-gold) !important;
            transform: translateY(-2px);
        }
        
        .navbar-toggler {
            border: 2px solid var(--white);
            background: transparent;
            padding: 0.5rem;
            border-radius: 8px;
        }
        
        .navbar-toggler-icon {
            color: var(--white);
        }
        
        /* Main Content */
        .main-content {
            margin-top: 2rem;
            margin-bottom: 3rem;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem 0;
        }
        
        .page-title {
            font-size: 3rem;
            font-weight: 800;
            color: var(--dark-gray);
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            letter-spacing: -0.02em;
        }
        
        .page-subtitle {
            font-size: 1.25rem;
            color: #6c757d;
            font-weight: 400;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.5;
        }
        
        /* Event Cards */
        .event-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 2.5rem;
            padding: 2rem 0;
        }
        
        .event-card {
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
            height: 480px;
            display: flex;
            flex-direction: column;
        }
        
        .event-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }
        
        .card-image {
            height: 240px;
            overflow: hidden;
            position: relative;
        }
        
        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }
        
        .event-card:hover .card-image img {
            transform: scale(1.05);
        }
        
        .card-content {
            padding: 2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .event-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-gray);
            margin-bottom: 0.75rem;
            text-align: center;
        }
        
        .event-price {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-gold);
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .btn-book {
            background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
            color: var(--white);
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            text-decoration: none;
            text-align: center;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: auto;
        }
        
        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(184, 134, 11, 0.3);
            color: var(--white);
            text-decoration: none;
        }
        
        .btn-book:active {
            transform: translateY(0);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2.5rem;
            }
            
            .page-subtitle {
                font-size: 1.1rem;
                padding: 0 1rem;
            }
            
            .event-cards {
                grid-template-columns: 1fr;
                gap: 2rem;
                padding: 1rem;
            }
            
            .event-card {
                height: auto;
                min-height: 400px;
            }
            
            .card-image {
                height: 200px;
            }
            
            .card-content {
                padding: 1.5rem;
            }
            
            .event-title {
                font-size: 1.3rem;
            }
            
            .event-price {
                font-size: 1.1rem;
            }
            
            .btn-book {
                padding: 0.875rem 1.5rem;
                font-size: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .page-title {
                font-size: 2rem;
            }
            
            .navbar-brand {
                font-size: 1.3rem;
            }
            
            .event-cards {
                padding: 0.5rem;
                gap: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Modern Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="dashboard_user.php">
                <i class="fas fa-building"></i> PT ANEKA USAHA
            </a>
            
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard_user.php">
                            <i class="fas fa-home"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="sewa.php">
                            <i class="fas fa-calendar-check"></i> Pilih Acara
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="acara_saya.php">
                            <i class="fas fa-calendar-alt"></i> Acara Saya
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="panduan.php">
                            <i class="fas fa-book"></i> Panduan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="akun.php">
                            <i class="fas fa-user"></i> Akun
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Pilih Acara</h1>
                <p class="page-subtitle">
                    Pilih jenis acara yang ingin Anda selenggarakan dan lakukan pemesanan langsung dengan mudah.
                </p>
            </div>

            <!-- Event Cards -->
            <div class="event-cards">
                <?php foreach ($events as $event): 
                    $eventName = $event['nama_acara'];
                    $eventImage = isset($eventData[$eventName]['image']) ? $eventData[$eventName]['image'] : 'asset/gambar/default.jpg';
                    $eventPrice = isset($eventData[$eventName]['price']) ? $eventData[$eventName]['price'] : 'Hubungi Kami';
                    
                    // Map to specific booking pages
                    $bookingLink = '';
                    switch(strtolower($eventName)) {
                        case 'seminar':
                            $bookingLink = 'proses_sewa.php?acara=seminar';
                            break;
                        case 'pernikahan':
                            $bookingLink = 'proses_sewa.php?acara=pernikahan';
                            break;
                        case 'rapat':
                        case 'rapat/meeting':
                            $bookingLink = 'proses_sewa.php?acara=rapat';
                            break;
                        default:
                            $bookingLink = 'sewa.php';
                    }
                ?>
                <div class="event-card">
                    <div class="card-image">
                        <img src="<?= htmlspecialchars($eventImage) ?>" 
                             alt="<?= htmlspecialchars($eventName) ?>"
                             onerror="this.src='asset/gambar/default.jpg'">
                    </div>
                    <div class="card-content">
                        <h3 class="event-title"><?= htmlspecialchars($eventName) ?></h3>
                        <div class="event-price"><?= htmlspecialchars($eventPrice) ?></div>
                        <a href="<?= htmlspecialchars($bookingLink) ?>" class="btn-book">
                            <i class="fas fa-calendar-plus"></i>
                            Pesan Sekarang
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
