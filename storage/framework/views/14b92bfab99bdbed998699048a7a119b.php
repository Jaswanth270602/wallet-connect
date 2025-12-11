<!-- Removed importmap - using direct imports instead -->
<script>
    // CRITICAL: Always open Web3Modal to show wallet selection (like TronSecure)
    window.connectWallet = async function(event) {
        console.log('✓ connectWallet called - opening wallet selection modal');
        
        // Prevent default behavior
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        // Debug: Check current state
        console.log('🔍 Checking ready state:');
        console.log('  - _walletConnectReady:', window._walletConnectReady);
        console.log('  - _handleConnectWallet:', typeof window._handleConnectWallet);
        console.log('  - _configReady:', window._configReady);
        console.log('  - _configError:', window._configError);
        
        // ALWAYS use Web3Modal to show wallet selection (don't connect directly)
        if (window._handleConnectWallet && window._walletConnectReady) {
            console.log('✓✓✓ READY! Opening Web3Modal wallet selection...');
            try {
                window._handleConnectWallet(event || {});
                return;
            } catch (error) {
                console.error('Error opening wallet selection:', error);
                alert('Error opening wallet selection. Please try again.');
                return;
            }
        }
        
        // If module not ready yet, wait for it
        if (!window._walletConnectReady) {
            console.log('⚠️ NOT READY - Web3Modal initializing, please wait...');
            console.log('⚠️ Current state:', {
                _walletConnectReady: window._walletConnectReady,
                _handleConnectWallet: typeof window._handleConnectWallet,
                _configReady: window._configReady,
                _configError: window._configError
            });
            if (typeof WalletApp !== 'undefined') {
                WalletApp.showToast('Initializing wallet connection...', 'info');
            }
            
            // Wait for module to be ready (max 10 seconds)
            let attempts = 0;
            const maxAttempts = 100;
            const checkReady = setInterval(() => {
                attempts++;
                
                // Debug every 10 attempts
                if (attempts % 10 === 0) {
                    console.log('Waiting for Web3Modal... (' + attempts + '/' + maxAttempts + ')');
                    console.log('  Current state:', {
                        _walletConnectReady: window._walletConnectReady,
                        _handleConnectWallet: typeof window._handleConnectWallet,
                        _configReady: window._configReady,
                        _configError: window._configError
                    });
                }
                
                if (window._walletConnectReady && window._handleConnectWallet) {
                    clearInterval(checkReady);
                    console.log('✓✓✓ Web3Modal ready! Opening wallet selection...');
                    try {
                        window._handleConnectWallet(event || {});
                    } catch (error) {
                        console.error('Error opening modal:', error);
                        alert('Error opening wallet selection: ' + error.message);
                    }
                } else if (attempts >= maxAttempts) {
                    clearInterval(checkReady);
                    console.error('❌ Timeout waiting for Web3Modal');
                    console.error('Final state:', {
                        _walletConnectReady: window._walletConnectReady,
                        _handleConnectWallet: typeof window._handleConnectWallet,
                        _configReady: window._configReady,
                        _configError: window._configError
                    });
                    alert('Wallet connection is still initializing. Please refresh the page and try again.\n\nCheck console for details.');
                }
            }, 100);
            return;
        }
        
        // Final fallback
        alert('Please wait for wallet connection to initialize, then try again.');
    };
    
    console.log('connectWallet function defined (early)');
