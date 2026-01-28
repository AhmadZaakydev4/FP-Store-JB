<?php 
include 'includes/cache_buster.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: produk.php');
    exit;
}

// Load product from database
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get product details
    $query = "SELECT p.id, p.nama_produk, p.deskripsi_singkat, p.deskripsi, p.foto, p.created_at, p.is_active,
                     c.nama_kategori, c.icon as category_icon, c.id as category_id
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.id = :product_id AND p.is_active = 1";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        header('Location: produk.php');
        exit;
    }
    
    // Get related products (same category, excluding current product)
    $relatedQuery = "SELECT p.id, p.nama_produk, p.deskripsi_singkat, p.foto, p.created_at,
                            c.nama_kategori, c.icon as category_icon
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     WHERE p.is_active = 1 AND p.id != :product_id";
    
    if ($product['category_id']) {
        $relatedQuery .= " AND p.category_id = :category_id";
    }
    
    $relatedQuery .= " ORDER BY p.created_at DESC LIMIT 3";
    
    $relatedStmt = $db->prepare($relatedQuery);
    $relatedStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    if ($product['category_id']) {
        $relatedStmt->bindParam(':category_id', $product['category_id'], PDO::PARAM_INT);
    }
    $relatedStmt->execute();
    $relatedProducts = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(Exception $e) {
    header('Location: produk.php');
    exit;
}

