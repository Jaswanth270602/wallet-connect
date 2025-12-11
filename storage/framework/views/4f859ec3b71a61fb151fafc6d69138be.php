<script type="module">
    // Import modules directly with full URLs
    const wagmiModal = await import('https://esm.sh/@web3modal/wagmi@latest');
    const wagmiCore = await import('https://esm.sh/@wagmi/core@latest');
    const wagmiChains = await import('https://esm.sh/@wagmi/core@latest/chains');
    const wagmiConnectors = await import('https://esm.sh/@wagmi/connectors@latest');
    const viemLib = await import('https://esm.sh/viem@latest');
    
    const { createWeb3Modal } = wagmiModal;
    const { createConfig, http, getAccount, watchAccount, connect, disconnect } = wagmiCore;
    const { mainnet, sepolia, goerli } = wagmiChains;
    const { walletConnect, injected, coinbaseWallet } = wagmiConnectors;
    const { createPublicClient } = viemLib;
    
    // Get project ID from environment (passed via Blade)
    const projectId = '<?php echo e($walletconnectProjectId ?? ""); ?>';
    
    if (!projectId || projectId.trim() === '') {
        console.error('WALLETCONNECT_PROJECT_ID is not set in .env file');
        if (typeof WalletApp !== 'undefined') {
            WalletApp.showToast('WalletConnect Project ID is missing. Please set WALLETCONNECT_PROJECT_ID in your .env file.', 'error');
        }
    }
    
    // Configure chains
    const chains = [mainnet, sepolia, goerli];
    
    // Create connectors
    const connectors = [
        injected({ shimDisconnect: true }),
        walletConnect({
            projectId,
            metadata: {
                name: "SecureConnect",
                description: "Wallet Verification Made Easy",
                url: window.location.origin,
                icons: ["https://avatars.githubusercontent.com/u/37784886"]
            }
        }),
        coinbaseWallet({ 
            appName: 'SecureConnect', 
            projectId,
            metadata: {
                name: "SecureConnect",
                description: "Wallet Verification Made Easy",
                url: window.location.origin,
                icons: ["https://avatars.githubusercontent.com/u/37784886"]
            }
        })
    ];
    
    // Create wagmi config
    const config = createConfig({
        chains,
        connectors,
        transports: {
            [mainnet.id]: http(),
            [sepolia.id]: http(),
            [goerli.id]: http()
        }
    });
    
    // Create public client
    const publicClient = createPublicClient({
        chain: mainnet,
        transport: http()
    });
    
    // Create Web3Modal
    const modal = createWeb3Modal({
        wagmiConfig: config,
        projectId,
        chains,
        themeMode: 'light',
        themeVariables: {
            '--w3m-accent': '#ff006e'
        }
    });
    
    // Initialize wallet connection state
    let currentAccount = null;
    let currentChain = null;
    
    // DOM elements
    const connectBtn = document.getElementById('connect-wallet-btn');
    const connectBtnTab = document.getElementById('connect-wallet-btn-tab');
    const disconnectBtn = document.getElementById('disconnect-wallet-btn');
    const walletAddressEl = document.getElementById('wallet-address');
    const chainInfoEl = document.getElementById('chain-info');
    const transactionForm = document.getElementById('transaction-form');
    const sendTransactionBtn = document.getElementById('send-transaction-btn');
    
    // Handle wallet connection
    async function handleConnect(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        try {
            // Set loading state
            if (typeof WalletApp !== 'undefined') {
                WalletApp.setButtonLoading('connect-wallet-btn', true);
                WalletApp.setButtonLoading('connect-wallet-btn-tab', true);
            }
            
            // Check if already connected
            const account = getAccount(config);
            if (account.isConnected) {
                if (typeof WalletApp !== 'undefined') {
                    WalletApp.showToast('Wallet is already connected.', 'info');
                    WalletApp.setButtonLoading('connect-wallet-btn', false);
                    WalletApp.setButtonLoading('connect-wallet-btn-tab', false);
                }
                return;
            }
            
            // Open Web3Modal - this shows QR code and wallet options
            await modal.open();
            console.log('Web3Modal opened - select your wallet or scan QR code');
            
            // Clear loading state after modal opens
            setTimeout(() => {
                if (typeof WalletApp !== 'undefined') {
                    WalletApp.setButtonLoading('connect-wallet-btn', false);
                    WalletApp.setButtonLoading('connect-wallet-btn-tab', false);
                }
            }, 500);
            
        } catch (error) {
            console.error('Connection error:', error);
            if (typeof WalletApp !== 'undefined') {
                WalletApp.showToast('Failed to connect wallet. Please try again.', 'error');
                WalletApp.setButtonLoading('connect-wallet-btn', false);
                WalletApp.setButtonLoading('connect-wallet-btn-tab', false);
            }
        }
    }
    
    // Handle wallet disconnection
    async function handleDisconnect() {
        try {
            await disconnect(config);
            await modal.close();
            
            currentAccount = null;
            currentChain = null;
            
            // Reset UI
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
            
            if (typeof WalletApp !== 'undefined') {
                WalletApp.showToast('Wallet disconnected successfully.', 'success');
            }
        } catch (error) {
            console.error('Disconnect error:', error);
            if (typeof WalletApp !== 'undefined') {
                WalletApp.showToast('Failed to disconnect wallet.', 'error');
            }
        }
    }
    
    // Update wallet information display
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
            
            // Show wallet info
            if (typeof WalletApp !== 'undefined') {
                WalletApp.hideElement('connection-section');
                WalletApp.showElement('wallet-info-section');
            }
            
            // Clear any previous transaction messages
            const messagesContainer = document.getElementById('transaction-messages');
            if (messagesContainer) {
                messagesContainer.innerHTML = '';
            }
            
            if (typeof WalletApp !== 'undefined') {
                WalletApp.showToast('Wallet connected successfully!', 'success');
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
            if (typeof WalletApp !== 'undefined') {
                WalletApp.showToast('Please connect your wallet first.', 'warning');
            }
            return;
        }
        
        const recipientAddress = document.getElementById('recipient-address').value.trim();
        const amount = document.getElementById('amount').value;
        
        // Validate inputs
        if (!recipientAddress || !recipientAddress.startsWith('0x') || recipientAddress.length !== 42) {
            if (typeof WalletApp !== 'undefined') {
                WalletApp.showToast('Please enter a valid recipient address.', 'error');
            }
            return;
        }
        
        if (!amount || parseFloat(amount) <= 0) {
            if (typeof WalletApp !== 'undefined') {
                WalletApp.showToast('Please enter a valid amount greater than 0.', 'error');
            }
            return;
        }
        
        try {
            if (typeof WalletApp !== 'undefined') {
                WalletApp.setButtonLoading('send-transaction-btn', true);
            }
            
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
            if (typeof WalletApp !== 'undefined') {
                WalletApp.showToast(
                    `Transaction sent successfully! Hash: ${txHash}`,
                    'success',
                    8000
                );
            }
            
            // Reset form
            if (transactionForm) {
                transactionForm.reset();
            }
            
            // Wait for transaction confirmation
            waitForTransactionConfirmation(txHash);
            
        } catch (error) {
            console.error('Transaction error:', error);
            let errorMessage = 'Failed to send transaction. ';
            
            if (error.message) {
                errorMessage += error.message;
            } else if (error.code) {
                errorMessage += `Error code: ${error.code}`;
            }
            
            if (typeof WalletApp !== 'undefined') {
                WalletApp.showToast(errorMessage, 'error', 6000);
            }
        } finally {
            if (typeof WalletApp !== 'undefined') {
                WalletApp.setButtonLoading('send-transaction-btn', false);
            }
        }
    }
    
    // Wait for transaction confirmation
    async function waitForTransactionConfirmation(txHash) {
        try {
            if (typeof WalletApp !== 'undefined') {
                WalletApp.showToast('Waiting for transaction confirmation...', 'info', 10000);
            }
            
            // Poll for transaction receipt
            const receipt = await publicClient.waitForTransactionReceipt({
                hash: txHash,
                timeout: 120000 // 2 minutes timeout
            });
            
            if (receipt.status === 'success') {
                if (typeof WalletApp !== 'undefined') {
                    WalletApp.showToast(
                        `Transaction confirmed! Hash: ${txHash}`,
                        'success',
                        8000
                    );
                }
            } else {
                if (typeof WalletApp !== 'undefined') {
                    WalletApp.showToast(
                        `Transaction failed. Hash: ${txHash}`,
                        'error',
                        6000
                    );
                }
            }
        } catch (error) {
            console.error('Error waiting for confirmation:', error);
            // Don't show error here as transaction was already sent
        }
    }
    
    // Setup event listeners
    function setupEventListeners() {
        // Connect wallet buttons
        if (connectBtn) {
            connectBtn.addEventListener('click', handleConnect);
        }
        if (connectBtnTab) {
            connectBtnTab.addEventListener('click', handleConnect);
        }
        
        // Disconnect wallet button
        if (disconnectBtn) {
            disconnectBtn.addEventListener('click', handleDisconnect);
        }
        
        // Transaction form submission
        if (transactionForm) {
            transactionForm.addEventListener('submit', handleSendTransaction);
        }
    }
    
    // Check if wallet is already connected
    async function checkConnection() {
        try {
            const account = getAccount(config);
            if (account.isConnected && account.address) {
                currentAccount = account.address;
                await updateWalletInfo();
            }
        } catch (error) {
            console.log('No existing connection found');
        }
    }
    
    // Watch for account changes
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
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', async () => {
        await checkConnection();
        setupEventListeners();
        console.log('WalletConnect initialized successfully');
    });
</script>
<?php /**PATH C:\Users\dell\Desktop\wallet-connect\resources\views/js/walletconnect.blade.php ENDPATH**/ ?>