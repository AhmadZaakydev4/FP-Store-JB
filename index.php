<?php include 'includes/cache_buster.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FP Store - Katalog Produk Online</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="shortcut icon" type="image/png" href="assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo asset('assets/css/style.min.css'); ?>" rel="stylesheet">
    <script>
        // Dark Mode Script - Load immediately
        (function() {
            // Apply saved theme immediately
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            // Function to toggle theme
            function toggleDarkMode() {
                const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                
                // Update all toggle button icons
                document.querySelectorAll('.theme-toggle i').forEach(icon => {
                    icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                });
                
                console.log('Theme switched to:', newTheme);
            }
            
            // Setup when DOM is ready
            document.addEventListener('DOMContentLoaded', function() {
                // Update initial icons
                const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
                document.querySelectorAll('.theme-toggle i').forEach(icon => {
                    icon.className = currentTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                });
                
                // Add click listeners
                document.querySelectorAll('.theme-toggle').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        toggleDarkMode();
                    });
                });
                
                console.log('Dark mode initialized');
            });
            
            // Make function global
            window.toggleDarkMode = toggleDarkMode;
        })();
    </script>
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
                        <a class="nav-link active" href="index.php">Home</a>
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-white mb-4">
                        Akun Game & Aplikasi Premium Terpercaya
                    </h1>
                    <p class="lead text-white mb-4" style="opacity: 0.9;">
                        Solusi terpercaya untuk kebutuhan akun game dan aplikasi premium Anda. 
                        Proses cepat, aman melalui WhatsApp.
                    </p>
                    <div class="d-flex gap-3 flex-wrap justify-content-center justify-content-lg-start">
                        <a href="https://wa.me/6289507410373?text=Halo admin, saya ingin bertanya tentang produk Anda" 
                           class="btn btn-success btn-lg" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>Chat WhatsApp
                        </a>
                        <a href="https://whatsapp.com/channel/0029VaABC123" 
                           class="btn btn-outline-success btn-lg" target="_blank">
                            <i class="fas fa-bell me-2"></i>Follow Channel
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="assets/images/hero-image.jpg" alt="Hero Image" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Produk Unggulan -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center section-header">
                <h2 class="fw-bold">Produk Unggulan</h2>
                <p class="text-muted">Pilihan terbaik untuk kebutuhan Anda</p>
            </div>
            <div class="row">
                <?php
                // Load products directly from database
                require_once 'config/database.php';
                
                try {
                    $database = new Database();
                    $db = $database->getConnection();
                    
                    // Query untuk mengambil 6 produk terbaru
                    $query = "SELECT p.id, p.nama_produk, p.deskripsi_singkat, p.deskripsi, p.foto, p.created_at, 
                                     p.category_id, c.nama_kategori, c.icon as category_icon
                              FROM products p 
                              LEFT JOIN categories c ON p.category_id = c.id 
                              WHERE p.is_active = 1 
                              ORDER BY p.created_at DESC 
                              LIMIT 6";
                    
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($products) > 0) {
                        foreach ($products as $product) {
                            // Format tanggal
                            $createdDate = date('d M Y', strtotime($product['created_at']));
                            
                            // WhatsApp link
                            $whatsappText = urlencode("Halo admin, saya tertarik dengan produk {$product['nama_produk']}. Apakah masih tersedia?");
                            $whatsappLink = "https://wa.me/6289507410373?text={$whatsappText}";
                            
                            // Check if needs detail button
                            $needsDetailButton = strlen($product['deskripsi']) > strlen($product['deskripsi_singkat']) + 20;
                            
                            echo '<div class="col-lg-4 col-md-6 mb-4">';
                            echo '<div class="card product-card h-100 shadow-sm" onclick="window.location.href=\'detail-produk.html?id=' . $product['id'] . '\'" style="cursor: pointer;">';
                            
                            // Image and badges
                            echo '<div class="position-relative">';
                            echo '<img src="' . htmlspecialchars($product['foto']) . '" class="card-img-top product-image" alt="' . htmlspecialchars($product['nama_produk']) . '" loading="lazy">';
                            
                            // Category badge
                            if ($product['nama_kategori']) {
                                echo '<div class="position-absolute top-0 start-0 m-2">';
                                echo '<span class="badge bg-primary">';
                                echo '<i class="' . ($product['category_icon'] ?: 'fas fa-tag') . ' me-1"></i>';
                                echo htmlspecialchars($product['nama_kategori']);
                                echo '</span>';
                                echo '</div>';
                            }
                            
                            // Date badge
                            echo '<div class="position-absolute bottom-0 end-0 m-2">';
                            echo '<small class="badge bg-dark bg-opacity-75">';
                            echo '<i class="fas fa-calendar me-1"></i>' . $createdDate;
                            echo '</small>';
                            echo '</div>';
                            echo '</div>';
                            
                            // Card body
                            echo '<div class="card-body d-flex flex-column">';
                            echo '<h5 class="card-title fw-bold">' . htmlspecialchars($product['nama_produk']) . '</h5>';
                            
                            // Description
                            echo '<div class="product-description flex-grow-1">';
                            echo '<div id="product-' . $product['id'] . '-short" class="description-short">';
                            echo $product['deskripsi_singkat']; // Allow HTML
                            echo '</div>';
                            
                            if ($needsDetailButton) {
                                echo '<div id="product-' . $product['id'] . '-full" class="description-full" style="display: none;">';
                                echo $product['deskripsi']; // Allow HTML
                                echo '</div>';
                                echo '<button class="btn btn-link p-0 mt-2 detail-toggle" onclick="event.stopPropagation(); toggleDescription(\'product-' . $product['id'] . '\')" id="product-' . $product['id'] . '-toggle">';
                                echo '<small>Lihat Detail <i class="fas fa-chevron-down ms-1"></i></small>';
                                echo '</button>';
                            }
                            echo '</div>';
                            
                            // Action buttons
                            echo '<div class="mt-3">';
                            echo '<div class="row g-2">';
                            echo '<div class="col-8">';
                            echo '<a href="' . $whatsappLink . '" target="_blank" class="btn btn-success w-100" onclick="event.stopPropagation()">';
                            echo '<i class="fab fa-whatsapp me-2"></i>Pesan Sekarang';
                            echo '</a>';
                            echo '</div>';
                            echo '<div class="col-4">';
                            echo '<a href="detail-produk.html?id=' . $product['id'] . '" class="btn btn-outline-primary w-100" title="Lihat Detail" onclick="event.stopPropagation()">';
                            echo '<i class="fas fa-eye"></i>';
                            echo '</a>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            
                            echo '</div>'; // card-body
                            echo '</div>'; // card
                            echo '</div>'; // col
                        }
                        
                        // Show "View More" button if there are more products
                        $totalQuery = "SELECT COUNT(*) as total FROM products WHERE is_active = 1";
                        $totalStmt = $db->prepare($totalQuery);
                        $totalStmt->execute();
                        $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($totalResult['total'] > 6) {
                            echo '<div class="col-12 text-center mt-4">';
                            echo '<a href="produk.php" class="btn btn-primary btn-lg">';
                            echo 'Lihat Semua Produk <i class="fas fa-arrow-right ms-2"></i>';
                            echo '</a>';
                            echo '</div>';
                        }
                        
                    } else {
                        echo '<div class="col-12 text-center">';
                        echo '<i class="fas fa-box-open fa-3x text-muted mb-3"></i>';
                        echo '<h4>Belum ada produk</h4>';
                        echo '<p class="text-muted">Produk akan segera ditambahkan</p>';
                        echo '</div>';
                    }
                    
                } catch(Exception $e) {
                    echo '<div class="col-12 text-center">';
                    echo '<i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>';
                    echo '<h4>Error loading products</h4>';
                    echo '<p class="text-muted">Please try again later</p>';
                    echo '</div>';
                }
                ?>
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
                    <!-- Admin link disembunyikan, akses langsung via admin/login.php -->
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple toggle description function
        function toggleDescription(productId) {
            const shortDiv = document.getElementById(productId + '-short');
            const fullDiv = document.getElementById(productId + '-full');
            const toggleBtn = document.getElementById(productId + '-toggle');
            
            if (fullDiv && toggleBtn) {
                if (fullDiv.style.display === 'none') {
                    // Show full description
                    shortDiv.style.display = 'none';
                    fullDiv.style.display = 'block';
                    toggleBtn.innerHTML = '<small>Sembunyikan <i class="fas fa-chevron-up ms-1"></i></small>';
                } else {
                    // Show short description
                    fullDiv.style.display = 'none';
                    shortDiv.style.display = 'block';
                    toggleBtn.innerHTML = '<small>Lihat Detail <i class="fas fa-chevron-down ms-1"></i></small>';
                }
            }
        }
        
        // Dark Mode Toggle - Inline implementation
        document.addEventListener('DOMContentLoaded', function() {
            // Get saved theme
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            // Find and setup toggle buttons
            const toggleButtons = document.querySelectorAll('.theme-toggle');
            
            // Update icons based on current theme
            function updateIcons(theme) {
                toggleButtons.forEach(btn => {
                    const icon = btn.querySelector('i');
                    if (icon) {
                        icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                    }
                });
            }
            
            // Initial icon update
            updateIcons(savedTheme);
            
            // Add click listeners
            toggleButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
                    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                    
                    // Apply theme
                    document.documentElement.setAttribute('data-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    
                    // Update icons
                    updateIcons(newTheme);
                    
                    // Animation
                    this.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 100);
                });
            });
        });
    </script>
    <script>
        // Temporary debug script
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== HOMEPAGE DEBUG ===');
            
            const currentPage = window.location.pathname.split('/').pop();
            console.log('Current page:', currentPage);
            
            // Add toggleDescription function
            window.toggleDescription = function(productId) {
                const shortDiv = document.getElementById(productId + '-short');
                const fullDiv = document.getElementById(productId + '-full');
                const toggleBtn = document.getElementById(productId + '-toggle');
                
                if (fullDiv.style.display === 'none') {
                    // Show full description
                    shortDiv.style.display = 'none';
                    fullDiv.style.display = 'block';
                    toggleBtn.innerHTML = '<small>Sembunyikan <i class="fas fa-chevron-up ms-1"></i></small>';
                } else {
                    // Show short description
                    fullDiv.style.display = 'none';
                    shortDiv.style.display = 'block';
                    toggleBtn.innerHTML = '<small>Lihat Detail <i class="fas fa-chevron-down ms-1"></i></small>';
                }
            };
            
            // Check if script.js loaded properly
            if (typeof loadProducts === 'function') {
                console.log('✅ loadProducts function exists');
            } else {
                console.log('❌ loadProducts function NOT found');
            }
            
            // Test API directly
            setTimeout(() => {
                console.log('Testing API...');
                fetch('api/get_products.php')
                    .then(response => response.json())
                    .then(data => {
                        console.log('API Response:', data);
                        
                        const container = document.getElementById('produk-unggulan');
                        if (data.success && data.products.length > 0 && container) {
                            console.log('✅ Manually loading products...');
                            
                            container.innerHTML = '';
                            data.products.slice(0, 6).forEach(product => {
                                const categoryBadge = product.nama_kategori ? 
                                    `<span class="badge bg-primary position-absolute top-0 start-0 m-2">
                                        <i class="${product.category_icon || 'fas fa-tag'} me-1"></i>
                                        ${product.nama_kategori}
                                    </span>` : '';
                                
                                const productCard = `
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="card product-card h-100 shadow-sm" onclick="window.location.href='detail-produk.html?id=${product.id}'" style="cursor: pointer;">
                                            <div class="position-relative">
                                                <img src="${product.foto}" class="card-img-top product-image" alt="${product.nama_produk}" loading="lazy">
                                                ${categoryBadge}
                                                <div class="position-absolute bottom-0 end-0 m-2">
                                                    <span class="badge bg-dark bg-opacity-75 text-white">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        ${new Date(product.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <h5 class="card-title fw-bold">${product.nama_produk}</h5>
                                                <div class="product-description flex-grow-1">
                                                    <div class="description-short" id="product-${product.id}-short">
                                                        <div>${product.deskripsi_singkat}</div>
                                                    </div>
                                                    <div class="description-full" id="product-${product.id}-full" style="display: none;">
                                                        <div>${product.deskripsi}</div>
                                                    </div>
                                                    ${product.deskripsi !== product.deskripsi_singkat ? `
                                                    <button class="btn btn-link p-0 mt-2 detail-toggle" 
                                                            onclick="event.stopPropagation(); toggleDescription('product-${product.id}')" 
                                                            id="product-${product.id}-toggle">
                                                        <small>Lihat Detail <i class="fas fa-chevron-down ms-1"></i></small>
                                                    </button>
                                                    ` : ''}
                                                </div>
                                                <div class="mt-3">
                                                    <div class="row g-2">
                                                        <div class="col-8">
                                                            <a href="https://wa.me/6289507410373?text=Halo admin, saya tertarik dengan produk ${encodeURIComponent(product.nama_produk)}. Apakah masih tersedia?" 
                                                               class="btn btn-success w-100" target="_blank">
                                                                <i class="fab fa-whatsapp me-2"></i>Pesan Sekarang
                                                            </a>
                                                        </div>
                                                        <div class="col-4">
                                                            <a href="detail-produk.html?id=${product.id}" class="btn btn-outline-primary w-100" title="Lihat Detail" onclick="event.stopPropagation()">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                container.innerHTML += productCard;
                            });
                            
                            // Show view more button
                            const viewMoreContainer = document.getElementById('view-more-container');
                            if (viewMoreContainer && data.products.length > 6) {
                                viewMoreContainer.style.display = 'block';
                            }
                            
                            console.log('✅ Products loaded manually');
                        }
                    })
                    .catch(error => {
                        console.error('❌ API Error:', error);
                    });
            }, 500);
        });
    </script>
</body>
</html>