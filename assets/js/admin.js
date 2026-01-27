// Admin Theme Management - Independent from main website
class AdminThemeManager {
    constructor() {
        this.theme = localStorage.getItem('admin-theme') || 'light';
        this.init();
    }
    
    init() {
        this.applyTheme();
        this.bindEvents();
    }
    
    applyTheme() {
        document.documentElement.setAttribute('data-theme', this.theme);
        localStorage.setItem('admin-theme', this.theme);
        
        // Update toggle button icon
        const toggleBtn = document.querySelector('.admin-theme-toggle i');
        if (toggleBtn) {
            toggleBtn.className = this.theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
    }
    
    bindEvents() {
        // Listen for theme toggle clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.admin-theme-toggle')) {
                this.toggleTheme();
            }
        });
    }
    
    toggleTheme() {
        this.theme = this.theme === 'light' ? 'dark' : 'light';
        this.applyTheme();
        
        // Animate toggle button
        const toggleBtn = document.querySelector('.admin-theme-toggle');
        if (toggleBtn) {
            toggleBtn.style.transform = 'scale(0.9)';
            setTimeout(() => {
                toggleBtn.style.transform = 'scale(1)';
            }, 150);
        }
        
        // Show notification
        this.showNotification(
            `Mode ${this.theme === 'dark' ? 'Malam' : 'Siang'} diaktifkan`, 
            'success'
        );
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} position-fixed`;
        notification.style.cssText = 'top: 80px; right: 20px; z-index: 9999; min-width: 250px; border-radius: 12px;';
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close ms-2" onclick="this.parentElement.remove()"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }
    
    getTheme() {
        return this.theme;
    }
    
    setTheme(theme) {
        this.theme = theme;
        this.applyTheme();
    }
}

// Initialize admin theme manager
const adminThemeManager = new AdminThemeManager();

// Global functions for admin pages
window.toggleAdminTheme = () => adminThemeManager.toggleTheme();
window.showAdminNotification = (message, type) => adminThemeManager.showNotification(message, type);
window.adminThemeManager = adminThemeManager;

// Admin utility functions
function confirmDelete(itemName, deleteUrl) {
    if (confirm(`Apakah Anda yakin ingin menghapus "${itemName}"?`)) {
        window.location.href = deleteUrl;
    }
}

function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Export for global use
window.confirmDelete = confirmDelete;
window.previewImage = previewImage;

// Session timeout management
let sessionTimeout = 3600; // 1 hour in seconds
let warningTime = 300; // Show warning 5 minutes before timeout
let sessionTimer;
let warningTimer;
let countdownInterval;

// Initialize session management
function initSessionManagement() {
    // Reset timers
    resetSessionTimer();
    
    // Add activity listeners
    document.addEventListener('click', resetSessionTimer);
    document.addEventListener('keypress', resetSessionTimer);
    document.addEventListener('scroll', resetSessionTimer);
    document.addEventListener('mousemove', throttle(resetSessionTimer, 30000)); // Throttle mouse movement to 30 seconds
}

// Throttle function to limit function calls
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// Reset session timer
function resetSessionTimer() {
    clearTimeout(sessionTimer);
    clearTimeout(warningTimer);
    clearInterval(countdownInterval);
    
    // Hide warning modal if shown
    const warningModal = document.getElementById('sessionWarningModal');
    if (warningModal) {
        const modal = bootstrap.Modal.getInstance(warningModal);
        if (modal) modal.hide();
    }
    
    // Set warning timer (show warning 5 minutes before timeout)
    warningTimer = setTimeout(showSessionWarning, (sessionTimeout - warningTime) * 1000);
    
    // Set logout timer
    sessionTimer = setTimeout(forceLogout, sessionTimeout * 1000);
}

// Show session warning modal
function showSessionWarning() {
    let timeLeft = warningTime;
    
    // Create modal if not exists
    if (!document.getElementById('sessionWarningModal')) {
        createSessionWarningModal();
    }
    
    const modal = new bootstrap.Modal(document.getElementById('sessionWarningModal'), {
        backdrop: 'static',
        keyboard: false
    });
    
    modal.show();
    
    // Update countdown
    updateCountdown(timeLeft);
    countdownInterval = setInterval(() => {
        timeLeft--;
        updateCountdown(timeLeft);
        
        if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            modal.hide();
            forceLogout();
        }
    }, 1000);
}

// Update countdown display
function updateCountdown(seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    const timeString = `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    
    const countdownElement = document.getElementById('sessionCountdown');
    if (countdownElement) {
        countdownElement.textContent = timeString;
    }
}

