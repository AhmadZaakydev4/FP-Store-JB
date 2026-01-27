<?php
require_once 'includes/session_check.php';
require_once '../config/database.php';

// Ambil data statistik
try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Total produk
    $query = "SELECT COUNT(*) as total_products FROM products";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Total kategori
    $query = "SELECT COUNT(*) as total_categories FROM categories";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $categories_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_categories'] = $categories_stats['total_categories'];
    
    // Produk aktif
    $query = "SELECT COUNT(*) as active_products FROM products WHERE is_active = 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $active_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['active_products'] = $active_stats['active_products'];
    
} catch(Exception $e) {
    $stats = ['total_products' => 0, 'total_categories' => 0, 'active_products' => 0];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FP Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <link href="../assets/css/admin-sidebar-fix.css" rel="stylesheet">
</head>
<body class="admin-body">
    <!-- Include Sidebar -->
    <?php include 'includes/sidebar.php'; ?>
    
    <!-- Include Navbar -->
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Selamat Datang</h2>
            <div class="text-muted">
                <i class="fas fa-calendar me-2"></i>
                <?php echo date('d F Y'); ?>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card admin-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-box fa-3x text-primary mb-3"></i>
                        <h3 class="fw-bold"><?php echo $stats['total_products']; ?></h3>
                        <p class="text-muted mb-0">Total Produk</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card admin-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h3 class="fw-bold"><?php echo $stats['active_products']; ?></h3>
                        <p class="text-muted mb-0">Produk Aktif</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card admin-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-tags fa-3x text-warning mb-3"></i>
                        <h3 class="fw-bold"><?php echo $stats['total_categories']; ?></h3>
                        <p class="text-muted mb-0">Kategori</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card admin-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar fa-3x text-info mb-3"></i>
                        <h3 class="fw-bold"><?php echo date('d'); ?></h3>
                        <p class="text-muted mb-0"><?php echo date('M Y'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card admin-card border-0 shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0 fw-bold">Aksi Cepat</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <a href="products.php?action=add" class="btn admin-btn admin-btn-primary w-100 py-3">
                                    <i class="fas fa-plus me-2"></i>
                                    Tambah Produk Baru
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="categories.php" class="btn btn-secondary w-100 py-3">
                                    <i class="fas fa-tags me-2"></i>
                                    Kelola Kategori
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="products.php" class="btn btn-outline-primary w-100 py-3">
                                    <i class="fas fa-list me-2"></i>
                                    Lihat Semua Produk
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="../index.html" target="_blank" class="btn admin-btn admin-btn-success w-100 py-3">
                                    <i class="fas fa-external-link-alt me-2"></i>
                                    Preview Website
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="settings.php" class="btn btn-info w-100 py-3">
                                    <i class="fas fa-cog me-2"></i>
                                    Pengaturan Website
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card admin-card border-0 shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0 fw-bold">Informasi Sistem</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>PHP Version:</span>
                            <span class="text-muted"><?php echo PHP_VERSION; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Server:</span>
                            <span class="text-muted"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Database:</span>
                            <span class="text-muted">MySQL</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Last Login:</span>
                            <span class="text-muted"><?php echo date('H:i'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
    <script src="../assets/js/admin-sidebar-fix.js"></script>
</body>
</html>