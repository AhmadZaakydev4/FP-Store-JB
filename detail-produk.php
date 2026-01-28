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
                            <img src="<?php echo htmlspecialchars($product['foto']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['nama_produk']); ?>" 
                                 class="img-fluid rounded shadow main-product-image clickable-image" 
                                 onclick="openImageModal('<?php echo htmlspecialchars($product['foto']); ?>', '<?php echo htmlspecialchars($product['nama_produk']); ?>')"
                                 style="cursor: pointer;">
                            <div class="image-overlay position-absolute top-50 start-50 translate-middle">
                                <i class="fas fa-search-plus text-white fa-2x opacity-75"></i>
                            </div>
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

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-white" id="imageModalLabel">Preview Gambar</h5>
                    <div class="d-flex align-items-center gap-2">
                        <!-- Zoom Controls -->
                        <div class="zoom-controls d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-outline-light btn-sm zoom-btn" onclick="zoomOut()" title="Zoom Out">
                                <i class="fas fa-minus"></i>
                            </button>
                            
                            <!-- Zoom Level Dropdown -->
                            <div class="dropdown">
                                <button class="btn btn-outline-light btn-sm dropdown-toggle zoom-level-btn" type="button" data-bs-toggle="dropdown" title="Zoom Presets">
                                    <span class="zoom-level">100%</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark">
                                    <li><a class="dropdown-item" href="#" onclick="zoomTo(0.1)">10%</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="zoomTo(0.25)">25%</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="zoomTo(0.5)">50%</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="zoomTo(0.75)">75%</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="zoomTo(1)">100%</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="zoomTo(1.5)">150%</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="zoomTo(2)">200%</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="zoomTo(3)">300%</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="zoomTo(5)">500%</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="zoomTo(10)">1000%</a></li>
                                </ul>
                            </div>
                            
                            <button type="button" class="btn btn-outline-light btn-sm zoom-btn" onclick="zoomIn()" title="Zoom In">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button type="button" class="btn btn-outline-light btn-sm zoom-btn" onclick="resetZoom()" title="Reset Zoom">
                                <i class="fas fa-expand-arrows-alt"></i>
                            </button>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body text-center p-0" id="imageContainer">
                    <div class="image-wrapper">
                        <img id="modalImage" src="" alt="" class="img-fluid rounded shadow-lg zoomable-image" style="max-height: 80vh; width: auto; transition: transform 0.3s ease;">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-2">
                    <div class="d-flex justify-content-between w-100">
                        <button type="button" class="btn btn-outline-light" onclick="downloadImage()">
                            <i class="fas fa-download me-2"></i>Download
                        </button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-light" onclick="toggleFullscreen()" title="Fullscreen">
                                <i class="fas fa-expand me-2"></i>Fullscreen
                            </button>
                            <button type="button" class="btn btn-outline-light" onclick="shareImage()">
                                <i class="fas fa-share me-2"></i>Share
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
        
        // Image Modal Functions
        let currentZoom = 1;
        let isDragging = false;
        let startX, startY, translateX = 0, translateY = 0;
        
        function openImageModal(imageSrc, imageAlt) {
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            const modalImage = document.getElementById('modalImage');
            const modalTitle = document.getElementById('imageModalLabel');
            
            modalImage.src = imageSrc;
            modalImage.alt = imageAlt;
            modalTitle.textContent = imageAlt;
            
            // Reset zoom and position
            currentZoom = 1;
            translateX = 0;
            translateY = 0;
            updateImageTransform();
            updateZoomLevel();
            
            // Store current image for download/share
            window.currentModalImage = {
                src: imageSrc,
                alt: imageAlt
            };
            
            modal.show();
            
            // Add event listeners after modal is shown
            setTimeout(() => {
                setupImageInteractions();
            }, 300);
        }
        
        function setupImageInteractions() {
            const modalImage = document.getElementById('modalImage');
            const imageContainer = document.getElementById('imageContainer');
            
            // Mouse wheel zoom
            imageContainer.addEventListener('wheel', function(e) {
                e.preventDefault();
                // Adaptive wheel zoom - smaller steps for lower zoom levels
                const step = currentZoom < 1 ? 0.05 : (currentZoom < 2 ? 0.1 : 0.2);
                const delta = e.deltaY > 0 ? -step : step;
                zoom(delta);
            });
            
            // Touch/Mouse drag to pan
            modalImage.addEventListener('mousedown', startDrag);
            modalImage.addEventListener('touchstart', startDrag);
            
            document.addEventListener('mousemove', drag);
            document.addEventListener('touchmove', drag);
            
            document.addEventListener('mouseup', endDrag);
            document.addEventListener('touchend', endDrag);
            
            // Double click to zoom
            modalImage.addEventListener('dblclick', function() {
                if (currentZoom === 1) {
                    zoomTo(2);
                } else {
                    resetZoom();
                }
            });
        }
        
        function startDrag(e) {
            if (currentZoom <= 1) return;
            
            isDragging = true;
            modalImage.style.cursor = 'grabbing';
            
            const clientX = e.type === 'mousedown' ? e.clientX : e.touches[0].clientX;
            const clientY = e.type === 'mousedown' ? e.clientY : e.touches[0].clientY;
            
            startX = clientX - translateX;
            startY = clientY - translateY;
            
            e.preventDefault();
        }
        
        function drag(e) {
            if (!isDragging || currentZoom <= 1) return;
            
            const clientX = e.type === 'mousemove' ? e.clientX : e.touches[0].clientX;
            const clientY = e.type === 'mousemove' ? e.clientY : e.touches[0].clientY;
            
            translateX = clientX - startX;
            translateY = clientY - startY;
            
            updateImageTransform();
            e.preventDefault();
        }
        
        function endDrag() {
            isDragging = false;
            const modalImage = document.getElementById('modalImage');
            modalImage.style.cursor = currentZoom > 1 ? 'grab' : 'default';
        }
        
        function zoom(delta) {
            const newZoom = Math.max(0.1, Math.min(10, currentZoom + delta));
            zoomTo(newZoom);
        }
        
        function zoomIn() {
            // Adaptive zoom step - smaller steps for lower zoom levels
            const step = currentZoom < 1 ? 0.1 : (currentZoom < 2 ? 0.2 : 0.5);
            zoom(step);
        }
        
        function zoomOut() {
            // Adaptive zoom step - smaller steps for lower zoom levels  
            const step = currentZoom <= 1 ? 0.1 : (currentZoom <= 2 ? 0.2 : 0.5);
            zoom(-step);
        }
        
        function zoomTo(level) {
            currentZoom = level;
            
            // Reset position if zooming out to 1x or less
            if (currentZoom <= 1) {
                translateX = 0;
                translateY = 0;
                document.getElementById('modalImage').style.cursor = 'default';
            } else {
                document.getElementById('modalImage').style.cursor = 'grab';
            }
            
            updateImageTransform();
            updateZoomLevel();
        }
        
        function resetZoom() {
            zoomTo(1);
        }
        
        function updateImageTransform() {
            const modalImage = document.getElementById('modalImage');
            modalImage.style.transform = `scale(${currentZoom}) translate(${translateX / currentZoom}px, ${translateY / currentZoom}px)`;
        }
        
        function updateZoomLevel() {
            const zoomLevelElement = document.querySelector('.zoom-level');
            if (zoomLevelElement) {
                // Show more precise zoom levels for small values
                let displayZoom;
                if (currentZoom < 1) {
                    displayZoom = Math.round(currentZoom * 1000) / 10; // Show 1 decimal for small values
                } else {
                    displayZoom = Math.round(currentZoom * 100);
                }
                zoomLevelElement.textContent = displayZoom + '%';
            }
        }
        
        function toggleFullscreen() {
            const modal = document.getElementById('imageModal');
            
            if (!document.fullscreenElement) {
                modal.requestFullscreen().catch(err => {
                    console.log('Error attempting to enable fullscreen:', err.message);
                });
            } else {
                document.exitFullscreen();
            }
        }
        
        function downloadImage() {
            if (window.currentModalImage) {
                const link = document.createElement('a');
                link.href = window.currentModalImage.src;
                link.download = window.currentModalImage.alt.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.jpg';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
        
        function shareImage() {
            if (window.currentModalImage && navigator.share) {
                navigator.share({
                    title: window.currentModalImage.alt + ' - FP Store',
                    text: 'Lihat gambar produk ini di FP Store!',
                    url: window.location.href
                });
            } else {
                // Fallback: copy image URL
                if (window.currentModalImage) {
                    navigator.clipboard.writeText(window.currentModalImage.src).then(() => {
                        alert('Link gambar berhasil disalin!');
                    });
                }
            }
        }
        
        // Keyboard navigation for modal
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('imageModal');
            if (modal.classList.contains('show')) {
                switch(e.key) {
                    case 'Escape':
                        bootstrap.Modal.getInstance(modal).hide();
                        break;
                    case '+':
                    case '=':
                        zoomIn();
                        break;
                    case '-':
                        zoomOut();
                        break;
                    case '0':
                        resetZoom();
                        break;
                    case 'f':
                    case 'F':
                        toggleFullscreen();
                        break;
                }
            }
        });
        
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