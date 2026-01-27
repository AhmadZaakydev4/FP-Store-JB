// ADMIN SIDEBAR FIX - HOSTING COMPATIBLE
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin Sidebar Fix loaded');
    
    // Ensure sidebar is visible on desktop
    function ensureSidebarVisible() {
        const sidebar = document.getElementById('adminSidebar');
        const mainContent = document.querySelector('.main-content');
        const navbar = document.querySelector('.admin-navbar');
        
        if (window.innerWidth >= 992) {
            // Desktop: Force sidebar to be visible
            if (sidebar) {
                sidebar.style.left = '0px';
                sidebar.style.transform = 'translateX(0)';
                sidebar.style.display = 'flex';
                sidebar.style.position = 'fixed';
                sidebar.style.top = '0';
                sidebar.style.width = '280px';
                sidebar.style.height = '100vh';
                sidebar.style.zIndex = '1050';
                console.log('Sidebar positioned for desktop');
            }
            
            // Adjust main content
            if (mainContent) {
                mainContent.style.marginLeft = '280px';
                mainContent.style.width = 'calc(100% - 280px)';
                console.log('Main content adjusted for sidebar');
            }
            
            // Adjust navbar
            if (navbar) {
                navbar.style.marginLeft = '280px';
                navbar.style.width = 'calc(100% - 280px)';
                console.log('Navbar adjusted for sidebar');
            }
        } else {
            // Mobile: Hide sidebar by default
            if (sidebar) {
                sidebar.style.left = '-280px';
                sidebar.style.transform = 'translateX(-280px)';
            }
            
            if (mainContent) {
                mainContent.style.marginLeft = '0';
                mainContent.style.width = '100%';
            }
            
            if (navbar) {
                navbar.style.marginLeft = '0';
                navbar.style.width = '100%';
            }
        }
    }
    
    // Run immediately
    ensureSidebarVisible();
    
    // Run on window resize
    window.addEventListener('resize', ensureSidebarVisible);
    
    // Run with delays to ensure everything is loaded
    setTimeout(ensureSidebarVisible, 100);
    setTimeout(ensureSidebarVisible, 500);
    setTimeout(ensureSidebarVisible, 1000);
    
    // Mobile sidebar toggle functionality
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const closeBtn = document.getElementById('sidebarClose');
    
    if (toggleBtn && sidebar && overlay) {
        toggleBtn.addEventListener('click', function() {
            console.log('Sidebar toggle clicked');
            sidebar.classList.add('show');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        });
        
        // Close sidebar
        function closeSidebar() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', closeSidebar);
        }
        
        overlay.addEventListener('click', closeSidebar);
        
        // Close on nav link click (mobile)
        const navLinks = sidebar.querySelectorAll('.sidebar-nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    setTimeout(closeSidebar, 150);
                }
            });
        });
    }
});