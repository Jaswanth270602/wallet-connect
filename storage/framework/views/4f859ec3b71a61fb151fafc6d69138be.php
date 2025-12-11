<script type="importmap">
{
    "imports": {
        "@web3modal/wagmi": "https://esm.sh/@web3modal/wagmi@2.7.4",
        "@wagmi/core": "https://esm.sh/@wagmi/core@2.6.8",
        "@wagmi/connectors": "https://esm.sh/@wagmi/connectors@2.1.4",
        "viem": "https://esm.sh/viem@2.21.45"
    }
}
</script>
<script type="module">
    import { createWeb3Modal } from '@web3modal/wagmi';
    import { createConfig, http } from '@wagmi/core';
    import { mainnet, sepolia, goerli } from '@wagmi/core/chains';
    import { walletConnect, injected, coinbaseWallet } from '@wagmi/connectors';
    import { createPublicClient } from 'viem';
    
    // Get project ID from environment (passed via Blade)
    const projectId = '<?php echo e($walletconnectProjectId ?? ""); ?>';
    
        if (!projectId) {
            console.error('WALLETCONNECT_PROJECT_ID is not set in .env file');
            WalletApp.showToast('WalletConnect Project ID is missing. Please set WALLETCONNECT_PROJECT_ID in your .env file.', 'error');
        }
    
    // Configure chains
    const chains = [mainnet, sepolia, goerli];
    
    // Create connectors
    const connectors = [
        walletConnect({ projectId }),
        injected({ shimDisconnect: true }),
        coinbaseWallet({ appName: 'WalletConnect App', projectId })
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
            '--w3m-accent': '#dc3545'
        }
    });
    
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
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', async () => {
        await checkConnection();
        setupEventListeners();
    });
    
    // Check if wallet is already connected
    async function checkConnection() {
        try {
            const state = config.state;
            const connection = state.connections.get(state.current);
            if (connection && connection.accounts && connection.accounts.length > 0) {
                currentAccount = connection.accounts[0];
                await updateWalletInfo();
            }
        } catch (error) {
            console.log('No existing connection found');
        }
    }
    
    // Setup event listeners
    function setupEventListeners() {
        // Connect wallet buttons (both hero and tab)
        const connectButtons = [
            document.getElementById('connect-wallet-btn'),
            document.getElementById('connect-wallet-btn-tab')
        ];
        connectButtons.forEach(btn => {
            if (btn) {
                btn.addEventListener('click', handleConnect);
            }
        });
        
        // Disconnect wallet button
        if (disconnectBtn) {
            disconnectBtn.addEventListener('click', handleDisconnect);
        }
        
        // Transaction form submission
        if (transactionForm) {
            transactionForm.addEventListener('submit', handleSendTransaction);
        }
    }
    
    // Handle wallet connection
    async function handleConnect() {
        try {
            WalletApp.setButtonLoading('connect-wallet-btn', true);
            
            // Open Web3Modal
            await modal.open();
            
            // Wait for connection with timeout
            let connectionEstablished = false;
            const unsubscribe = config.subscribe((state) => {
                const connection = state.connections.get(state.current);
                const account = connection?.accounts?.[0];
                if (account && !connectionEstablished) {
                    connectionEstablished = true;
                    currentAccount = account;
                    updateWalletInfo();
                    unsubscribe();
                    WalletApp.setButtonLoading('connect-wallet-btn', false);
                }
            });
            
            // Timeout after 60 seconds
            setTimeout(() => {
                if (!connectionEstablished) {
                    unsubscribe();
                    WalletApp.setButtonLoading('connect-wallet-btn', false);
                }
            }, 60000);
            
        } catch (error) {
            console.error('Connection error:', error);
            WalletApp.showToast('Failed to connect wallet. Please try again.', 'error');
            WalletApp.setButtonLoading('connect-wallet-btn', false);
        }
    }
    
    // Handle wallet disconnection
    async function handleDisconnect() {
        try {
            await modal.close();
            
            // Disconnect all connectors
            const state = config.state;
            const connection = state.connections.get(state.current);
            if (connection) {
                const connector = connection.connector;
                if (connector && typeof connector.disconnect === 'function') {
                    await connector.disconnect();
                }
            }
            
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
    
    // Update wallet information display (exposed globally)
    window.updateWalletInfo = async function updateWalletInfo() {
        if (!currentAccount) return;
        
        try {
            // Get chain information from current connection
            const state = config.state;
            const connection = state.connections.get(state.current);
            const chainId = connection?.chainId || state.chainId || mainnet.id;
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
        
        if (!currentAccount) {
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
            
            // Get the active connection and connector
            const state = config.state;
            const connection = state.connections.get(state.current);
            if (!connection) {
                throw new Error('No active connection found');
            }
            
            const connector = connection.connector;
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
                    from: currentAccount,
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
    
    // Listen for account changes
    config.subscribe((state) => {
        const account = state.connections.get(state.current)?.accounts[0];
        if (account && account !== currentAccount) {
            currentAccount = account;
            updateWalletInfo();
        } else if (!account && currentAccount) {
            // Account disconnected
            currentAccount = null;
            WalletApp.hideElement('wallet-info-section');
            WalletApp.hideElement('transaction-section');
            WalletApp.showElement('connection-section');
        }
    });
</script>

<?php /**PATH C:\Users\dell\Desktop\wallet-connect\resources\views/js/walletconnect.blade.php ENDPATH**/ ?>