// Create session warning modal
function createSessionWarningModal() {
    const modalHTML = `
        <div class="modal fade" id="sessionWarningModal" tabindex="-1" aria-labelledby="sessionWarningModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title" id="sessionWarningModalLabel">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Peringatan Sesi
                        </h5>
                    </div>
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                        </div>
                        <h6>Sesi Anda akan berakhir dalam:</h6>
                        <div class="display-4 text-danger fw-bold mb-3" id="sessionCountdown">5:00</div>
                        <p class="text-muted">Klik "Perpanjang Sesi" untuk melanjutkan atau Anda akan otomatis logout.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-success" onclick="extendSession()">
                            <i class="fas fa-refresh me-2"></i>
                            Perpanjang Sesi
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="forceLogout()">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Logout Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// Extend session
function extendSession() {
    // Make AJAX call to refresh session
    fetch('includes/refresh_session.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resetSessionTimer();
            showToast('Sesi berhasil diperpanjang!', 'success');
        } else {
            forceLogout();
        }
    })
    .catch(error => {
        console.error('Error extending session:', error);
        forceLogout();
    });
}

// Force logout
function forceLogout() {
    showToast('Sesi berakhir. Mengalihkan ke halaman login...', 'warning');
    setTimeout(() => {
        window.location.href = 'logout.php';
    }, 2000);
}

// Show toast notification
function showToast(message, type = 'info') {
    // Create toast container if not exists
    if (!document.getElementById('toastContainer')) {
        const toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now();
    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    document.getElementById('toastContainer').insertAdjacentHTML('beforeend', toastHTML);
    
    const toast = new bootstrap.Toast(document.getElementById(toastId));
    toast.show();
    
    // Remove toast element after it's hidden
    document.getElementById(toastId).addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

// Initialize session management when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initSessionManagement();
});

// Export session functions for global use
window.extendSession = extendSession;
window.forceLogout = forceLogout;
window.showToast = showToast;

// Session timer display in navbar
let navbarTimerInterval;

function updateNavbarTimer() {
    const timerElement = document.getElementById('sessionTimer');
    if (!timerElement) return;
    
    // Calculate remaining time (assuming session starts fresh)
    const now = Date.now();
    const sessionStart = sessionStorage.getItem('sessionStart') || now;
    const elapsed = Math.floor((now - sessionStart) / 1000);
    const remaining = Math.max(0, sessionTimeout - elapsed);
    
    if (remaining <= 0) {
        timerElement.innerHTML = '<span class="text-danger">Sesi Berakhir</span>';
        return;
    }
    
    const hours = Math.floor(remaining / 3600);
    const minutes = Math.floor((remaining % 3600) / 60);
    const seconds = remaining % 60;
    
    const timeString = `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    // Change color based on remaining time
    let colorClass = 'text-success';
    if (remaining <= 300) colorClass = 'text-danger'; // Last 5 minutes
    else if (remaining <= 900) colorClass = 'text-warning'; // Last 15 minutes
    
    timerElement.innerHTML = `<span class="${colorClass}">Sesi: ${timeString}</span>`;
}

function startNavbarTimer() {
    // Set session start time
    if (!sessionStorage.getItem('sessionStart')) {
        sessionStorage.setItem('sessionStart', Date.now());
    }
    
    // Update immediately
    updateNavbarTimer();
    
    // Update every second
    navbarTimerInterval = setInterval(updateNavbarTimer, 1000);
}

