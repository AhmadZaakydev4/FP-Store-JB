<?php include 'includes/cache_buster.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - FP Store</title>
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
                        <a class="nav-link active" href="kontak.php">Kontak</a>
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
                    <h1 class="fw-bold">Hubungi Kami</h1>
                    <p>Kami siap membantu kebutuhan Anda</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-5 contact-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="row g-4">
                        <div class="col-md-4 col-sm-6">
                            <div class="card h-100 border-0 shadow-sm text-center">
                                <div class="card-body">
                                    <i class="fab fa-whatsapp fa-3x text-success mb-3"></i>
                                    <h5 class="fw-bold">WhatsApp</h5>
                                    <p class="text-muted mb-3">Chat langsung dengan admin</p>
                                    <a href="https://wa.me/6289507410373?text=Halo admin, saya ingin bertanya" 
                                       class="btn btn-success" target="_blank">
                                        Chat Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="card h-100 border-0 shadow-sm text-center">
                                <div class="card-body">
                                    <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                                    <h5 class="fw-bold">Email</h5>
                                    <p class="text-muted mb-3">Kirim email untuk pertanyaan</p>
                                    <a href="mailto:fpstore@gmail.com?subject=Pertanyaan%20tentang%20Produk%20FP%20Store&body=Halo%20FP%20Store,%0A%0ASaya%20ingin%20bertanya%20tentang%20produk%20Anda.%0A%0ATerima%20kasih." 
                                       onclick="openEmail(); return false;" class="btn btn-primary">
                                        <i class="fas fa-envelope me-2"></i>
                                        Kirim Email
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="card h-100 border-0 shadow-sm text-center">
                                <div class="card-body">
                                    <i class="fas fa-map-marker-alt fa-3x text-danger mb-3"></i>
                                    <h5 class="fw-bold">Alamat</h5>
                                    <p class="text-muted mb-3">Alamat Kami</p>
                                    <button class="btn btn-danger" onclick="showAddress()">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        Buka di Google Maps
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Info -->
    <section class="py-5 bg-light contact-info">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h3 class="fw-bold mb-4 text-center">Informasi Kontak</h3>
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-phone text-primary me-3"></i>
                                <div class="text-center text-md-start">
                                    <strong>Telepon</strong><br>
                                    <span class="text-muted">+62 895-0741-0373</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-clock text-primary me-3"></i>
                                <div class="text-center text-md-start">
                                    <strong>Jam Operasional</strong><br>
                                    <span class="text-muted">24 Jam</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="https://wa.me/6289507410373?text=Halo admin, saya ingin bertanya tentang produk Anda" 
                           class="btn btn-success btn-lg me-3 mb-2" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>Chat WhatsApp
                        </a>
                        <a href="https://whatsapp.com/channel/0029VaABC123" 
                           class="btn btn-outline-success btn-lg mb-2" target="_blank">
                            <i class="fas fa-bell me-2"></i>Follow Channel
                        </a>
                    </div>
                </div>
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
        // Email function
        function openEmail() {
            window.location.href = 'mailto:fpstore@gmail.com?subject=Pertanyaan%20tentang%20Produk%20FP%20Store&body=Halo%20FP%20Store,%0A%0ASaya%20ingin%20bertanya%20tentang%20produk%20Anda.%0A%0ATerima%20kasih.';
        }
        
        // Address function
        function showAddress() {
            const address = "Jl. Raya Pahlawan No.29, Cinangka, Kec. Sawangan, Kota Depok, Jawa Barat 16515, Indonesia";
            const encodedAddress = encodeURIComponent(address);
            
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            
            let mapsUrl;
            if (isMobile) {
                mapsUrl = `https://maps.google.com/?q=${encodedAddress}`;
            } else {
                mapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodedAddress}`;
            }
            
            window.open(mapsUrl, '_blank');
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