</script>
<script type="module">
    // CRITICAL: Set handler and ready flag immediately
    window._handleConnectWallet = function(event) {
        console.log('⚠️ Early handler called - module may still be loading');
        console.log('⚠️ This should be replaced by the real handler once module loads');
        // This will be replaced by the real handler once module loads
    };
    window._walletConnectReady = false; // Will be set to true when module loads
    window._configReady = false;
    console.log('🔵 Module script started - ready flag initialized to false');
    
    // Wrap everything in try-catch to ensure handler is always set
    (async () => {
        try {
            console.log('=== STARTING WalletConnect module load ===');
            console.log('Timestamp:', new Date().toISOString());
            console.log('Loading WalletConnect modules...');
            
            // Try loading modules with fallback
            let createWeb3Modal, createConfig, http, getAccount, watchAccount, connect, disconnect;
            let mainnet, sepolia, goerli;
            let walletConnect, injected, coinbaseWallet;
            let createPublicClient;
            
            // Try multiple CDNs with fallback - using verified versions
            // Using @latest to get the most recent stable version
            const cdnUrls = [
                {
                    name: 'esm.sh (latest)',
                    wagmi: 'https://esm.sh/@web3modal/wagmi@latest',
                    core: 'https://esm.sh/@wagmi/core@latest',
                    chains: 'https://esm.sh/@wagmi/core@latest/chains',
                    connectors: 'https://esm.sh/@wagmi/connectors@latest',
                    viem: 'https://esm.sh/viem@latest'
                },
                {
                    name: 'jsdelivr (latest)',
                    wagmi: 'https://cdn.jsdelivr.net/npm/@web3modal/wagmi@latest/+esm',
                    core: 'https://cdn.jsdelivr.net/npm/@wagmi/core@latest/+esm',
                    chains: 'https://cdn.jsdelivr.net/npm/@wagmi/core@latest/chains/+esm',
                    connectors: 'https://cdn.jsdelivr.net/npm/@wagmi/connectors@latest/+esm',
                    viem: 'https://cdn.jsdelivr.net/npm/viem@latest/+esm'
                },
                {
                    name: 'skypack (latest)',
                    wagmi: 'https://cdn.skypack.dev/@web3modal/wagmi',
                    core: 'https://cdn.skypack.dev/@wagmi/core',
                    chains: 'https://cdn.skypack.dev/@wagmi/core/chains',
                    connectors: 'https://cdn.skypack.dev/@wagmi/connectors',
                    viem: 'https://cdn.skypack.dev/viem'
                }
            ];
            
            let loaded = false;
            let lastError = null;
            
            for (const cdn of cdnUrls) {
                try {
                    console.log(`Trying to load from ${cdn.name}...`);
                    
                    // Load modules with timeout
                    const loadWithTimeout = (url, timeout = 10000) => {
                        return Promise.race([
                            import(url),
                            new Promise((_, reject) => 
                                setTimeout(() => reject(new Error(`Timeout loading ${url}`)), timeout)
                            )
                        ]);
                    };
                    
                    const wagmiModal = await loadWithTimeout(cdn.wagmi);
                    const wagmiCore = await loadWithTimeout(cdn.core);
                    const wagmiChains = await loadWithTimeout(cdn.chains);
                    const wagmiConnectors = await loadWithTimeout(cdn.connectors);
                    const viemLib = await loadWithTimeout(cdn.viem);
                    
                    createWeb3Modal = wagmiModal.createWeb3Modal;
                    ({ createConfig, http, getAccount, watchAccount, connect, disconnect } = wagmiCore);
                    ({ mainnet, sepolia, goerli } = wagmiChains);
                    ({ walletConnect, injected, coinbaseWallet } = wagmiConnectors);
                    createPublicClient = viemLib.createPublicClient;
                    
                    console.log(`✓ Modules loaded successfully from ${cdn.name}`);
                    loaded = true;
                    break;
                } catch (error) {
                    console.warn(`✗ Failed to load from ${cdn.name}:`, error.message);
                    lastError = error;
                    continue;
                }
            }
            
            if (!loaded) {
                const errorMsg = `Failed to load WalletConnect modules from all CDNs.\n\n` +
                    `This usually means:\n` +
                    `1. Your internet connection is blocked or slow\n` +
                    `2. Your firewall/antivirus is blocking CDN requests\n` +
                    `3. The CDN services are temporarily down\n\n` +
                    `Last error: ${lastError?.message || 'Unknown error'}\n\n` +
                    `Please check your network connection and try again.`;
                throw new Error(errorMsg);
            }
            
            // Get project ID from environment (passed via Blade)
            const projectId = '<?php echo e($walletconnectProjectId ?? ""); ?>';
            
            // Validate project ID
            if (!projectId || projectId.trim() === '') {
                console.error('⚠️ WALLETCONNECT_PROJECT_ID is not set in .env file');
                console.warn('⚠️ WalletConnect features will be limited. Please set WALLETCONNECT_PROJECT_ID in your .env file.');
                console.warn('⚠️ Get your Project ID from: https://cloud.walletconnect.com');
                // Don't block execution, but show warning
                setTimeout(() => {
                    if (typeof WalletApp !== 'undefined') {
                        WalletApp.showToast('WalletConnect Project ID is missing. Please set WALLETCONNECT_PROJECT_ID in your .env file.', 'error');
                    } else {
                        console.warn('WalletConnect Project ID is missing. Please set WALLETCONNECT_PROJECT_ID in your .env file.');
                    }
                }, 1000);
            } else {
                console.log('✓ WalletConnect Project ID found:', projectId.substring(0, 8) + '...');
                console.log('ℹ️ Note: If you see 403 errors, you may need to whitelist your domain:');
                console.log('   1. Go to https://cloud.walletconnect.com (or dashboard.reown.com)');
                console.log('   2. Select your project');
                console.log('   3. Click the "Domain" tab');
                console.log('   4. Add "127.0.0.1:8000" or "localhost:8000" to the allowlist');
                console.log('   However, MetaMask and other injected wallets will work without this!');
            }
            
            // Configure chains
            const chains = [mainnet, sepolia, goerli];
            
            let config, modal, publicClient;
            
            try {
        // Create connectors - PRIORITIZE injected wallets (MetaMask) which work without WalletConnect Cloud
        const connectors = [];
        
        // Add connectors - prioritize injected wallets (MetaMask, etc.)
        connectors.push(injected({ shimDisconnect: true }));
        console.log('✓ Injected wallet connector added (MetaMask will work)');
        
        // Add WalletConnect connectors to show all wallet options in modal
        if (projectId && projectId.trim() !== '') {
            try {
                // WalletConnect connector - for QR code wallet connections
                connectors.push(walletConnect({ projectId }));
                // Coinbase Wallet connector
                connectors.push(coinbaseWallet({ appName: 'SecureConnect', projectId }));
                console.log('✓ WalletConnect connectors added - all wallet options will be shown');
            } catch (error) {
                console.warn('⚠️ Failed to add WalletConnect connectors:', error.message);
                console.warn('⚠️ Injected wallets (MetaMask) will still work');
            }
        }
        
        // Create wagmi config
        config = createConfig({
            chains,
            connectors,
            transports: {
                [mainnet.id]: http(),
                [sepolia.id]: http(),
                [goerli.id]: http()
            }
        });
        
        console.log('✓✓✓ Config created - setting ready flag IMMEDIATELY');
        // CRITICAL: Mark as ready IMMEDIATELY after config is created
        window._walletConnectReady = true;
        window._configReady = true;
        console.log('✓✓✓ READY FLAG SET - buttons can work now!');
        console.log('✓✓✓ Verification - _walletConnectReady:', window._walletConnectReady);
        console.log('✓✓✓ Verification - _handleConnectWallet:', typeof window._handleConnectWallet);
        
        // Create public client
        publicClient = createPublicClient({
            chain: mainnet,
            transport: http()
        });
        
        // CRITICAL: Create handler IMMEDIATELY after config (before modal creation)
        // This ensures it's available even if modal creation fails
        async function handleConnectImmediate(event) {
            console.log('✓✓✓ Opening wallet selection modal...');
            
            if (!config) {
                alert('Wallet connection is still initializing. Please refresh the page.');
                return;
            }
            
            // Wait for modal if not ready yet
            if (!modal) {
                console.log('Modal not ready, waiting...');
                let attempts = 0;
                while (!modal && attempts < 50) {
                    await new Promise(resolve => setTimeout(resolve, 100));
                    attempts++;
                }
                if (!modal) {
                    alert('Wallet connection modal is not ready. Please refresh the page.');
                    return;
                }
            }
            
            try {
                // Set loading state
                const hasWalletApp = typeof WalletApp !== 'undefined';
                if (hasWalletApp) {
                    WalletApp.setButtonLoading('connect-wallet-btn', true);
                    WalletApp.setButtonLoading('connect-wallet-btn-tab', true);
                }
                
                // Set flags to prevent cleanup script from removing modal
                if (window._setUserOpenedModal) window._setUserOpenedModal(true);
                if (window._userWantsModal) window._userWantsModal(true);
                window._userOpenedModal = true;
                window._lastModalOpenTime = Date.now();
                
                // Open modal - this shows wallet selection
                await modal.open();
                console.log('✓✓✓ Wallet selection modal opened! Select your wallet.');
                
                // Clear loading state
                if (hasWalletApp) {
                    setTimeout(() => {
                        WalletApp.setButtonLoading('connect-wallet-btn', false);
                        WalletApp.setButtonLoading('connect-wallet-btn-tab', false);
                    }, 500);
                }
            } catch (error) {
                console.error('Error opening modal:', error);
                alert('Error opening wallet selection: ' + error.message);
                if (typeof WalletApp !== 'undefined') {
                    WalletApp.setButtonLoading('connect-wallet-btn', false);
                    WalletApp.setButtonLoading('connect-wallet-btn-tab', false);
                }
            }
        }
        
        // CRITICAL: Store handler (ready flag already set above after config creation)
        window._handleConnectWallet = handleConnectImmediate;
        console.log('✓✓✓ WalletConnect handler stored - ready to open modal!');
        
        // Create Web3Modal - DISABLE WalletConnect Cloud to avoid 403/Project not found errors
        // MetaMask and injected wallets work perfectly without WalletConnect Cloud
        if (projectId && projectId.trim() !== '') {
            try {
                modal = createWeb3Modal({
                    wagmiConfig: config,
                    projectId,
                    chains,
                    themeMode: 'light',
                    themeVariables: {
                        '--w3m-accent': '#dc3545'
                    },
                enableEIP6963: true, // Show all detected wallets
                enableCoinbase: true, // Enable Coinbase Wallet
                enableInjected: true, // CRITICAL: Enable MetaMask and other injected wallets
                enableWalletConnect: true // Enable WalletConnect (QR code wallets)
                });
                console.log('✓ Web3Modal created - wallet selection will show all options');
                console.log('✓ Handler already set - ready to open modal!');
                
            } catch (error) {
                console.warn('⚠️ Web3Modal error (non-critical):', error.message);
                console.warn('⚠️ MetaMask will work via direct connection');
            }
        }
            
            // CRITICAL: Ensure modal is closed by default
            // Track if user explicitly opened the modal
            let userOpenedModal = false;
            
            // Subscribe to modal state - only auto-close if user didn't open it
            if (modal && modal.subscribeState) {
                modal.subscribeState((state) => {
                    if (state.open && !userOpenedModal) {
                        console.log('⚠️ Modal opened automatically (not by user), closing it...');
                        setTimeout(() => {
                            try {
                                if (!userOpenedModal && modal && modal.getState && modal.getState().open) {
                                    modal.close();
                                    console.log('✓ Forced auto-opened modal closed');
                                }
                            } catch (e) {
                                console.warn('Could not close modal:', e);
                            }
                        }, 200);
                    } else if (!state.open && userOpenedModal) {
                        // Reset flag when modal closes (user closed it)
                        userOpenedModal = false;
                        console.log('✓ Modal closed, reset user flag');
                    }
                });
            }
            
            // Store flag setter globally so handleConnect can use it
            window._setUserOpenedModal = function(value) {
                userOpenedModal = value;
            };
            
            // Force close immediately after creation (before any user interaction)
            setTimeout(() => {
                try {
                    if (modal && modal.getState && modal.getState().open && !userOpenedModal) {
                        modal.close();
                        console.log('✓ Closed auto-opened modal after initialization');
                    }
                } catch (e) {
                    // Ignore errors
                }
            }, 500);
        } else {
            console.warn('Web3Modal not initialized - Project ID missing');
        }
        
        console.log('WalletConnect configuration initialized');
        
        // Listen for network errors (403, etc.) - WalletConnect domain whitelist issues
        // NOTE: 403 errors don't block injected wallets (MetaMask) - they only affect QR code connections
        window.addEventListener('unhandledrejection', (event) => {
            if (event.reason && event.reason.message && event.reason.message.includes('403')) {
                console.warn('⚠️ WalletConnect Cloud 403 Error (non-blocking)');
                console.warn('This only affects QR code wallet connections. MetaMask and other injected wallets will still work!');
                console.warn('To fix QR code connections, add your domain to WalletConnect Cloud allowlist.');
                // Don't show error toast - it's not blocking functionality
            }
        });
        
        // Also suppress 403 errors in console (they're expected for localhost)
        const originalError = console.error;
        console.error = function(...args) {
            // Don't log WalletConnect 403 errors as errors (they're warnings)
            if (args.some(arg => typeof arg === 'string' && arg.includes('403') && arg.includes('pulse.walletconnect.org'))) {
                console.warn('⚠️ WalletConnect Cloud 403 (expected for localhost - MetaMask still works)');
                return;
            }
            originalError.apply(console, args);
        };
            
        } catch (error) {
            console.error('Error initializing WalletConnect config:', error);
            console.error('Error stack:', error.stack);
            window._configError = error.message;
            // Don't throw - allow handler to be set up even if config fails
            // This way injected wallets can still work
            console.warn('Continuing despite config error - injected wallets may still work');
        }
            
            // Initialize wallet connection state
            let currentAccount = null;
            let currentChain = null;
            
            // DOM elements
            const connectBtn = document.getElementById('connect-wallet-btn') || document.getElementById('connect-wallet-btn-tab');
            const disconnectBtn = document.getElementById('disconnect-wallet-btn');
            const walletAddressEl = document.getElementById('wallet-address');
            const chainInfoEl = document.getElementById('chain-info');
            const transactionForm = document.getElementById('transaction-form');
            const sendTransactionBtn = document.getElementById('send-transaction-btn');
            
            // Store handleConnect globally so it can be called
            let handleConnectFunction = null;
            
            // Wait for DOM and WalletApp to be ready
            function initializeApp() {
        // Check if DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeApp);
            return;
        }
        
        // Wait for WalletApp to be available (with timeout)
        let attempts = 0;
        const maxAttempts = 50; // 5 seconds max wait
        
        function checkWalletApp() {
            if (typeof WalletApp !== 'undefined') {
                // Initialize
                (async () => {
                    try {
                        console.log('Initializing WalletConnect...');
                        await checkConnection();
                        setupEventListeners();
                        setupAccountWatcher();
                        console.log('WalletConnect initialized successfully');
                    } catch (error) {
                        console.error('Error initializing WalletConnect:', error);
                        console.error('Error details:', error.message, error.stack);
                        if (typeof WalletApp !== 'undefined') {
                            WalletApp.showToast('Error initializing wallet connection: ' + error.message, 'error');
                        }
                    } finally {
                        // Ensure ready flag is set even if there's an error
                        // (handler should already be set by this point)
                        if (window._handleConnectWallet && !window._walletConnectReady) {
                            window._walletConnectReady = true;
                            console.log('WalletConnect marked as ready (in finally block)');
                        }
                    }
                })();
            } else {
                attempts++;
                if (attempts < maxAttempts) {
                    setTimeout(checkWalletApp, 100);
                } else {
                    console.warn('WalletApp not found after waiting, initializing anyway...');
                    // Initialize anyway - WalletApp might not be critical for basic functionality
                    (async () => {
                        try {
                            await checkConnection();
                            setupEventListeners();
                            setupAccountWatcher();
                            console.log('WalletConnect initialized (without WalletApp)');
                        } catch (error) {
                            console.error('Error initializing WalletConnect:', error);
                        } finally {
                            // Ensure ready flag is set
                            if (window._handleConnectWallet && !window._walletConnectReady) {
                                window._walletConnectReady = true;
                                console.log('WalletConnect marked as ready (in finally block, no WalletApp)');
                            }
                        }
                    })();
                }
            }
        }
        
                checkWalletApp();
            }
            
            // Start initialization
            console.log('Starting WalletConnect initialization...');
            initializeApp();
            
            // Watch for account changes
            function setupAccountWatcher() {
                if (!config) {
                    console.error('Config not initialized, cannot setup account watcher');
                    return;
                }
                
                try {
                    watchAccount(config, {
                        onChange(account) {
                            if (account.address) {
                                currentAccount = account.address;
                                updateWalletInfo();
                            } else {
                                // Account disconnected
                                currentAccount = null;
                                currentChain = null;
                                if (typeof WalletApp !== 'undefined') {
                                    WalletApp.hideElement('wallet-info-section');
                                    WalletApp.showElement('connection-section');
                                }
                                
                                // Reset status tab
                                const statusAddress = document.getElementById('status-address');
                                const statusNetwork = document.getElementById('status-network');
                                const statusVerification = document.getElementById('status-verification');
                                
                                if (statusAddress) statusAddress.textContent = 'Not Connected';
                                if (statusNetwork) statusNetwork.textContent = '-';
                                if (statusVerification) {
                                    statusVerification.textContent = 'Pending';
                                    statusVerification.className = 'fw-bold text-warning';
                                }
                            }
                        }
                    });
                    console.log('Account watcher setup complete');
                } catch (error) {
                    console.error('Error setting up account watcher:', error);
                }
            }
            
            // Check if wallet is already connected
            async function checkConnection() {
                if (!config) {
                    console.warn('Config not initialized, skipping connection check');
                    return;
                }
                
                try {
                    const account = getAccount(config);
                    if (account.address) {
                        currentAccount = account.address;
                        await updateWalletInfo();
                        console.log('Found existing connection:', account.address);
                    }
                } catch (error) {
                    console.log('No existing connection found:', error);
                }
            }
            
            // Setup event listeners
            function setupEventListeners() {
        console.log('Setting up event listeners...');
        
        // Connect wallet buttons (both hero and tab)
        const connectButtons = [
            document.getElementById('connect-wallet-btn'),
            document.getElementById('connect-wallet-btn-tab')
        ];
        
        console.log('Found connect buttons:', connectButtons);
        
        connectButtons.forEach((btn, index) => {
            if (btn) {
                const btnId = btn.id;
                console.log('Setting up button:', btnId);
                
                // DON'T clone - preserve inline onclick handlers
                // Just ensure button is enabled and add listeners
                btn.disabled = false;
                btn.removeAttribute('disabled');
                btn.style.pointerEvents = 'auto';
                btn.style.cursor = 'pointer';
                btn.style.zIndex = '10000';
                btn.style.position = 'relative';
                
                // Create click handler
                const clickHandler = function(e) {
                    console.log('=== MODULE LISTENER CLICKED ===', btnId);
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (typeof handleConnect === 'function') {
                        handleConnect(e);
                    } else if (window.connectWallet) {
                        window.connectWallet(e);
                    } else if (window._handleConnectWallet) {
                        window._handleConnectWallet(e);
                    }
                    return false;
                };
                
                // Add listeners (inline onclick will also fire)
                btn.addEventListener('click', clickHandler, true);
                btn.addEventListener('click', clickHandler, false);
                btn.addEventListener('mousedown', function(e) {
                    console.log('Mouse down on', btnId);
                });
                
                // DON'T override onclick - let inline handler work
                // The inline onclick in HTML will fire first
                
                console.log('Module listener added to:', btnId, 'Inline onclick preserved:', !!btn.getAttribute('onclick'));
            } else {
                console.warn('Button not found at index:', index);
            }
        });
        
        // Disconnect wallet button
        const disconnectBtnEl = document.getElementById('disconnect-wallet-btn');
        if (disconnectBtnEl) {
            disconnectBtnEl.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                handleDisconnect();
            });
            console.log('Disconnect button listener added');
        }
        
        // Transaction form submission
        const transactionFormEl = document.getElementById('transaction-form');
        if (transactionFormEl) {
            transactionFormEl.addEventListener('submit', handleSendTransaction);
            console.log('Transaction form listener added');
        }
        
        console.log('Event listeners setup complete');
    }
    
    // Handle wallet connection
    async function handleConnect(event) {
        console.log('✓✓✓ handleConnect called - opening wallet selection modal');
        console.log('Config available:', !!config, 'Modal available:', !!modal);
        
        // CRITICAL: Store handler and mark ready IMMEDIATELY when function is called
        // This ensures it's available even if initialization isn't complete
        if (!window._handleConnectWallet || window._handleConnectWallet.toString().includes('Early handler')) {
            window._handleConnectWallet = handleConnect;
            window._walletConnectReady = true;
            console.log('✓✓✓ Handler stored and ready flag set');
        }
        
        try {
            // Prevent default behavior
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            // Get the button that was clicked
            const clickedButton = event?.target || event?.currentTarget;
            const buttonId = clickedButton?.id || 'connect-wallet-btn';
            
            console.log('Button clicked:', buttonId);
            
            // Check if config is available
            if (!config) {
                throw new Error('WalletConnect config not initialized. Please refresh the page.');
            }
            
            // Check if WalletApp is available (optional but preferred)
            const hasWalletApp = typeof WalletApp !== 'undefined';
            
            if (hasWalletApp) {
                // Set loading state for both buttons
                WalletApp.setButtonLoading('connect-wallet-btn', true);
                WalletApp.setButtonLoading('connect-wallet-btn-tab', true);
            }
            
            // Check if already connected
            const account = getAccount(config);
            if (account.isConnected) {
                console.log('Wallet already connected');
                if (hasWalletApp) {
                    WalletApp.showToast('Wallet is already connected.', 'info');
                    WalletApp.setButtonLoading('connect-wallet-btn', false);
                    WalletApp.setButtonLoading('connect-wallet-btn-tab', false);
                } else {
                    alert('Wallet is already connected.');
                }
                return;
            }
            
            console.log('Opening wallet connection...');
            
            // If modal not available, connect directly to MetaMask (bypasses WalletConnect Cloud errors)
            if (!modal) {
                console.log('⚠️ Web3Modal not available - connecting directly to MetaMask');
                try {
                    const injectedConnector = config.connectors.find(c => c.id === 'injected' || c.name?.toLowerCase().includes('meta'));
                    if (injectedConnector) {
                        await connect(config, { connector: injectedConnector });
                        console.log('✓✓✓ Connected to MetaMask directly!');
                        if (hasWalletApp) {
                            WalletApp.showToast('MetaMask connected successfully!', 'success');
                        }
                        WalletApp.setButtonLoading('connect-wallet-btn', false);
                        WalletApp.setButtonLoading('connect-wallet-btn-tab', false);
                        return;
                    } else {
                        throw new Error('No injected wallet found. Please install MetaMask.');
                    }
                } catch (e) {
                    console.error('Direct MetaMask connection failed:', e);
                    if (hasWalletApp) {
                        WalletApp.showToast('Please install MetaMask browser extension', 'error');
                    } else {
                        alert('Please install MetaMask browser extension to connect your wallet.');
                    }
                    WalletApp.setButtonLoading('connect-wallet-btn', false);
                    WalletApp.setButtonLoading('connect-wallet-btn-tab', false);
                    return;
                }
            }
            
            // Check if modal is already open - close it first if needed
            try {
                const modalState = modal.getState();
                if (modalState?.open) {
                    console.log('Modal is already open, closing it first...');
                    await modal.close();
                    await new Promise(resolve => setTimeout(resolve, 300)); // Wait for close animation
                }
            } catch (e) {
                console.warn('Could not check modal state:', e);
            }
            
            // Also check DOM for stuck modal elements
            const stuckModal = document.querySelector('w3m-modal');
            if (stuckModal && stuckModal.shadowRoot) {
                const modalContainer = stuckModal.shadowRoot.querySelector('[data-open="true"]');
                if (modalContainer) {
                    console.log('Found stuck modal in DOM, removing...');
                    stuckModal.remove();
                }
            }
            
            // Validate project ID before opening modal
            const projectId = '<?php echo e($walletconnectProjectId ?? ""); ?>';
            if (!projectId || projectId.trim() === '') {
                const errorMsg = 'WalletConnect Project ID is missing. Please set WALLETCONNECT_PROJECT_ID in your .env file.';
                console.error(errorMsg);
                if (hasWalletApp) {
                    WalletApp.showToast(errorMsg, 'error');
                } else {
                    alert(errorMsg);
                }
                WalletApp.setButtonLoading('connect-wallet-btn', false);
                WalletApp.setButtonLoading('connect-wallet-btn-tab', false);
                return;
            }
            
            // CRITICAL: Set flags BEFORE opening modal to prevent cleanup script from removing it
            if (window._setUserOpenedModal) {
                window._setUserOpenedModal(true);
            }
            if (window._userWantsModal) {
                window._userWantsModal(true);
            }
            window._userOpenedModal = true;
            window._lastModalOpenTime = Date.now();
            console.log('✓ Flags set - cleanup script will not remove this modal');
            
            // Open Web3Modal - this will show the wallet selection UI
            try {
                await modal.open();
                console.log('✓✓✓ modal.open() called successfully');
                
                // Poll for modal element with multiple attempts
                let found = false;
                for (let i = 0; i < 20; i++) {
                    await new Promise(resolve => setTimeout(resolve, 100));
                    const modalElement = document.querySelector('w3m-modal');
                    if (modalElement) {
                        found = true;
                        console.log('✓✓✓ Modal element FOUND in DOM!');
                        // Ensure visibility
                        modalElement.style.display = '';
                        modalElement.style.visibility = '';
                        modalElement.style.opacity = '';
                        modalElement.style.pointerEvents = '';
                        modalElement.style.zIndex = '';
                        
                        // Verify state
                        try {
                            const state = modal.getState();
                            if (state && state.open) {
                                console.log('✓✓✓ Modal is OPEN and VISIBLE - Click MetaMask to connect!');
                                console.log('ℹ️ 403 errors are normal for localhost - MetaMask will work fine');
                                break;
                            }
                        } catch (e) {
                            console.log('✓ Modal element is visible');
                            break;
                        }
                    }
                }
                
                if (!found) {
                    console.error('⚠️ Modal element never appeared - Web3Modal may have an issue');
                    console.log('Trying alternative approach...');
                    // Force create modal element if it doesn't exist
                    if (!document.querySelector('w3m-modal')) {
                        const modalEl = document.createElement('w3m-modal');
                        document.body.appendChild(modalEl);
                        console.log('✓ Created w3m-modal element manually');
                        // Try opening again
                        setTimeout(() => modal.open(), 500);
                    }
                }
            } catch (error) {
                console.error('Error opening Web3Modal:', error);
                let errorMsg = 'Failed to open wallet connection modal. ';
                
                // Check for 403 errors (domain not whitelisted or invalid project ID)
                if (error.message && error.message.includes('403')) {
                    errorMsg += 'This usually means:\n1. Your domain (127.0.0.1:8000) is not whitelisted in WalletConnect Cloud\n2. Your Project ID is invalid\n\nPlease check your WalletConnect Cloud settings at https://cloud.walletconnect.com';
                } else {
                    errorMsg += error.message || 'Unknown error';
                }
                
                if (hasWalletApp) {
                    WalletApp.showToast(errorMsg, 'error');
                } else {
                    alert(errorMsg);
                }
                WalletApp.setButtonLoading('connect-wallet-btn', false);
                WalletApp.setButtonLoading('connect-wallet-btn-tab', false);
                return;
            }
            
            // IMPORTANT: Clear loading state immediately after modal opens
            // The modal handles its own UI, so we don't need to keep buttons in loading state
            // Use setTimeout to ensure DOM updates happen and page remains interactive
            setTimeout(() => {
                if (hasWalletApp) {
                    WalletApp.setButtonLoading('connect-wallet-btn', false);
                    WalletApp.setButtonLoading('connect-wallet-btn-tab', false);
                }
                // Ensure body remains scrollable and interactive
                document.body.style.overflow = '';
                document.body.style.position = '';
                // Remove any blocking classes
                document.body.classList.remove('modal-open', 'loading');
                console.log('Button loading state cleared, page should be interactive');
            }, 100);
            
            // Wait for connection - the watchAccount will handle the update
            // Set a timeout to remove loading state if user closes modal
            let connectionCheckInterval = setInterval(() => {
                const currentAccount = getAccount(config);
                if (currentAccount.isConnected) {
                    clearInterval(connectionCheckInterval);
                    if (hasWalletApp) {
                        WalletApp.showToast('Wallet connected successfully!', 'success');
                    }
                    console.log('Wallet connected!');
                }
            }, 500);
            
            // Clear interval after 60 seconds
            setTimeout(() => {
                clearInterval(connectionCheckInterval);
            }, 60000);
            
            // Also listen for modal close event to ensure buttons are reset
            try {
                // Check if modal has a close event we can listen to
                if (modal && typeof modal.subscribe === 'function') {
                    modal.subscribe((state) => {
                        if (state.open === false && hasWalletApp) {
                            // Modal was closed, ensure buttons are not in loading state
                            WalletApp.setButtonLoading('connect-wallet-btn', false);
                            WalletApp.setButtonLoading('connect-wallet-btn-tab', false);
                            // Ensure page is interactive again
                            document.body.style.overflow = '';
                            document.body.classList.remove('modal-open');
                        }
                    });
                }
            } catch (e) {
                console.log('Could not subscribe to modal events:', e);
            }
            
        } catch (error) {
            console.error('Connection error:', error);
            console.error('Error stack:', error.stack);
            
            if (typeof WalletApp !== 'undefined') {
                WalletApp.showToast('Failed to connect wallet: ' + (error.message || 'Unknown error'), 'error');
                WalletApp.setButtonLoading('connect-wallet-btn', false);
                WalletApp.setButtonLoading('connect-wallet-btn-tab', false);
            } else {
                alert('Failed to connect wallet: ' + (error.message || 'Unknown error') + '. Please check the console for details.');
            }
        }
    }
    
            // Store function globally (do this immediately after definition)
            handleConnectFunction = handleConnect;
            
            // CRITICAL: Store handler and mark ready IMMEDIATELY
            window._handleConnectWallet = handleConnect;
            window._walletConnectReady = true;
            console.log('✓✓✓ WalletConnect READY - handler available NOW');
    
            // Handle wallet disconnection
            async function handleDisconnect() {
        try {
            // Disconnect using wagmi
            await disconnect(config);
            
            // Close modal if open
            await modal.close();
            
            currentAccount = null;
            currentChain = null;
            
            // Reset UI
            WalletApp.hideElement('wallet-info-section');
            WalletApp.showElement('connection-section');
            
            // Reset status tab
            const statusAddress = document.getElementById('status-address');
            const statusNetwork = document.getElementById('status-network');
            const statusVerification = document.getElementById('status-verification');
            
            if (statusAddress) {
                statusAddress.textContent = 'Not Connected';
            }
            if (statusNetwork) {
                statusNetwork.textContent = '-';
            }
            if (statusVerification) {
                statusVerification.textContent = 'Pending';
                statusVerification.className = 'fw-bold text-warning';
            }
            
            WalletApp.showToast('Wallet disconnected successfully.', 'success');
        } catch (error) {
            console.error('Disconnect error:', error);
            WalletApp.showToast('Failed to disconnect wallet.', 'error');
        }
    }
    
            // Mark as ready (this happens after initialization completes)
            // The handler is already stored above, so we just mark ready
            window._walletConnectReady = true;
            console.log('WalletConnect module ready, handler available');
            
            // Update the global function to use the real handler
            window.connectWallet = function(event) {
                console.log('Global connectWallet called (module version)');
                if (handleConnectFunction && typeof handleConnectFunction === 'function') {
                    handleConnectFunction(event || {});
                } else if (typeof handleConnect === 'function') {
                    handleConnect(event || {});
                } else {
                    console.error('handleConnect function not available');
                    alert('Wallet connection not initialized. Please refresh the page.');
                }
            };
            
        } catch (error) {
            // If module fails to load, set up a basic fallback
            console.error('WalletConnect module failed to load:', error);
            console.error('Error message:', error.message);
            console.error('Error stack:', error.stack);
            
            // Set error flag
            window._configError = error.message || 'Module failed to load';
            
            // CRITICAL: Set ready flag even on error so buttons stop polling
            window._walletConnectReady = true;
            window._configReady = false;
            console.log('⚠️ Module failed but ready flag set - fallback handler active');
            
            // Create a basic fallback handler that shows helpful error
            window._handleConnectWallet = function(event) {
                console.error('WalletConnect module not available');
                
                // Check if it's a network issue
                const isNetworkError = error.message.includes('Failed to fetch') || 
                                      error.message.includes('NetworkError') ||
                                      error.message.includes('timeout');
                
                let errorMsg = 'WalletConnect failed to load.\n\n';
                
                if (isNetworkError) {
                    errorMsg += 'NETWORK ERROR DETECTED:\n';
                    errorMsg += 'The CDN modules cannot be loaded. This could be due to:\n\n';
                    errorMsg += '1. No internet connection\n';
                    errorMsg += '2. Firewall/Antivirus blocking CDN requests\n';
                    errorMsg += '3. Corporate network blocking external CDNs\n';
                    errorMsg += '4. VPN or proxy issues\n\n';
                    errorMsg += 'SOLUTIONS:\n';
                    errorMsg += '• Check your internet connection\n';
                    errorMsg += '• Disable VPN/proxy temporarily\n';
                    errorMsg += '• Check firewall/antivirus settings\n';
                    errorMsg += '• Try a different network\n';
                } else {
                    errorMsg += 'Please check:\n';
                    errorMsg += '1. Your WALLETCONNECT_PROJECT_ID in .env file\n';
                    errorMsg += '2. Your internet connection\n';
                    errorMsg += '3. Browser console for errors\n';
                }
                
                errorMsg += '\nError: ' + (window._configError || 'Unknown error');
                alert(errorMsg);
            };
            
            // Mark as ready (even with error) so waiting stops
            window._walletConnectReady = true;
            console.log('WalletConnect marked as ready (with error fallback)');
        }
        
        // Update wallet information display (exposed globally)
        window.updateWalletInfo = async function updateWalletInfo() {
        try {
            const account = getAccount(config);
            if (!account.isConnected || !account.address) {
                return;
            }
            
            currentAccount = account.address;
            
            // Get chain information
            const chainId = account.chainId || mainnet.id;
            const chain = chains.find(c => c.id === chainId) || chains[0];
            currentChain = chain;
            
            // Update UI elements
            if (walletAddressEl) {
                walletAddressEl.textContent = currentAccount;
            }
            
            if (chainInfoEl) {
                chainInfoEl.textContent = `Network: ${chain.name} (Chain ID: ${chain.id})`;
            }
            
            // Update status tab
            const statusAddress = document.getElementById('status-address');
            const statusNetwork = document.getElementById('status-network');
            const statusVerification = document.getElementById('status-verification');
            
            if (statusAddress) {
                statusAddress.textContent = currentAccount;
            }
            if (statusNetwork) {
                statusNetwork.textContent = `${chain.name} (Chain ID: ${chain.id})`;
            }
            if (statusVerification) {
                statusVerification.textContent = 'Verified';
                statusVerification.className = 'fw-bold text-success';
            }
            
            // Show wallet info and transaction sections
            WalletApp.hideElement('connection-section');
            WalletApp.showElement('wallet-info-section');
            
            // Clear any previous transaction messages
            const messagesContainer = document.getElementById('transaction-messages');
            if (messagesContainer) {
                messagesContainer.innerHTML = '';
            }
        } catch (error) {
            console.error('Error updating wallet info:', error);
        }
        }
        
        // Handle send transaction
        async function handleSendTransaction(event) {
        event.preventDefault();
        
        const account = getAccount(config);
        if (!account.isConnected || !account.address) {
            WalletApp.showToast('Please connect your wallet first.', 'warning');
            return;
        }
        
        const recipientAddress = document.getElementById('recipient-address').value.trim();
        const amount = document.getElementById('amount').value;
        
        // Validate inputs
        if (!recipientAddress || !recipientAddress.startsWith('0x') || recipientAddress.length !== 42) {
            WalletApp.showToast('Please enter a valid recipient address.', 'error');
            return;
        }
        
        if (!amount || parseFloat(amount) <= 0) {
            WalletApp.showToast('Please enter a valid amount greater than 0.', 'error');
            return;
        }
        
        try {
            WalletApp.setButtonLoading('send-transaction-btn', true);
            
            // Get the connector from the account
            const connector = account.connector;
            if (!connector) {
                throw new Error('No connector found');
            }
            
            // Get provider from connector
            const provider = await connector.getProvider();
            if (!provider) {
                throw new Error('Provider not available');
            }
            
            // Convert amount to wei (1 ETH = 10^18 wei)
            const amountInWei = BigInt(Math.floor(parseFloat(amount) * 1e18));
            
            // Send transaction
            const txHash = await provider.request({
                method: 'eth_sendTransaction',
                params: [{
                    from: account.address,
                    to: recipientAddress,
                    value: '0x' + amountInWei.toString(16)
                }]
            });
            
            // Display success message
            WalletApp.showToast(
                `Transaction sent successfully! Hash: ${txHash}`,
                'success',
                8000
            );
            
            // Reset form
            transactionForm.reset();
            
            // Optionally, wait for transaction confirmation
            waitForTransactionConfirmation(txHash);
            
        } catch (error) {
            console.error('Transaction error:', error);
            let errorMessage = 'Failed to send transaction. ';
            
            if (error.message) {
                errorMessage += error.message;
            } else if (error.code) {
                errorMessage += `Error code: ${error.code}`;
            }
            
            WalletApp.showToast(errorMessage, 'error', 6000);
        } finally {
            WalletApp.setButtonLoading('send-transaction-btn', false);
        }
        }
        
        // Wait for transaction confirmation
        async function waitForTransactionConfirmation(txHash) {
            try {
            WalletApp.showToast('Waiting for transaction confirmation...', 'info', 10000);
            
            // Poll for transaction receipt
            const receipt = await publicClient.waitForTransactionReceipt({
                hash: txHash,
                timeout: 120000 // 2 minutes timeout
            });
            
            if (receipt.status === 'success') {
                WalletApp.showToast(
                    `Transaction confirmed! Hash: ${txHash}`,
                    'success',
                    8000
                );
            } else {
                WalletApp.showToast(
                    `Transaction failed. Hash: ${txHash}`,
                    'error',
                    6000
                );
            }
        } catch (error) {
            console.error('Error waiting for confirmation:', error);
            // Don't show error here as transaction was already sent
        }
        }
        
    })();
</script>

<?php /**PATH C:\agdp_projects\wallet-connect\resources\views/js/walletconnect.blade.php ENDPATH**/ ?>