function resetNavbarTimer() {
    sessionStorage.setItem('sessionStart', Date.now());
    updateNavbarTimer();
}

// Override the existing resetSessionTimer to also reset navbar timer
const originalResetSessionTimer = resetSessionTimer;
resetSessionTimer = function() {
    originalResetSessionTimer();
    resetNavbarTimer();
};

// Start navbar timer when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    startNavbarTimer();
});
// Mobile Sidebar Management
class MobileSidebar {
    constructor() {
        this.sidebar = document.getElementById('adminSidebar');
        this.overlay = document.getElementById('sidebarOverlay');
        this.toggleBtn = document.getElementById('sidebarToggle');
        this.closeBtn = document.getElementById('sidebarClose');
        
        this.init();
    }
    
    init() {
        if (!this.sidebar || !this.overlay || !this.toggleBtn) {
            return; // Elements not found, probably not on a page with sidebar
        }
        
        this.bindEvents();
    }
    
    bindEvents() {
        // Toggle sidebar
        this.toggleBtn.addEventListener('click', () => this.show());
        
        // Close sidebar
        if (this.closeBtn) {
            this.closeBtn.addEventListener('click', () => this.hide());
        }
        
        // Close on overlay click
        this.overlay.addEventListener('click', () => this.hide());
        
        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isVisible()) {
                this.hide();
            }
        });
        
        // Close sidebar when clicking on nav links (mobile)
        const navLinks = this.sidebar.querySelectorAll('.sidebar-nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                // Small delay to allow navigation
                setTimeout(() => this.hide(), 100);
            });
        });
        
        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992 && this.isVisible()) {
                this.hide();
            }
        });
    }
    
    show() {
        this.sidebar.classList.add('show');
        this.overlay.classList.add('show');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }
    
    hide() {
        this.sidebar.classList.remove('show');
        this.overlay.classList.remove('show');
        document.body.style.overflow = ''; // Restore scrolling
    }
    
    isVisible() {
        return this.sidebar.classList.contains('show');
    }
    
    toggle() {
        if (this.isVisible()) {
            this.hide();
        } else {
            this.show();
        }
    }
}

// Initialize mobile sidebar when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.mobileSidebar = new MobileSidebar();
});

// Export for global use
window.MobileSidebar = MobileSidebar;
// Debug sidebar functionality
console.log('Admin.js loaded');

// Force initialize sidebar on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing sidebar...');
    
    // Check if elements exist
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    
    console.log('Sidebar elements:', {
        sidebar: !!sidebar,
        overlay: !!overlay,
        toggleBtn: !!toggleBtn
    });
    
    if (toggleBtn) {
        console.log('Adding click listener to toggle button');
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Toggle button clicked!');
            
            if (sidebar && overlay) {
                const isVisible = sidebar.classList.contains('show');
                console.log('Current sidebar state:', isVisible ? 'visible' : 'hidden');
                
                if (isVisible) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                    document.body.style.overflow = '';
                    console.log('Sidebar hidden');
                } else {
                    sidebar.classList.add('show');
                    overlay.classList.add('show');
                    document.body.style.overflow = 'hidden';
                    console.log('Sidebar shown');
                }
            }
        });
    }
    
    // Add overlay click listener
    if (overlay) {
        overlay.addEventListener('click', function() {
            console.log('Overlay clicked, hiding sidebar');
            if (sidebar) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
    }
    
    // Add close button listener
    const closeBtn = document.getElementById('sidebarClose');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            console.log('Close button clicked');
            if (sidebar && overlay) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
    }
});

