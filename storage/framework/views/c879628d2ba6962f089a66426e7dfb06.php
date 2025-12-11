<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>SecureConnect - Wallet Verification Made Easy</title>
    
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #ff006e 0%, #8338ec 25%, #000000 50%, #dc3545 75%, #ff006e 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #fff;
            overflow-x: hidden;
            position: relative;
            /* CRITICAL: Ensure body doesn't block clicks */
            pointer-events: auto !important;
        }
        
        /* Check for any global click blockers */
        * {
            /* Don't block pointer events globally */
        }
        
        /* Ensure nothing blocks buttons */
        button, a, input, select, textarea {
            pointer-events: auto !important;
        }
        
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Navbar */
        .navbar {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(20px);
            padding: 1.2rem 0;
            border-bottom: 2px solid rgba(255, 0, 110, 0.3);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }
        
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ff006e 0%, #8338ec 50%, #dc3545 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }
        
        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            margin: 0 0.5rem;
            transition: color 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover {
            color: #fff !important;
        }
        
        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 0 80px;
        }
        
        .hero-content h1 {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #ff006e 0%, #8338ec 50%, #dc3545 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 40px rgba(255, 0, 110, 0.3);
            letter-spacing: -1px;
        }
        
        .hero-content p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        /* Sections */
        .section {
            padding: 80px 0;
        }
        
        .section-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 3rem;
            text-align: center;
            background: linear-gradient(135deg, #ff006e 0%, #8338ec 50%, #dc3545 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
        
        .card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 60px rgba(255, 0, 110, 0.3);
            border-color: rgba(255, 0, 110, 0.5);
            background: rgba(255, 255, 255, 0.12);
        }
        
        .card h3 {
            color: #fff;
            margin-bottom: 1rem;
        }
        
        .card p {
            color: rgba(255, 255, 255, 0.8);
        }
        
        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #ff006e 0%, #8338ec 50%, #dc3545 100%);
            background-size: 200% 200%;
            animation: gradientShift 3s ease infinite;
            border: none;
            padding: 14px 35px;
            font-weight: 700;
            border-radius: 15px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 8px 25px rgba(255, 0, 110, 0.4), 0 0 20px rgba(131, 56, 236, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            cursor: pointer !important;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 40px rgba(255, 0, 110, 0.6), 0 0 30px rgba(131, 56, 236, 0.5);
        }
        
        .btn-primary:active {
            transform: translateY(-1px) scale(1.02);
        }
        
        .btn-outline-light {
            border: 2px solid rgba(255, 255, 255, 0.5);
            color: #fff;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #fff;
            color: #fff;
        }
        
        /* Verification Form */
        .verification-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 30px;
            padding: 50px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1);
            color: #212529;
            min-height: 500px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 0, 110, 0.2);
            transition: all 0.4s ease;
        }
        
        .verification-card:hover {
            box-shadow: 0 40px 100px rgba(255, 0, 110, 0.3), 0 0 0 1px rgba(255, 0, 110, 0.3);
            transform: translateY(-5px);
        }
        
        .nav-tabs {
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 2rem;
        }
        
        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        .nav-tabs .nav-link:hover {
            border-bottom-color: #dc3545;
            color: #dc3545;
        }
        
        .nav-tabs .nav-link.active {
            color: #ff006e;
            border-bottom-color: #ff006e;
            background: transparent;
            font-weight: 600;
        }
        
        .nav-tabs .nav-link:hover {
            border-bottom-color: #8338ec;
            color: #8338ec;
        }
        
        .form-label {
            font-weight: 600;
            color: #212529;
            margin-bottom: 8px;
        }
        
        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #ff006e;
            box-shadow: 0 0 0 0.2rem rgba(255, 0, 110, 0.25), 0 0 20px rgba(131, 56, 236, 0.2);
        }
        
        .wallet-info {
            background: linear-gradient(135deg, rgba(255, 0, 110, 0.1) 0%, rgba(131, 56, 236, 0.1) 50%, rgba(220, 53, 69, 0.1) 100%);
            border-radius: 15px;
            padding: 25px;
            margin-top: 20px;
            border-left: 5px solid;
            border-image: linear-gradient(135deg, #ff006e 0%, #8338ec 50%, #dc3545 100%) 1;
            backdrop-filter: blur(10px);
        }
        
        .wallet-address {
            font-family: 'Courier New', monospace;
            background: white;
            padding: 10px;
            border-radius: 5px;
            word-break: break-all;
            font-size: 0.9rem;
            color: #495057;
            margin-bottom: 10px;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-top: 20px;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .hidden {
            display: none !important;
        }
        
        /* Stats */
        .stat-card {
            text-align: center;
            padding: 2rem;
        }
        
        .stat-number {
            font-size: 3.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #ff006e 0%, #8338ec 50%, #dc3545 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 30px rgba(255, 0, 110, 0.5);
        }
        
        .stat-label {
            color: rgba(255, 255, 255, 0.8);
            margin-top: 0.5rem;
        }
        
        /* Footer */
        .footer {
            background: rgba(0, 0, 0, 0.5);
            padding: 3rem 0;
            margin-top: 80px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 9999;
        }
        
        .toast {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            border-left: 5px solid;
            min-width: 300px;
            animation: slideInRight 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .toast-success {
            border-left-color: #28a745;
        }
        
        .toast-error {
            border-left-color: #dc3545;
        }
        
        .toast-info {
            border-left-color: #17a2b8;
        }
        
        .toast-warning {
            border-left-color: #ffc107;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        
        .toast.hiding {
            animation: slideOutRight 0.3s ease forwards;
        }
        
        .toast-header {
            background: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            font-weight: 600;
        }
        
        .toast-body {
            padding: 1rem;
            color: #212529;
        }
        
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2rem;
            }
            
            .verification-card {
                padding: 30px;
                min-height: 400px;
            }
            
            .toast-container {
                right: 10px;
                left: 10px;
            }
            
            .toast {
                min-width: auto;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#home">SecureConnect</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 30 30\'%3E%3Cpath stroke=\'rgba%28255, 255, 255, 0.85%29\' stroke-linecap=\'round\' stroke-miterlimit=\'10\' stroke-width=\'2\' d=\'M4 7h22M4 15h22M4 23h22\'/%3E%3C/svg%3E');"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#verification-status">Verification Status</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#wallet-security">Wallet Security Check</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <?php echo $__env->yieldContent('content'); ?>
    
    <!-- Toast Container -->
    <div class="toast-container" id="toast-container"></div>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>SecureConnect</h5>
                    <p class="text-muted">Making wallet verification secure and seamless</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="text-muted">&copy; 2025 SecureConnect. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
    
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- Include JavaScript files -->
    <?php echo $__env->make('js.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\dell\Desktop\wallet-connect\resources\views/layout.blade.php ENDPATH**/ ?>