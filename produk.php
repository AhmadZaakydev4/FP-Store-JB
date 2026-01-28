<?php include 'includes/cache_buster.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - FP Store</title>
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
                        <a class="nav-link active" href="produk.php">Produk</a>
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

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="fw-bold">Katalog Produk</h1>
                    <p>Temukan produk yang Anda butuhkan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Produk Section -->
    <section class="py-5">
        <div class="container">
            <?php
            // Load products and categories from database
            require_once 'config/database.php';
            
            try {
                $database = new Database();
                $db = $database->getConnection();
                
                // Get categories for filter
                $categoryQuery = "SELECT id, nama_kategori, icon FROM categories ORDER BY nama_kategori";
                $categoryStmt = $db->prepare($categoryQuery);
                $categoryStmt->execute();
                $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get selected category from URL
                $selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;
                $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
                
                // Build products query
                $productsQuery = "SELECT p.id, p.nama_produk, p.deskripsi_singkat, p.deskripsi, p.foto, p.created_at, 
                                         p.category_id, c.nama_kategori, c.icon as category_icon
                                  FROM products p 
                                  LEFT JOIN categories c ON p.category_id = c.id 
                                  WHERE p.is_active = 1";
                
                if ($selectedCategory > 0) {
                    $productsQuery .= " AND p.category_id = :category_id";
                }
                
                // Add sorting
                switch ($sortBy) {
                    case 'oldest':
                        $productsQuery .= " ORDER BY p.created_at ASC";
                        break;
                    case 'name-asc':
                        $productsQuery .= " ORDER BY p.nama_produk ASC";
                        break;
                    case 'name-desc':
                        $productsQuery .= " ORDER BY p.nama_produk DESC";
                        break;
                    default: // newest
                        $productsQuery .= " ORDER BY p.created_at DESC";
                        break;
                }
                
                $productsStmt = $db->prepare($productsQuery);
                if ($selectedCategory > 0) {
                    $productsStmt->bindParam(':category_id', $selectedCategory, PDO::PARAM_INT);
                }
                $productsStmt->execute();
                $products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);
                
            } catch(Exception $e) {
                $categories = [];
                $products = [];
                $error = $e->getMessage();
            }
            ?>
            
            <!-- Filter & Sorting Controls -->
            <div class="row mb-4">
                <!-- Category Filter -->
                <div class="col-lg-8 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-3">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-filter me-2"></i>Filter Kategori
                            </h6>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="produk.php" class="btn <?php echo $selectedCategory == 0 ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <i class="fas fa-th-large me-2"></i>Semua Produk
                                </a>
                                <?php foreach ($categories as $category): ?>
                                <a href="produk.php?category=<?php echo $category['id']; ?>&sort=<?php echo $sortBy; ?>" 
                                   class="btn <?php echo $selectedCategory == $category['id'] ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <i class="<?php echo $category['icon']; ?> me-2"></i><?php echo htmlspecialchars($category['nama_kategori']); ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sorting Controls -->
                <div class="col-lg-4 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-3">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-sort me-2"></i>Urutkan
                            </h6>
                            <form method="GET" action="produk.php">
                                <?php if ($selectedCategory > 0): ?>
                                <input type="hidden" name="category" value="<?php echo $selectedCategory; ?>">
                                <?php endif; ?>
                                <select class="form-select" name="sort" onchange="this.form.submit()">
                                    <option value="newest" <?php echo $sortBy == 'newest' ? 'selected' : ''; ?>>Terbaru</option>
                                    <option value="oldest" <?php echo $sortBy == 'oldest' ? 'selected' : ''; ?>>Terlama</option>
                                    <option value="name-asc" <?php echo $sortBy == 'name-asc' ? 'selected' : ''; ?>>Nama A-Z</option>
                                    <option value="name-desc" <?php echo $sortBy == 'name-desc' ? 'selected' : ''; ?>>Nama Z-A</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product Count & Results Info -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <i class="fas fa-box me-2"></i>
                            <?php 
                            $productCount = count($products);
                            echo "Menampilkan {$productCount} produk";
                            if ($selectedCategory > 0) {
                                $categoryName = '';
                                foreach ($categories as $cat) {
                                    if ($cat['id'] == $selectedCategory) {
                                        $categoryName = $cat['nama_kategori'];
                                        break;
                                    }
                                }
                                echo " dalam kategori \"{$categoryName}\"";
                            }
                            ?>
                        </div>
                        <?php if ($selectedCategory > 0 || $sortBy != 'newest'): ?>
                        <a href="produk.php" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-2"></i>Reset Filter
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="row">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <?php
                        // Format tanggal
                        $createdDate = date('d M Y', strtotime($product['created_at']));
                        
                        // WhatsApp link
                        $whatsappText = urlencode("Halo admin, saya tertarik dengan produk {$product['nama_produk']}. Apakah masih tersedia?");
                        $whatsappLink = "https://wa.me/6289507410373?text={$whatsappText}";
                        
                        // Check if needs detail button
                        $needsDetailButton = strlen($product['deskripsi']) > strlen($product['deskripsi_singkat']) + 20;
                        ?>
                        
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card product-card h-100 shadow-sm" onclick="window.location.href='detail-produk.php?id=<?php echo $product['id']; ?>'" style="cursor: pointer;">
                                <!-- Image and badges -->
                                <div class="position-relative">
                                    <img src="<?php echo htmlspecialchars($product['foto']); ?>" class="card-img-top product-image" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>" loading="lazy">
                                    
                                    <!-- Category badge -->
                                    <?php if ($product['nama_kategori']): ?>
                                    <div class="position-absolute top-0 start-0 m-2">
                                        <span class="badge bg-primary">
                                            <i class="<?php echo $product['category_icon'] ?: 'fas fa-tag'; ?> me-1"></i>
                                            <?php echo htmlspecialchars($product['nama_kategori']); ?>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <!-- Date badge -->
                                    <div class="position-absolute bottom-0 end-0 m-2">
                                        <small class="badge bg-dark bg-opacity-75">
                                            <i class="fas fa-calendar me-1"></i><?php echo $createdDate; ?>
                                        </small>
                                    </div>
                                </div>
                                
                                <!-- Card body -->
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title fw-bold"><?php echo htmlspecialchars($product['nama_produk']); ?></h5>
                                    
                                    <!-- Description -->
                                    <div class="product-description flex-grow-1">
                                        <div id="product-<?php echo $product['id']; ?>-short" class="description-short">
                                            <?php echo $product['deskripsi_singkat']; ?>
                                        </div>
                                        
                                        <?php if ($needsDetailButton): ?>
                                        <div id="product-<?php echo $product['id']; ?>-full" class="description-full" style="display: none;">
                                            <?php echo $product['deskripsi']; ?>
                                        </div>
                                        <button class="btn btn-link p-0 mt-2 detail-toggle" onclick="event.stopPropagation(); toggleDescription('product-<?php echo $product['id']; ?>')" id="product-<?php echo $product['id']; ?>-toggle">
                                            <small>Lihat Detail <i class="fas fa-chevron-down ms-1"></i></small>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Action buttons -->
                                    <div class="mt-3">
                                        <div class="row g-2">
                                            <div class="col-8">
                                                <a href="<?php echo $whatsappLink; ?>" target="_blank" class="btn btn-success w-100" onclick="event.stopPropagation()">
                                                    <i class="fab fa-whatsapp me-2"></i>Pesan Sekarang
                                                </a>
                                            </div>
                                            <div class="col-4">
                                                <a href="detail-produk.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary w-100" title="Lihat Detail" onclick="event.stopPropagation()">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h4>Tidak ada produk ditemukan</h4>
                        <p class="text-muted">
                            <?php if ($selectedCategory > 0): ?>
                                Tidak ada produk dalam kategori ini. <a href="produk.php">Lihat semua produk</a>
                            <?php else: ?>
                                Produk akan segera ditambahkan
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
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
        // Toggle description function
        function toggleDescription(productId) {
            const shortDiv = document.getElementById(productId + '-short');
            const fullDiv = document.getElementById(productId + '-full');
            const toggleBtn = document.getElementById(productId + '-toggle');
            
            if (fullDiv && toggleBtn) {
                if (fullDiv.style.display === 'none') {
                    shortDiv.style.display = 'none';
                    fullDiv.style.display = 'block';
                    toggleBtn.innerHTML = '<small>Sembunyikan <i class="fas fa-chevron-up ms-1"></i></small>';
                } else {
                    fullDiv.style.display = 'none';
                    shortDiv.style.display = 'block';
                    toggleBtn.innerHTML = '<small>Lihat Detail <i class="fas fa-chevron-down ms-1"></i></small>';
                }
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