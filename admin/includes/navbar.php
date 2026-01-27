<?php
// Admin Navbar Component
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<!-- Admin Header Navbar -->
<nav class="navbar navbar-expand-lg admin-navbar sticky-top">
    <div class="container-fluid">
        <!-- Mobile Sidebar Toggle -->
        <button class="sidebar-toggle d-lg-none me-2" id="sidebarToggle" title="Menu Navigasi">
            <i class="fas fa-bars"></i>
            <span class="d-none d-sm-inline ms-1">Menu</span>
        </button>
        
        <h5 class="navbar-brand mb-0 fw-bold">
            <?php
            switch($current_page) {
                case 'index': echo 'Dashboard'; break;
                case 'products': echo 'Kelola Produk'; break;
                case 'categories': echo 'Kelola Kategori'; break;
                case 'settings': echo 'Pengaturan'; break;
                default: echo 'Admin Panel'; break;
            }
            ?>
        </h5>
        
        <div class="d-flex align-items-center ms-auto">
            <!-- Session Timer -->
            <div class="me-3 d-none d-md-block">
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    <span id="sessionTimer">Sesi: 1:00:00</span>
                </small>
            </div>
            
            <button class="admin-theme-toggle me-3" title="Toggle Dark Mode">
                <i class="fas fa-moon"></i>
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user me-2"></i>
                    <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="settings.php">
                        <i class="fas fa-cog me-2"></i>Pengaturan
                    </a></li>
                    <li><a class="dropdown-item" href="../index.html" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>Lihat Website
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-success" href="#" onclick="extendSession(); return false;">
                        <i class="fas fa-refresh me-2"></i>Perpanjang Sesi
                    </a></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>