// Alternative initialization method
window.addEventListener('load', function() {
    console.log('Window loaded, double-checking sidebar...');
    
    const toggleBtn = document.getElementById('sidebarToggle');
    if (toggleBtn && !toggleBtn.hasAttribute('data-initialized')) {
        console.log('Initializing sidebar toggle as backup...');
        toggleBtn.setAttribute('data-initialized', 'true');
        
        toggleBtn.onclick = function(e) {
            e.preventDefault();
            console.log('Backup toggle clicked!');
            
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebar && overlay) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
                
                if (sidebar.classList.contains('show')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }
        };
    }
});
// EMERGENCY FIX: Force sidebar visibility on desktop
document.addEventListener('DOMContentLoaded', function() {
    function ensureSidebarVisible() {
        const sidebar = document.getElementById('adminSidebar');
        if (sidebar && window.innerWidth >= 992) {
            // Force sidebar to be visible on desktop
            sidebar.style.display = 'flex';
            sidebar.style.position = 'fixed';
            sidebar.style.top = '0';
            sidebar.style.left = '0';
            sidebar.style.width = '280px';
            sidebar.style.height = '100vh';
            sidebar.style.zIndex = '1000';
            sidebar.style.background = 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)';
            sidebar.style.opacity = '1';
            sidebar.style.visibility = 'visible';
            sidebar.style.transform = 'translateX(0)';
            
            console.log('Sidebar forced to be visible on desktop');
        }
    }
    
    // Run immediately
    ensureSidebarVisible();
    
    // Run on window resize
    window.addEventListener('resize', ensureSidebarVisible);
    
    // Run after a short delay to ensure all CSS is loaded
    setTimeout(ensureSidebarVisible, 100);
});
// FORCE MAIN CONTENT POSITIONING - ZOOM RESPONSIVE
document.addEventListener('DOMContentLoaded', function() {
    function fixMainContentPosition() {
        if (window.innerWidth >= 992) {
            const mainContent = document.querySelector('.main-content');
            const navbar = document.querySelector('.admin-navbar');
            const sidebar = document.getElementById('adminSidebar');
            
            if (mainContent) {
                mainContent.style.marginLeft = '280px';
                mainContent.style.width = 'calc(100% - 280px)';
                mainContent.style.padding = '2rem';
                mainContent.style.minHeight = '100vh';
                mainContent.style.position = 'relative';
                mainContent.style.zIndex = '1';
                mainContent.style.boxSizing = 'border-box';
                mainContent.style.maxWidth = 'none';
                mainContent.style.left = '0';
                mainContent.style.transform = 'none';
                
                console.log('Main content positioning fixed for zoom level');
            }
            
            if (navbar) {
                navbar.style.marginLeft = '280px';
                navbar.style.width = 'calc(100% - 280px)';
                navbar.style.position = 'sticky';
                navbar.style.top = '0';
                navbar.style.zIndex = '999';
                navbar.style.boxSizing = 'border-box';
                navbar.style.maxWidth = 'none';
                navbar.style.left = '0';
                navbar.style.transform = 'none';
                
                console.log('Navbar positioning fixed for zoom level');
            }
            
            if (sidebar) {
                sidebar.style.position = 'fixed';
                sidebar.style.top = '0';
                sidebar.style.left = '0';
                sidebar.style.width = '280px';
                sidebar.style.height = '100vh';
                sidebar.style.zIndex = '1000';
                sidebar.style.transform = 'translateX(0)';
                sidebar.style.minWidth = '280px';
                sidebar.style.maxWidth = '280px';
                
                console.log('Sidebar positioning fixed for zoom level');
            }
        }
    }
    
    // Run immediately
    fixMainContentPosition();
    
    // Run on window resize (includes zoom changes)
    window.addEventListener('resize', fixMainContentPosition);
    
    // Run on zoom change (additional detection)
    let lastZoom = window.devicePixelRatio;
    setInterval(function() {
        if (window.devicePixelRatio !== lastZoom) {
            lastZoom = window.devicePixelRatio;
            console.log('Zoom level changed, fixing layout...');
            setTimeout(fixMainContentPosition, 100);
        }
    }, 500);
    
    // Run after delays to ensure all elements are loaded
    setTimeout(fixMainContentPosition, 200);
    setTimeout(fixMainContentPosition, 1000);
});