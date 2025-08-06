<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penyewaan - Mobile Fixed Demo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            color: #2d3748;
            font-size: 2.5em;
            margin: 0;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .fix-section {
            background: #f0f9ff;
            padding: 25px;
            border-radius: 15px;
            border: 2px solid #0ea5e9;
            margin-bottom: 30px;
        }
        .fix-section h3 {
            color: #0c4a6e;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .before-after {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .before, .after {
            padding: 20px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
        }
        .before {
            background: #fee2e2;
            border-color: #ef4444;
        }
        .after {
            background: #d1fae5;
            border-color: #10b981;
        }
        .before h4, .after h4 {
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .before h4 {
            color: #991b1b;
        }
        .after h4 {
            color: #065f46;
        }
        .code-snippet {
            background: #1e293b;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            margin: 10px 0;
            overflow-x: auto;
        }
        .mobile-demo {
            background: #fef3c7;
            padding: 25px;
            border-radius: 15px;
            border: 2px solid #f59e0b;
            margin: 20px 0;
        }
        .mobile-demo h3 {
            color: #92400e;
            margin-top: 0;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .feature-item {
            background: white;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .feature-item h4 {
            color: #667eea;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-test {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 1.1em;
            margin: 10px;
        }
        .btn-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        .demo-video {
            background: #f3f4f6;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin: 30px 0;
            border: 2px dashed #9ca3af;
        }
        .list-check {
            list-style: none;
            padding: 0;
        }
        .list-check li {
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .list-check .check {
            color: #10b981;
        }
        .list-check .cross {
            color: #ef4444;
        }
        @media (max-width: 768px) {
            .before-after {
                grid-template-columns: 1fr;
            }
            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-mobile-alt"></i> LAPORAN PENYEWAAN - MOBILE FIXED!</h1>
            <p>Hamburger menu dan tampilan mobile sudah diperbaiki dengan sempurna</p>
        </div>

        <div class="fix-section">
            <h3><i class="fas fa-tools"></i> Masalah Yang Sudah Diperbaiki</h3>
            
            <div class="before-after">
                <div class="before">
                    <h4><i class="fas fa-times-circle"></i> SEBELUM (Bermasalah)</h4>
                    <ul class="list-check">
                        <li><i class="fas fa-times cross"></i> Hamburger menu tidak bisa diklik</li>
                        <li><i class="fas fa-times cross"></i> Sidebar tidak muncul di mobile</li>
                        <li><i class="fas fa-times cross"></i> Table tidak responsive</li>
                        <li><i class="fas fa-times cross"></i> Scroll horizontal sulit</li>
                        <li><i class="fas fa-times cross"></i> Button terlalu kecil di mobile</li>
                        <li><i class="fas fa-times cross"></i> Text terpotong</li>
                    </ul>
                </div>

                <div class="after">
                    <h4><i class="fas fa-check-circle"></i> SESUDAH (Perfect)</h4>
                    <ul class="list-check">
                        <li><i class="fas fa-check check"></i> Hamburger menu responsif dan smooth</li>
                        <li><i class="fas fa-check check"></i> Sidebar slide animation</li>
                        <li><i class="fas fa-check check"></i> Table scroll dengan custom scrollbar</li>
                        <li><i class="fas fa-check check"></i> Touch-friendly scrolling</li>
                        <li><i class="fas fa-check check"></i> Button ukuran optimal mobile</li>
                        <li><i class="fas fa-check check"></i> Typography responsive</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mobile-demo">
            <h3><i class="fas fa-mobile-alt"></i> Demo Perbaikan Mobile</h3>
            <p><strong>Test di berbagai ukuran layar:</strong></p>
            <ol>
                <li><strong>Desktop (>1024px):</strong> Layout normal dengan sidebar tetap</li>
                <li><strong>Tablet (768px-1024px):</strong> Responsive grid dan button</li>
                <li><strong>Mobile (≤768px):</strong> Hamburger menu dan full responsive</li>
                <li><strong>Small Mobile (≤480px):</strong> Optimized untuk layar kecil</li>
            </ol>
        </div>

        <h3><i class="fas fa-code"></i> Technical Fixes:</h3>

        <div class="features-grid">
            <div class="feature-item">
                <h4><i class="fas fa-bars"></i> Hamburger Menu Fix</h4>
                <div class="code-snippet">
// JavaScript event handling diperbaiki
function toggleMobileMenu() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.querySelector('.mobile-overlay');
    
    mobileMenuOpen = !mobileMenuOpen;
    
    if (mobileMenuOpen) {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        toggle.innerHTML = '&lt;i class="fas fa-times"&gt;&lt;/i&gt;';
    }
}</div>
                <p>✅ Event listener yang proper</p>
                <p>✅ State management yang benar</p>
                <p>✅ Icon toggle animation</p>
            </div>

            <div class="feature-item">
                <h4><i class="fas fa-mobile-alt"></i> Mobile CSS</h4>
                <div class="code-snippet">
@media (max-width: 768px) {
    .mobile-menu-toggle {
        display: flex !important;
        position: fixed;
        top: 20px;
        left: 20px;
        z-index: 1001;
    }
    
    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .sidebar.active {
        transform: translateX(0);
    }
}</div>
                <p>✅ Smooth slide animation</p>
                <p>✅ Proper z-index layering</p>
                <p>✅ Mobile-first approach</p>
            </div>

            <div class="feature-item">
                <h4><i class="fas fa-table"></i> Table Responsive</h4>
                <div class="code-snippet">
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 4px;
}</div>
                <p>✅ Touch scrolling support</p>
                <p>✅ Custom scrollbar styling</p>
                <p>✅ Sticky table headers</p>
            </div>

            <div class="feature-item">
                <h4><i class="fas fa-expand-arrows-alt"></i> Responsive Grid</h4>
                <div class="code-snippet">
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
}</div>
                <p>✅ Adaptive grid layout</p>
                <p>✅ Optimal spacing</p>
                <p>✅ Single column on mobile</p>
            </div>

            <div class="feature-item">
                <h4><i class="fas fa-mouse-pointer"></i> Touch Optimization</h4>
                <div class="code-snippet">
.btn {
    padding: 12px 24px;
    min-height: 44px; /* iOS touch target */
    touch-action: manipulation;
}

@media (max-width: 768px) {
    .btn {
        font-size: 12px;
        padding: 10px 16px;
        flex: 1;
        min-width: 120px;
    }
}</div>
                <p>✅ Touch-friendly button sizes</p>
                <p>✅ iOS guidelines compliance</p>
                <p>✅ Optimal tap targets</p>
            </div>

            <div class="feature-item">
                <h4><i class="fas fa-eye"></i> UX Improvements</h4>
                <div class="code-snippet">
// Auto-close menu on navigation
navLinks.forEach(link => {
    link.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
            setTimeout(closeMobileMenu, 150);
        }
    });
});

