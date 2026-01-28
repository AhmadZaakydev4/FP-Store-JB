<?php
// Admin Sidebar Component
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<!-- Mobile Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Admin Sidebar -->
<div class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <div class="d-flex align-items-center">
            <img src="../assets/images/logo.png" alt="FP Store" height="32" class="me-2">
            <h5 class="mb-0 fw-bold text-white">FP Store</h5>
        </div>
        <button class="sidebar-close d-lg-none" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="sidebar-body">
        <nav class="sidebar-nav">
            <a class="sidebar-nav-link <?php echo $current_page === 'index' ? 'active' : ''; ?>" href="index.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a class="sidebar-nav-link <?php echo $current_page === 'products' ? 'active' : ''; ?>" href="products.php">
                <i class="fas fa-box"></i>
                <span>Kelola Produk</span>
            </a>
            <a class="sidebar-nav-link <?php echo $current_page === 'categories' ? 'active' : ''; ?>" href="categories.php">
                <i class="fas fa-tags"></i>
                <span>Kelola Kategori</span>
            </a>
            <a class="sidebar-nav-link <?php echo $current_page === 'settings' ? 'active' : ''; ?>" href="settings.php">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
            
            <hr class="sidebar-divider">
            
            <a class="sidebar-nav-link" href="../index.php" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span>Lihat Website</span>
            </a>
            <a class="sidebar-nav-link text-danger" href="logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>
    
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="d-flex align-items-center">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <div class="user-name"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
        </div>
    </div>
</div>