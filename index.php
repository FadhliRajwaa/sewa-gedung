<?php 
session_start(); 
require_once 'config.php';
require_once 'includes/pricing.php';

// Get pricing data from database
$eventPricing = getEventPricing($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Aneka Usaha - Penyewaan Gedung Serbaguna</title>
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
            background-color: var(--light-gray);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            line-height: 1.6;
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
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, rgba(0,0,0,0.6), rgba(184, 134, 11, 0.3)), url('asset/gambar/bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 85vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--white);
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(184, 134, 11, 0.1) 70%);
        }
        
        .hero-content {
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
        
        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 24px;
            text-shadow: 2px 4px 8px rgba(0,0,0,0.6);
            line-height: 1.2;
            letter-spacing: -1px;
        }
        
        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 40px;
            text-shadow: 1px 2px 4px rgba(0,0,0,0.5);
            font-weight: 400;
            opacity: 0.95;
        }
        
        .btn-mulai {
            background: linear-gradient(135deg, var(--primary-gold) 0%, var(--secondary-gold) 100%);
            border: none;
            color: var(--white);
            padding: 18px 45px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 50px;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }
        
        .btn-mulai::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: var(--transition);
        }
        
        .btn-mulai:hover::before {
            left: 100%;
        }
        
        .btn-mulai:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 35px rgba(184, 134, 11, 0.4);
            color: var(--white);
            text-decoration: none;
        }
        
        /* Pilihan Acara Section */
        .pilihan-acara {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--primary-gold) 0%, var(--secondary-gold) 100%);
            position: relative;
        }
        
        .pilihan-acara::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--secondary-gold), var(--primary-gold), var(--secondary-gold));
        }
        
        .pilihan-acara h2 {
            text-align: center;
            color: var(--white);
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 60px;
            letter-spacing: -1px;
            position: relative;
        }
        
        .pilihan-acara h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--white);
            border-radius: 2px;
        }
        
        .acara-card {
            background: var(--white);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            margin-bottom: 30px;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .acara-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-gold), var(--secondary-gold));
        }
        
        .acara-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }
        
        .acara-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: var(--transition);
        }
        
        .acara-card:hover img {
            transform: scale(1.05);
        }
        
        .acara-card-body {
            padding: 30px;
        }
        
        .acara-card h3 {
            color: var(--primary-gold);
            font-weight: 700;
            margin-bottom: 16px;
            font-size: 1.5rem;
        }
        
        .acara-card p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
            font-weight: 400;
        }
        
        .acara-details {
            font-size: 14px;
            color: #777;
            margin-bottom: 25px;
            background: var(--light-gray);
            padding: 16px;
            border-radius: 12px;
            border-left: 4px solid var(--primary-gold);
        }
        
        .btn-cek {
            background: linear-gradient(135deg, var(--primary-gold) 0%, var(--secondary-gold) 100%);
            color: var(--white);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }
        
        .btn-cek::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: var(--transition);
        }
        
        .btn-cek:hover::before {
            left: 100%;
        }
        
        .btn-cek:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(184, 134, 11, 0.3);
        }
        
        /* Panduan Section */
        .panduan-section {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--primary-gold) 0%, var(--secondary-gold) 100%);
            position: relative;
        }
        
        .panduan-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--secondary-gold), var(--primary-gold), var(--secondary-gold));
        }
        
        .panduan-section h2 {
            text-align: center;
            color: var(--white);
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 70px;
            letter-spacing: -1px;
            position: relative;
        }
        
        .panduan-section h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--white);
            border-radius: 2px;
        }
        
        .panduan-step {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
        }
        
        .panduan-icon {
            width: 100px;
            height: 100px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 36px;
            color: var(--primary-gold);
            position: relative;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
        }
        
        .panduan-icon::before {
            content: '';
            position: absolute;
            top: -6px;
            left: -6px;
            right: -6px;
            bottom: -6px;
            background: linear-gradient(135deg, var(--white), rgba(255,255,255,0.8));
            border-radius: 50%;
            z-index: -1;
            animation: pulse 3s infinite;
        }
        
        .panduan-step:hover .panduan-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        .step-number {
            position: absolute;
            top: -12px;
            right: -12px;
            background: linear-gradient(135deg, var(--secondary-gold) 0%, var(--primary-gold) 100%);
            color: var(--white);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            box-shadow: var(--shadow);
            border: 3px solid var(--white);
        }
        
        .panduan-step h4 {
            color: var(--white);
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        
        .panduan-step p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 15px;
            line-height: 1.6;
            font-weight: 400;
            max-width: 280px;
            margin: 0 auto;
        }
        
        /* Footer */
        .footer {
            background: linear-gradient(135deg, #1a252f 0%, #2c3e50 50%, #34495e 100%);
            color: var(--white);
            padding: 80px 0 0;
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
        
        .footer::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="%23B8860B" stroke-width="0.5" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            pointer-events: none;
        }
        
        .footer .container {
            position: relative;
            z-index: 2;
            padding-bottom: 30px;
        }
        
        .footer h5 {
            color: var(--secondary-gold);
            margin-bottom: 30px;
            font-weight: 700;
            font-size: 1.4rem;
            position: relative;
            display: inline-block;
        }
        
        .footer h5::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-gold), var(--secondary-gold));
            border-radius: 2px;
        }
        
        .footer p, .footer a {
            color: #bdc3c7;
            text-decoration: none;
            line-height: 1.8;
            transition: var(--transition);
            margin-bottom: 12px;
        }
        
        .footer a:hover {
            color: var(--secondary-gold);
            transform: translateX(5px);
            text-decoration: none;
        }
        
        .footer .embed-responsive {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            border: 2px solid rgba(184, 134, 11, 0.2);
        }
        
        /* Location Card Styles */
        .location-card {
            background: linear-gradient(135deg, rgba(184, 134, 11, 0.1), rgba(218, 165, 32, 0.1));
            border-radius: 15px;
            padding: 25px;
            border: 2px solid rgba(184, 134, 11, 0.2);
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
        }
        
        .location-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }
        
        .location-info {
            text-align: left;
        }
        
        .location-address {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .location-address i {
            color: var(--secondary-gold);
            font-size: 24px;
            margin-top: 5px;
        }
        
        .location-address strong {
            color: var(--secondary-gold);
            font-size: 16px;
            display: block;
            margin-bottom: 8px;
        }
        
        .location-address p {
            margin: 0;
            color: #bdc3c7;
            line-height: 1.6;
        }
        
        .btn-maps {
            background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
            color: var(--white);
            padding: 12px 20px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }
        
        .btn-maps:hover {
            color: var(--white);
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(184, 134, 11, 0.3);
        }
        
        .footer-bottom {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.3), rgba(44, 62, 80, 0.3));
            border-top: 1px solid rgba(184, 134, 11, 0.2);
            margin-top: 50px;
            padding: 25px 0;
            text-align: center;
            backdrop-filter: blur(10px);
        }
        
        .footer-bottom p {
            margin: 0;
            color: #95a5a6;
            font-weight: 400;
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
            
            /* Hero Section Mobile */
            .hero-section {
                height: 70vh;
                padding: 30px 20px;
            }
            
            .hero-content h1 {
                font-size: 2.2rem;
                line-height: 1.2;
                margin-bottom: 20px;
                text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
            }
            
            .hero-content p {
                font-size: 1.1rem;
                margin-bottom: 30px;
                line-height: 1.6;
            }
            
            .btn-mulai {
                padding: 15px 35px;
                font-size: 16px;
                font-weight: 600;
            }
            
            /* Pilihan Acara Mobile */
            .pilihan-acara {
                padding: 80px 0;
            }
            
            .pilihan-acara h2 {
                font-size: 2.4rem;
                margin-bottom: 50px;
            }
            
            .acara-card {
                margin-bottom: 30px;
                border-radius: 20px;
            }
            
            .acara-card img {
                height: 200px;
            }
            
            .acara-card-body {
                padding: 25px;
            }
            
            .acara-card h3 {
                font-size: 1.4rem;
                margin-bottom: 15px;
            }
            
            .acara-card p {
                font-size: 15px;
                margin-bottom: 15px;
            }
            
            .acara-details {
                font-size: 14px;
                margin-bottom: 20px;
                padding: 12px;
            }
            
            .btn-cek {
                padding: 12px 25px;
                font-size: 15px;
                width: 100%;
                border-radius: 25px;
                font-weight: 600;
            }
            
            /* Panduan Section Mobile */
            .panduan-section {
                padding: 80px 0;
            }
            
            .panduan-section h2 {
                font-size: 2.4rem;
                margin-bottom: 50px;
            }
            
            .panduan-step {
                margin-bottom: 40px;
                padding: 0 20px;
            }
            
            .panduan-icon {
                width: 85px;
                height: 85px;
                font-size: 30px;
                margin-bottom: 20px;
            }
            
            .step-number {
                width: 32px;
                height: 32px;
                font-size: 14px;
                top: -10px;
                right: -10px;
                border-width: 2px;
            }
            
            .panduan-step h4 {
                font-size: 1.2rem;
                margin-bottom: 12px;
                line-height: 1.4;
            }
            
            .panduan-step p {
                font-size: 14px;
                line-height: 1.6;
                max-width: 300px;
            }
            
            /* Footer Mobile */
            .footer {
                padding: 60px 0 0;
            }
            
            .footer .col-lg-4 {
                margin-bottom: 40px;
                text-align: center;
            }
            
            .footer h5 {
                font-size: 1.4rem;
                margin-bottom: 25px;
                text-align: center;
            }
            
            .footer h5::after {
                left: 50%;
                transform: translateX(-50%);
            }
            
            .footer p {
                font-size: 15px;
                line-height: 1.8;
                text-align: center;
            }
            
            .footer .d-flex {
                justify-content: center;
                margin-bottom: 15px;
            }
            
            .footer .embed-responsive {
                max-width: 100%;
                margin: 0 auto;
                border-radius: 12px;
            }
            
            .location-card {
                text-align: center;
                padding: 20px;
            }
            
            .location-address {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
            
            .location-address i {
                align-self: center;
            }
            
            .footer-bottom {
                margin-top: 40px;
                padding: 20px 0;
            }
        }
        
        @media (max-width: 576px) {
            /* Extra small devices - Enhanced Mobile */
            .container {
                padding-left: 20px;
                padding-right: 20px;
            }
            
            .hero-content h1 {
                font-size: 1.9rem;
                margin-bottom: 15px;
            }
            
            .hero-content p {
                font-size: 1rem;
                margin-bottom: 25px;
            }
            
            .pilihan-acara h2,
            .panduan-section h2 {
                font-size: 2rem;
                margin-bottom: 40px;
            }
            
            .acara-card-body {
                padding: 20px;
            }
            
            .panduan-step {
                padding: 0 15px;
            }
            
            .panduan-icon {
                width: 75px;
                height: 75px;
                font-size: 28px;
            }
            
            .footer-bottom {
                margin-top: 30px;
                padding: 15px 0;
            }
            
            .footer .embed-responsive {
                border-radius: 10px;
            }
            
            .location-card {
                padding: 15px;
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
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#acara">Pilih Acara</a>
                    </li>
                    <?php if (isset($_SESSION['id_penyewa'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="acara_saya.php">Acara Saya</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="panduan.php">Panduan</a>
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>PENYEWAAN GEDUNG SERBAGUNA<br class="d-none d-md-block">PT ANEKA USAHA KABUPATEN<br class="d-none d-md-block">PEMALANG (PERSERODA)</h1>
                <p>Kami memiliki semangat dalam menyediakan penyewaan<br class="d-none d-md-block">gedung yang aman dan nyaman</p>
                <a href="login.php" class="btn-mulai">Mulai Penyewaan</a>
            </div>
        </div>
    </section>

    <!-- Pilihan Acara Section -->
    <section class="pilihan-acara" id="acara">
        <div class="container">
            <h2>PILIHAN ACARA</h2>
            <div class="row">
                <!-- Seminar Card -->
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="acara-card">
                        <img src="asset/gambar/gedung_seminar.jpg" alt="Seminar" loading="lazy">
                        <div class="acara-card-body">
                            <h3>Seminar</h3>
                            <p>Cocok untuk seminar dan konferensi.</p>
                            <div class="acara-details">
                                <p><strong>Harga Sewa <?= $eventPricing[3]['formatted_price'] ?>;</strong><br>
                                Kapasitas : 1.000 Orang<br>
                                Lokasi : Jl. Jenderal Sudirman No 1, Pemalang<br>
                                Fasilitas : Panggung Utama, Halaman Parkir Luas, Kipas Angin, Toilet<br>
                                Status : Tersedia</p>
                            </div>
                            <button class="btn btn-cek" onclick="location.href='seminar.php'">Cek Tanggal</button>
                        </div>
                    </div>
                </div>

                <!-- Pernikahan Card -->
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="acara-card">
                        <img src="asset/gambar/gedung_pernikahan.jpg" alt="Pernikahan" loading="lazy">
                        <div class="acara-card-body">
                            <h3>Pernikahan</h3>
                            <p>Fasilitas lengkap untuk acara Anda.</p>
                            <div class="acara-details">
                                <p><strong>Harga Sewa <?= $eventPricing[1]['formatted_price'] ?>;</strong><br>
                                Kapasitas : 1.000 Orang<br>
                                Lokasi : Jl. Jenderal Sudirman No 1, Pemalang<br>
                                Fasilitas : Panggung Utama, Halaman Parkir Luas, Kipas Angin, Toilet<br>
                                Status : Tersedia</p>
                            </div>
                            <button class="btn btn-cek" onclick="location.href='pernikahan.php'">Cek Tanggal</button>
                        </div>
                    </div>
                </div>

                <!-- Rapat Card -->
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="acara-card">
                        <img src="asset/gambar/gedung_rapat.jpg" alt="Rapat" loading="lazy">
                        <div class="acara-card-body">
                            <h3>Rapat</h3>
                            <p>Ideal untuk acara kecil dan intimate.</p>
                            <div class="acara-details">
                                <p><strong>Harga Sewa <?= $eventPricing[2]['formatted_price'] ?>;</strong><br>
                                Kapasitas : 20 Orang<br>
                                Lokasi : Jl. Jenderal Sudirman No 1, Pemalang<br>
                                Fasilitas : Panggung Utama, Halaman Parkir Luas, Maja, Kursi, AC, Toilet<br>
                                Status : Tersedia</p>
                            </div>
                            <button class="btn btn-cek" onclick="location.href='rapat.php'">Cek Tanggal</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Panduan Penyewaan Section -->
    <section class="panduan-section">
        <div class="container">
            <h2>PANDUAN PENYEWAAN</h2>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="panduan-step">
                        <div class="panduan-icon">
                            📝
                            <div class="step-number">1</div>
                        </div>
                        <h4>Registrasi akun terlebih dahulu</h4>
                        <p>sebagai umum atau instansi</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="panduan-step">
                        <div class="panduan-icon">
                            ✉️
                            <div class="step-number">2</div>
                        </div>
                        <h4>Verifikasi email anda dengan kode</h4>
                        <p>yang dikirim</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="panduan-step">
                        <div class="panduan-icon">
                            👤
                            <div class="step-number">3</div>
                        </div>
                        <h4>Login ke akun anda setelah verifikasi</h4>
                        <p>berhasil</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="panduan-step">
                        <div class="panduan-icon">
                            📋
                            <div class="step-number">4</div>
                        </div>
                        <h4>Pilih acara dan isi formulir</h4>
                        <p>pemesanan sesuai kebutuhan</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="panduan-step">
                        <div class="panduan-icon">
                            💳
                            <div class="step-number">5</div>
                        </div>
                        <h4>Lakukan pembayaran sesuai</h4>
                        <p>metode yang tersedia dan upload bukti pembayaran</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="panduan-step">
                        <div class="panduan-icon">
                            🧾
                            <div class="step-number">6</div>
                        </div>
                        <h4>Cetak nota sebagai bukti pemesanan</h4>
                        <p>gedung Anda</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-12 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <img src="logoAU.png" alt="PT Aneka Usaha" width="50" class="mr-3">
                        <h5>PT ANEKA USAHA</h5>
                    </div>
                    <p>Menyediakan Penyewaan gedung secara online terpercaya ideal, aman dan nyaman. Memberikan pelayanan penyewaan seperti seminar, pernikahan dan acara-acara lainnya. Terletak di Jl. Jenderal Sudirman No 1, Pemalang, Jawa Tengah 52312</p>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5>Hubungi Kami</h5>
                    <p>📍 Jl. Jenderal Sudirman No 1,<br>Pemalang, Jawa Tengah 52312</p>
                    <p>📞 0285-22198-28</p>
                    <p>✉️ info@anekaperseroda.co.id</p>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5><i class="fas fa-map-marker-alt"></i> LOKASI</h5>
                    <div class="location-card">
                        <div class="location-info">
                            <div class="location-address">
                                <i class="fas fa-building"></i>
                                <div>
                                    <strong>PT Aneka Usaha Gedung</strong>
                                    <p>Jl. Jenderal Sudirman No 1<br>Pemalang, Jawa Tengah 52312</p>
                                </div>
                            </div>
                            <div class="location-action">
                                <a href="https://maps.google.com/?q=Jl.+Jenderal+Sudirman+No+1+Pemalang" 
                                   target="_blank" 
                                   class="btn-maps">
                                    <i class="fas fa-map"></i>
                                    Buka di Google Maps
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Company All rights Reserved</p>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