// Format data
$createdDate = date('d M Y', strtotime($product['created_at']));
$whatsappText = urlencode("Halo admin, saya tertarik dengan produk {$product['nama_produk']}. Apakah masih tersedia?");
$whatsappLink = "https://wa.me/6289507410373?text={$whatsappText}";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['nama_produk']); ?> - FP Store</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="shortcut icon" type="image/png" href="assets/images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo asset('assets/css/style.min.css'); ?>" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.php">
                <img src="assets/images/logo.png" alt="FP Store" height="50" class="me-2">FP Store
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="produk.php">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tentang.php">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="kontak.php">Kontak</a>
                    </li>
                    <li class="nav-item">
                        <button class="theme-toggle ms-2" title="Toggle Dark Mode">
                            <i class="fas fa-moon"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <section class="py-3 bg-light" style="margin-top: 80px;">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="produk.php">Produk</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['nama_produk']); ?></li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Product Detail Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Product Images -->
                <div class="col-lg-6 mb-4">
                    <div class="product-image-container">
                        <div class="main-image-wrapper position-relative">
                            <img src="<?php echo htmlspecialchars($product['foto']); ?>" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>" class="img-fluid rounded shadow main-product-image">
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="product-info">
                        <!-- Category Badge -->
                        <?php if ($product['nama_kategori']): ?>
                        <div class="mb-3">
                            <span class="badge bg-primary fs-6">
                                <i class="<?php echo $product['category_icon'] ?: 'fas fa-tag'; ?> me-2"></i>
                                <?php echo htmlspecialchars($product['nama_kategori']); ?>
                            </span>
                        </div>
                        <?php endif; ?>

                        <!-- Product Title -->
                        <h1 class="product-title fw-bold mb-3"><?php echo htmlspecialchars($product['nama_produk']); ?></h1>

                        <!-- Product Meta Info -->
                        <div class="product-meta mb-4">
                            <div class="row g-3">
                                <div class="col-auto">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        Ditambahkan: <?php echo $createdDate; ?>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Short Description -->
                        <div class="product-short-description mb-4">
                            <h5 class="fw-semibold mb-2">Ringkasan</h5>
                            <div class="text-muted">
                                <?php echo $product['deskripsi_singkat']; ?>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="product-actions mb-4">
                            <div class="row g-3">
                                <div class="col-md-9">
                                    <a href="<?php echo $whatsappLink; ?>" class="btn btn-success btn-lg w-100" target="_blank">
                                        <i class="fab fa-whatsapp me-2"></i>Pesan Sekarang
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-outline-primary w-100" onclick="shareProduct()" title="Share Produk">
                                        <i class="fas fa-share-alt me-2"></i>Share
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="additional-info">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="info-item text-center p-3 bg-light rounded">
                                        <i class="fas fa-star text-warning fa-2x mb-2"></i>
                                        <h6 class="fw-semibold mb-1">Cepat & Efisien</h6>
                                        <small class="text-muted">Tanpa ribet</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-item text-center p-3 bg-light rounded">
                                        <i class="fas fa-shipping-fast text-success fa-2x mb-2"></i>
                                        <h6 class="fw-semibold mb-1">Gercep!</h6>
                                        <small class="text-muted">Respon super cepat ⚡</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Description -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent">
                            <h4 class="fw-bold mb-0">
                                <i class="fas fa-info-circle me-2"></i>Detail Produk
                            </h4>
                        </div>
                        <div class="card-body">
                            <div>
                                <?php echo $product['deskripsi']; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <?php if (count($relatedProducts) > 0): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <h4 class="fw-bold mb-4">
                        <i class="fas fa-box me-2"></i>Produk Terkait
                    </h4>
                    <div class="row">
                        <?php foreach ($relatedProducts as $related): ?>
                            <?php
                            $relatedDate = date('d M Y', strtotime($related['created_at']));
                            $relatedWhatsappText = urlencode("Halo admin, saya tertarik dengan produk {$related['nama_produk']}. Apakah masih tersedia?");
                            $relatedWhatsappLink = "https://wa.me/6289507410373?text={$relatedWhatsappText}";
                            ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card product-card h-100 shadow-sm" onclick="window.location.href='detail-produk.php?id=<?php echo $related['id']; ?>'" style="cursor: pointer;">
                                    <div class="position-relative">
                                        <img src="<?php echo htmlspecialchars($related['foto']); ?>" class="card-img-top product-image" alt="<?php echo htmlspecialchars($related['nama_produk']); ?>" loading="lazy">
                                        
                                        <?php if ($related['nama_kategori']): ?>
                                        <div class="position-absolute top-0 start-0 m-2">
                                            <span class="badge bg-primary">
                                                <i class="<?php echo $related['category_icon'] ?: 'fas fa-tag'; ?> me-1"></i>
                                                <?php echo htmlspecialchars($related['nama_kategori']); ?>
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="position-absolute bottom-0 end-0 m-2">
                                            <small class="badge bg-dark bg-opacity-75">
                                                <i class="fas fa-calendar me-1"></i><?php echo $relatedDate; ?>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($related['nama_produk']); ?></h5>
                                        <div class="product-description flex-grow-1">
                                            <div class="description-short">
                                                <?php echo $related['deskripsi_singkat']; ?>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <div class="row g-2">
                                                <div class="col-8">
                                                    <a href="<?php echo $relatedWhatsappLink; ?>" target="_blank" class="btn btn-success w-100" onclick="event.stopPropagation()">
                                                        <i class="fab fa-whatsapp me-2"></i>Pesan Sekarang
                                                    </a>
                                                </div>
                                                <div class="col-4">
                                                    <a href="detail-produk.php?id=<?php echo $related['id']; ?>" class="btn btn-outline-primary w-100" title="Lihat Detail" onclick="event.stopPropagation()">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>FP Store</h5>
                    <p class="text-muted">Solusi Jual Beli online terpercaya dengan layanan terbaik.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; 2026 FP Store. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Share product function
        function shareProduct() {
            const productTitle = "<?php echo addslashes($product['nama_produk']); ?>";
            const productUrl = window.location.href;
            
            if (navigator.share) {
                navigator.share({
                    title: productTitle + ' - FP Store',
                    text: 'Lihat produk menarik ini di FP Store!',
                    url: productUrl
                });
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(productUrl).then(() => {
                    alert('Link produk berhasil disalin!');
                });
            }
        }
        
        // Dark Mode Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            const toggleButtons = document.querySelectorAll('.theme-toggle');
            
            function updateIcons(theme) {
                toggleButtons.forEach(btn => {
                    const icon = btn.querySelector('i');
                    if (icon) {
                        icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                    }
                });
            }
            
            updateIcons(savedTheme);
            
            toggleButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
                    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                    
                    document.documentElement.setAttribute('data-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    
                    updateIcons(newTheme);
                    
                    this.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 100);
                });
            });
        });
    </script>
</body>
</html>