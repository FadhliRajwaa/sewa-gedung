/* Universal Admin Styles for Mobile Responsive */
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
        min-height: 100vh;
        color: #333;
    }

    .container {
        display: flex;
        min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
        width: 280px;
        background: rgba(139, 69, 19, 0.98);
        backdrop-filter: blur(10px);
        color: white;
        padding: 0;
        position: fixed;
        height: 100vh;
        overflow-y: auto;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        transform: translateX(0);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .sidebar.mobile-hidden {
        transform: translateX(-100%);
    }

    .sidebar-header {
        padding: 25px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        text-align: center;
        background: rgba(255, 255, 255, 0.05);
    }

    .logo {
        width: 65px;
        height: 65px;
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 16px;
        margin: 0 auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.25);
        border: 2px solid rgba(255, 255, 255, 0.1);
    }

    .logo img {
        width: 50px;
        height: 50px;
        object-fit: contain;
    }

    .sidebar h2 {
        font-size: 15px;
        font-weight: 700;
        margin: 0;
        line-height: 1.3;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        letter-spacing: 0.5px;
    }

    .nav-menu {
        list-style: none;
        padding: 20px 0;
        margin: 0;
    }

    .nav-item {
        margin: 4px 15px;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 12px;
        font-size: 15px;
        font-weight: 500;
        position: relative;
        overflow: hidden;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 0;
        height: 100%;
        background: linear-gradient(90deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.05) 100%);
        transition: width 0.3s ease;
        z-index: -1;
    }

    .nav-link:hover::before, .nav-link.active::before {
        width: 100%;
    }

    .nav-link:hover, .nav-link.active {
        background: rgba(255, 255, 255, 0.12);
        color: white;
        text-decoration: none;
        transform: translateX(8px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .nav-link i {
        margin-right: 15px;
        width: 20px;
        text-align: center;
        font-size: 17px;
        opacity: 0.9;
    }

    /* Main Content */
    .main-content {
        flex: 1;
        margin-left: 280px;
        padding: 20px;
        transition: margin-left 0.3s ease;
    }
    
    /* Mobile Menu Toggle */
    .mobile-menu-toggle {
        display: none;
        position: fixed;
        top: 20px;
        left: 20px;
        z-index: 1001;
        background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
        color: white;
        border: none;
        padding: 14px;
        border-radius: 12px;
        font-size: 20px;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3);
        transition: all 0.3s ease;
        border: 2px solid rgba(255, 255, 255, 0.1);
    }
    
    .mobile-menu-toggle:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(139, 69, 19, 0.4);
    }
    
    .mobile-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 999;
        backdrop-filter: blur(3px);
    }

    /* Content Headers */
    .page-header {
        background: white;
        padding: 20px 25px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
    }

    .page-header h1 {
        color: #8B4513;
        font-size: 24px;
        font-weight: 700;
        margin: 0;
    }

    /* Cards */
    .card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    /* Tables */
    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table th, table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #f0f0f0;
    }

    table th {
        background: #f8f9fa;
        color: #8B4513;
        font-weight: 600;
    }

    /* Buttons */
    .btn {
        background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        margin: 0 5px;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(139, 69, 19, 0.3);
        color: white;
        text-decoration: none;
    }

    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }

    .btn-danger {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    }

    .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        color: #333;
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        color: #8B4513;
        font-weight: 600;
        font-size: 14px;
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: #8B4513;
        background: white;
        box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
    }

    /* Alert Messages */
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .alert.success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert.error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert.info {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    /* Badges */
    .badge {
        display: inline-block;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-warning {
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        color: #333;
    }

    .badge-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }

    .badge-danger {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        color: white;
    }

    .badge-info {
        background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
        color: white;
    }

    /* Text utilities */
    .text-muted {
        color: #6c757d !important;
        font-style: italic;
    }

    .text-center {
        text-align: center !important;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .main-content {
            padding: 15px;
        }
        
        .page-header h1 {
            font-size: 20px;
        }
        
        .card {
            padding: 20px;
        }
    }

    @media (max-width: 768px) {
        .mobile-menu-toggle {
            display: block;
        }
        
        .sidebar {
            transform: translateX(-100%);
            width: 300px;
            box-shadow: 8px 0 25px rgba(0, 0, 0, 0.2);
        }
        
        .sidebar.mobile-show {
            transform: translateX(0);
        }
        
        .mobile-overlay.show {
            display: block;
        }
        
        .main-content {
            margin-left: 0;
            padding: 80px 20px 20px 20px;
        }
        
        .page-header {
            padding: 20px 25px;
            margin-bottom: 20px;
            border-radius: 16px;
        }
        
        .page-header h1 {
            font-size: 20px;
        }
        
        .card {
            padding: 20px;
            border-radius: 16px;
        }
        
        /* Table responsive */
        .table-container {
            overflow-x: auto;
        }
        
        table th, table td {
            padding: 12px 10px;
            font-size: 13px;
        }
        
        /* Button responsive */
        .btn {
            padding: 10px 20px;
            font-size: 13px;
            margin: 2px;
        }
        
        /* Form responsive */
        .form-input, .form-select, .form-textarea {
            padding: 14px 16px;
            font-size: 15px;
            border-radius: 12px;
        }
        
        /* Sidebar mobile adjustments */
        .sidebar-header {
            padding: 30px 25px;
        }
        
        .logo {
            width: 70px;
            height: 70px;
            margin-bottom: 15px;
        }
        
        .sidebar h2 {
            font-size: 16px;
        }
        
        .nav-item {
            margin: 6px 20px;
        }
        
        .nav-link {
            padding: 18px 24px;
            font-size: 16px;
            border-radius: 14px;
        }
        
        .nav-link i {
            margin-right: 18px;
            font-size: 18px;
        }
    }
    
    @media (max-width: 480px) {
        .main-content {
            padding: 70px 15px 15px 15px;
        }
        
        .sidebar {
            width: 280px;
        }
        
        .card {
            padding: 16px;
            border-radius: 14px;
        }
        
        .page-header {
            padding: 16px 20px;
            border-radius: 14px;
        }
        
        .page-header h1 {
            font-size: 18px;
        }
        
        .mobile-menu-toggle {
            top: 15px;
            left: 15px;
            padding: 12px;
            font-size: 18px;
        }
        
        /* Table extra small screens */
        table th, table td {
            padding: 10px 8px;
            font-size: 12px;
        }
        
        .btn {
            padding: 8px 16px;
            font-size: 12px;
        }
        
        .form-input, .form-select, .form-textarea {
            padding: 12px 14px;
            font-size: 14px;
            border-radius: 10px;
        }
        
        /* Sidebar mobile small screen adjustments */
        .sidebar-header {
            padding: 25px 20px;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            margin-bottom: 12px;
        }
        
        .sidebar h2 {
            font-size: 14px;
        }
        
        .nav-item {
            margin: 4px 15px;
        }
        
        .nav-link {
            padding: 16px 20px;
            font-size: 15px;
            border-radius: 12px;
        }
        
        .nav-link i {
            margin-right: 15px;
            font-size: 16px;
        }
    }
</style>

<script>
    // Mobile Menu Functions
    function toggleMobileMenu() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.mobile-overlay');
        
        sidebar.classList.toggle('mobile-show');
        overlay.classList.toggle('show');
    }
    
    function closeMobileMenu() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.mobile-overlay');
        
        sidebar.classList.remove('mobile-show');
        overlay.classList.remove('show');
    }
    
    // Close mobile menu when clicking on nav links
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', closeMobileMenu);
        });
    });
</script>
