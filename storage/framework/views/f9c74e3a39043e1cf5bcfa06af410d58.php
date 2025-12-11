<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>SecureConnect - Wallet Verification Made Easy</title>
    
    <!-- CRITICAL: Force close any modal on page load - it's blocking everything -->
    <script>
        (function() {
            console.log('=== FORCE CLOSING ANY OPEN MODAL ===');
            
            // IMMEDIATELY remove any modal that's blocking clicks
            function forceCloseModal() {
                const modal = document.querySelector('w3m-modal');
                if (modal) {
                    console.log('⚠️ FORCE REMOVING blocking modal...');
                    // Hide it completely
                    modal.style.display = 'none';
                    modal.style.visibility = 'hidden';
                    modal.style.opacity = '0';
                    modal.style.pointerEvents = 'none';
                    modal.style.zIndex = '-9999';
                    // Remove from DOM
                    modal.remove();
                    console.log('✓ Modal removed - buttons should be clickable now');
                }
            }
            
            // Run immediately
            forceCloseModal();
            
            // Run after DOM loads
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    forceCloseModal();
                    setTimeout(forceCloseModal, 500);
                });
            } else {
                forceCloseModal();
                setTimeout(forceCloseModal, 500);
            }
            
            // Watch for new modals and only allow them if user clicked button
            let userWantsModal = false;
            let lastUserClickTime = 0;
            
            window._userWantsModal = function(value) {
                userWantsModal = value;
                if (value) {
                    lastUserClickTime = Date.now();
                    console.log('✓ User wants modal open - allowing it');
                }
            };
            
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeName === 'W3M-MODAL' || (node.querySelector && node.querySelector('w3m-modal'))) {
                            // Check if user clicked within last 10 seconds (increased window)
                            const timeSinceClick = Date.now() - lastUserClickTime;
                            if (userWantsModal || timeSinceClick < 10000) {
                                console.log('✓ Modal opened by user - KEEPING IT OPEN');
                                // Ensure it's visible and not removed
                                setTimeout(() => {
                                    const modal = document.querySelector('w3m-modal');
                                    if (modal) {
                                        modal.style.display = '';
                                        modal.style.visibility = '';
                                        modal.style.opacity = '';
                                        modal.style.pointerEvents = '';
                                        modal.style.zIndex = '';
                                        console.log('✓ Modal visibility ensured');
                                    }
                                }, 200);
                            } else {
                                console.log('⚠️ Modal appeared without user click - removing');
                                setTimeout(forceCloseModal, 100);
                            }
                        }
                    });
                });
            });
            
            if (document.body) {
                observer.observe(document.body, { childList: true, subtree: true });
            } else {
                document.addEventListener('DOMContentLoaded', function() {
                    observer.observe(document.body, { childList: true, subtree: true });
                });
            }
            
            // Track button clicks (no alerts - just logging)
            document.addEventListener('click', function(e) {
                if (e.target && e.target.id && e.target.id.includes('connect-wallet')) {
                    console.log('✓ User clicked Verify Wallet button');
                    if (window._userWantsModal) {
                        window._userWantsModal(true);
                    }
                }
            }, true);
            
            // Check body pointer-events
            setTimeout(function() {
                const body = document.body;
                if (body) {
                    const style = window.getComputedStyle(body);
                    console.log('Body pointer-events:', style.pointerEvents);
                    if (style.pointerEvents === 'none') {
                        console.error('⚠️ BODY HAS pointer-events: none!');
                        body.style.setProperty('pointer-events', 'auto', 'important');
                    }
                }
            }, 100);
        })();
    </script>
    
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
        
        /* CRITICAL: Ensure buttons are always clickable */
        #connect-wallet-btn,
        #connect-wallet-btn-tab {
            pointer-events: auto !important;
            cursor: pointer !important;
            z-index: 99999 !important;
            position: relative !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        
        /* Ensure Web3Modal doesn't block page interactions */
        body.modal-open {
            overflow: hidden;
        }
        
        /* Allow scrolling on modal container - but don't block buttons */
        w3m-modal {
            pointer-events: auto;
        }
        
        /* CRITICAL: Ensure buttons are always above modal */
        #connect-wallet-btn,
        #connect-wallet-btn-tab,
        #test-click-btn,
        #test-click-tab-btn {
            z-index: 999999 !important;
            position: relative !important;
            pointer-events: auto !important;
        }
        
        /* If modal overlay exists, don't let it block buttons */
        w3m-modal::part(overlay),
        w3m-modal > * {
            pointer-events: none;
        }
        
        w3m-modal > w3m-container,
        w3m-modal > div {
            pointer-events: auto;
        }
        
        /* Make sure nothing overlays the buttons */
        .hero-section,
        .verification-card {
            position: relative;
            z-index: 1;
        }
        
        .hero-section button,
        .verification-card button {
            position: relative;
            z-index: 1000 !important;
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
            /* Ensure buttons are always clickable */
            pointer-events: auto !important;
            cursor: pointer !important;
            z-index: 10;
        }
        
        /* Ensure connect wallet buttons are always clickable */
        #connect-wallet-btn,
        #connect-wallet-btn-tab {
            pointer-events: auto !important;
            cursor: pointer !important;
            z-index: 1000 !important;
            position: relative !important;
        }
        
        #connect-wallet-btn:disabled,
        #connect-wallet-btn-tab:disabled {
            pointer-events: none;
            opacity: 0.6;
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
    
    <!-- Web3Modal Component - Required for modal to render -->
    <w3m-modal></w3m-modal>
    
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
    
    <!-- CRITICAL: Test clicks BEFORE anything else loads -->
    <script>
        console.log('=== EARLY CLICK TEST SCRIPT ===');
        
        // Test if clicks work at all - run immediately
        document.addEventListener('click', function(e) {
            console.log('EARLY CLICK DETECTED:', e.target, e.target.tagName, e.target.id);
            // Don't prevent default - let clicks work
        }, true);
        
        // Check for overlays blocking clicks
        function checkForOverlays() {
            const allElements = document.querySelectorAll('*');
            const overlays = [];
            allElements.forEach(el => {
                const style = window.getComputedStyle(el);
                if (style.position === 'fixed' || style.position === 'absolute') {
                    const zIndex = parseInt(style.zIndex) || 0;
                    if (zIndex > 1000) {
                        const rect = el.getBoundingClientRect();
                        if (rect.width > window.innerWidth * 0.5 && rect.height > window.innerHeight * 0.5) {
                            overlays.push({
                                element: el,
                                zIndex: zIndex,
                                tag: el.tagName,
                                id: el.id,
                                class: el.className,
                                pointerEvents: style.pointerEvents
                            });
                        }
                    }
                }
            });
            if (overlays.length > 0) {
                console.warn('⚠️ POTENTIAL OVERLAYS FOUND:', overlays);
                overlays.forEach(overlay => {
                    if (overlay.pointerEvents !== 'none') {
                        console.warn('Overlay might be blocking clicks:', overlay);
                        overlay.element.style.pointerEvents = 'none';
                    }
                });
            }
        }
        
        // Run check immediately and after DOM loads
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', checkForOverlays);
        } else {
            checkForOverlays();
        }
        setTimeout(checkForOverlays, 1000);
    </script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- Include JavaScript files -->
    <?php echo $__env->make('js.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\agdp_projects\wallet-connect\resources\views/layout.blade.php ENDPATH**/ ?>