// Escape key support
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && mobileMenuOpen) {
        closeMobileMenu();
    }
});</div>
                <p>✅ Smart menu behavior</p>
                <p>✅ Keyboard accessibility</p>
                <p>✅ Intuitive interactions</p>
            </div>
        </div>

        <div class="demo-video">
            <h3><i class="fas fa-play-circle"></i> Test Responsive Design</h3>
            <p>🔧 <strong>Cara Test:</strong></p>
            <ol style="text-align: left; max-width: 500px; margin: 0 auto;">
                <li>Buka halaman laporan penyewaan</li>
                <li>Resize browser window atau gunakan developer tools</li>
                <li>Test hamburger menu di ukuran mobile</li>
                <li>Scroll horizontal table di mobile</li>
                <li>Check semua button dapat diklik dengan mudah</li>
            </ol>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="laporan_penyewaan.php" class="btn-test" target="_blank">
                <i class="fas fa-external-link-alt"></i> Test Laporan Penyewaan Mobile
            </a>
            <a href="laporan_penyewaan.php" class="btn-test" target="_blank" onclick="setTimeout(() => { alert('Tips: Resize browser window atau gunakan Device Mode di Developer Tools untuk test responsive!'); }, 1000);">
                <i class="fas fa-mobile-alt"></i> Test Responsive Mode
            </a>
        </div>

        <div style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; border-radius: 15px; padding: 30px; text-align: center; margin-top: 30px;">
            <h3>🎉 LAPORAN PENYEWAAN MOBILE PERFECT!</h3>
            <p><strong>✅ Hamburger Menu:</strong> Smooth slide animation dengan toggle icon</p>
            <p><strong>✅ Responsive Design:</strong> Perfect di desktop, tablet, dan mobile</p>
            <p><strong>✅ Touch Optimization:</strong> Button size dan scroll yang optimal</p>
            <p><strong>✅ UX Improvements:</strong> Auto-close menu dan keyboard support</p>
            
            <div style="margin-top: 20px; padding: 20px; background: rgba(255,255,255,0.1); border-radius: 10px;">
                <p style="font-style: italic; margin: 0;">
                    <i class="fas fa-star"></i> 
                    Sekarang laporan penyewaan dapat digunakan dengan nyaman di semua device!
                    <i class="fas fa-star"></i>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
