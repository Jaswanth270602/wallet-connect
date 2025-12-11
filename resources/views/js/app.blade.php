<script>
    // Global app utilities
    (function() {
        'use strict';
        
    // Utility functions
    window.WalletApp = {
        // Show toast notification
        showToast: function(message, type = 'info', duration = 5000) {
            const container = document.getElementById('toast-container');
            if (!container) return;
            
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            
            const typeLabels = {
                'success': 'Success',
                'error': 'Error',
                'info': 'Info',
                'warning': 'Warning'
            };
            
            const typeIcons = {
                'success': '✓',
                'error': '✕',
                'info': 'ℹ',
                'warning': '⚠'
            };
            
            toast.innerHTML = `
                <div class="toast-header">
                    <strong class="me-auto">${typeIcons[type]} ${typeLabels[type]}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">${message}</div>
            `;
            
            container.appendChild(toast);
            
            // Initialize Bootstrap toast
            const bsToast = new bootstrap.Toast(toast, {
                autohide: true,
                delay: duration
            });
            
            bsToast.show();
            
            // Remove from DOM after hiding
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        },
        
        // Show alert message (legacy support)
        showAlert: function(message, type = 'info') {
            // Also show as toast
            this.showToast(message, type);
            
            const messagesContainer = document.getElementById('transaction-messages');
            if (!messagesContainer) return;
            
            // Remove existing alerts
            messagesContainer.innerHTML = '';
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.setAttribute('role', 'alert');
            alertDiv.textContent = message;
            
            messagesContainer.appendChild(alertDiv);
            
            // Auto-remove after 10 seconds for success/info messages
            if (type === 'success' || type === 'info') {
                setTimeout(() => {
                    alertDiv.remove();
                }, 10000);
            }
        },
            
            // Format address for display
            formatAddress: function(address) {
                if (!address) return '-';
                return `${address.slice(0, 6)}...${address.slice(-4)}`;
            },
            
            // Show/hide elements
            showElement: function(elementId) {
                const element = document.getElementById(elementId);
                if (element) {
                    element.classList.remove('hidden');
                }
            },
            
            hideElement: function(elementId) {
                const element = document.getElementById(elementId);
                if (element) {
                    element.classList.add('hidden');
                }
            },
            
            // Set button loading state
            setButtonLoading: function(buttonId, isLoading) {
                const button = document.getElementById(buttonId);
                if (!button) return;
                
                if (isLoading) {
                    button.disabled = true;
                    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...';
                } else {
                    button.disabled = false;
                }
            }
        };
    })();
</script>

