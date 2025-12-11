# WalletConnect Laravel Application

A modern Laravel application with WalletConnect v2 integration for connecting Web3 wallets and sending transactions.

## Features

- **WalletConnect v2 Integration**: Connect to MetaMask, TrustWallet, Coinbase Wallet, and more
- **Transaction Support**: Send ETH transactions directly from the frontend
- **Modern UI**: Red-to-black gradient background with Bootstrap 5 styling
- **Fully Responsive**: Works seamlessly on desktop and mobile devices
- **No Build Tools**: Uses plain JavaScript loaded via Blade templates

## Requirements

- PHP 8.1 or higher
- Laravel 10.x
- WalletConnect Project ID (get one at [cloud.walletconnect.com](https://cloud.walletconnect.com))

## Installation

1. Clone the repository
2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Set your WalletConnect Project ID in `.env`:
   ```
   WALLETCONNECT_PROJECT_ID=your_project_id_here
   ```

6. Start the Laravel development server:
   ```bash
   php artisan serve
   ```

7. Open your browser and navigate to `http://localhost:8000`

## Project Structure

```
resources/views/
├── layout.blade.php          # Main layout with Bootstrap and styling
├── home.blade.php            # Home page with wallet UI
└── js/
    ├── app.blade.php         # Global JavaScript utilities
    └── walletconnect.blade.php  # WalletConnect integration logic
```

## Usage

1. Click "Verify Wallet" button
2. Scan QR code with your mobile wallet or connect via browser extension
3. Once connected, your wallet address and network will be displayed
4. Use the "Send Transaction" form to send ETH to any address
5. Transaction hash will be displayed upon successful submission

## Technologies Used

- **Laravel**: PHP framework
- **Blade Templates**: Laravel's templating engine
- **Bootstrap 5**: UI framework (via CDN)
- **WalletConnect v2**: Web3 wallet connection protocol
- **Wagmi**: React Hooks for Ethereum (used via vanilla JS)
- **Viem**: TypeScript Ethereum library

## Notes

- All JavaScript is loaded via Blade `@include` directives
- No build tools (Webpack, Vite, Mix) are required
- JavaScript libraries are loaded as ES modules
- The application supports Ethereum mainnet, Sepolia, and Goerli testnets

## License

MIT

