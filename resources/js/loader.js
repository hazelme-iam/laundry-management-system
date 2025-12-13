// Page Loader Utilities

// Show/hide page loader
function showPageLoader() {
    const loader = document.getElementById('page-loader');
    if (loader) {
        loader.classList.remove('hidden');
    }
}

function hidePageLoader() {
    const loader = document.getElementById('page-loader');
    if (loader) {
        loader.classList.add('hidden');
    }
}

// Button with loader
function showButtonLoader(buttonId) {
    const button = document.getElementById(buttonId);
    if (!button) return;
    
    const btnText = button.querySelector('[data-loader-text]');
    const btnSpinner = button.querySelector('[data-loader-spinner]');
    
    button.disabled = true;
    if (btnText) btnText.textContent = 'Processing...';
    if (btnSpinner) btnSpinner.classList.remove('hidden');
}

function hideButtonLoader(buttonId) {
    const button = document.getElementById(buttonId);
    if (!button) return;
    
    const btnText = button.querySelector('[data-loader-text]');
    const btnSpinner = button.querySelector('[data-loader-spinner]');
    const originalText = button.getAttribute('data-original-text') || 'Submit';
    
    button.disabled = false;
    if (btnText) btnText.textContent = originalText;
    if (btnSpinner) btnSpinner.classList.add('hidden');
}

// Show loader on form submission
function submitFormWithLoader(formId, buttonId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    showPageLoader();
    if (buttonId) showButtonLoader(buttonId);
    
    // Submit form after a brief delay
    setTimeout(() => {
        form.submit();
    }, 300);
}

// Automatic page loader on page load
document.addEventListener('DOMContentLoaded', function() {
    // Show loader initially
    showPageLoader();
    
    // Hide loader when page is fully loaded
    window.addEventListener('load', function() {
        setTimeout(hidePageLoader, 500);
    });
    
    // Hide loader after minimum 500ms
    setTimeout(hidePageLoader, 500);
    
    // Real-time notification polling for order status updates
    // Only poll if user is authenticated (check for csrf token)
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) return;
    
    // Poll for new notifications every 30 seconds
    setInterval(function() {
        fetch('/notifications/check-new', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin',
        })
        .then(response => {
            if (!response.ok) {
                console.log('Notification check returned status:', response.status);
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.hasNewNotifications) {
                // Show a subtle toast notification
                showNotificationToast(data.latestNotification);
                // Reload the notification bell to show updated count
                location.reload();
            }
        })
        .catch(error => {
            // Silently fail - don't spam console
            console.debug('Notification polling error:', error.message);
        });
    }, 30000); // Check every 30 seconds
});

// Show loader on link click
document.addEventListener('click', function(e) {
    const link = e.target.closest('a[data-loader="true"]');
    if (link && !link.hasAttribute('onclick')) {
        showPageLoader();
    }
});

// Show loader on form submission
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (form.hasAttribute('data-loader')) {
        showPageLoader();
    }
});

// Hide loader if navigation is prevented
window.addEventListener('beforeunload', function() {
    // Keep loader visible during navigation
});

// Expose helpers globally for inline Blade onclick handlers
window.showPageLoader = showPageLoader;
window.hidePageLoader = hidePageLoader;
window.showButtonLoader = showButtonLoader;
window.hideButtonLoader = hideButtonLoader;
window.submitFormWithLoader = submitFormWithLoader;
