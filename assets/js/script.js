// JavaScript untuk website katalog produk

// Konfigurasi WhatsApp (akan dimuat dari database)
let WHATSAPP_CONFIG = {
    link: 'https://wa.me/6281234567890', // Default, akan diupdate dari database
    channel: 'https://whatsapp.com/channel/0029VaABC123' // Default, akan diupdate dari database
};

// Fungsi untuk membuat link WhatsApp dengan pesan otomatis
function createWhatsAppLink(productName) {
    const message = `Halo admin, saya tertarik dengan produk ${productName}. Apakah masih tersedia?`;
    const encodedMessage = encodeURIComponent(message);
    
    // Jika link sudah lengkap, tambahkan parameter text
    if (WHATSAPP_CONFIG.link.includes('wa.me/')) {
        const separator = WHATSAPP_CONFIG.link.includes('?') ? '&' : '?';
        return `${WHATSAPP_CONFIG.link}${separator}text=${encodedMessage}`;
    }
    
    // Fallback jika format tidak sesuai
    return `https://wa.me/6281234567890?text=${encodedMessage}`;
}

// Fungsi untuk memuat produk dari database
async function loadProducts(limit = null) {
    try {
        const url = limit ? `api/get_products.php?limit=${limit}` : 'api/get_products.php';
        console.log('Loading products from:', url);
        
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        console.log('Products API response:', data);
        
        if (data.success) {
            console.log('Products loaded successfully:', data.products.length, 'items');
            console.log('Product details:', data.products);
            return data.products;
        } else {
            console.error('Error loading products:', data.message);
            return [];
        }
    } catch (error) {
        console.error('Error fetching products:', error);
        return [];
    }
}

// Fungsi untuk menampilkan produk dalam bentuk card
function displayProducts(products, containerId) {
    console.log('displayProducts called with:', products.length, 'products for container:', containerId);
    
    const container = document.getElementById(containerId);
    
    if (!container) {
        console.error('Container not found:', containerId);
        return;
    }
    
    console.log('Container found:', container);
    
    if (products.length === 0) {
        console.log('No products to display');
        if (containerId === 'produk-container') {
            const loadingElement = document.getElementById('loading');
            const noProductsElement = document.getElementById('no-products');
            if (loadingElement) loadingElement.style.display = 'none';
            if (noProductsElement) noProductsElement.style.display = 'block';
        }
        return;
    }
    
    console.log('Clearing container and adding products...');
    container.innerHTML = '';
    
    products.forEach((product, index) => {
        console.log('Creating card for product:', product.nama_produk);
        const productCard = createProductCard(product);
        container.appendChild(productCard);
    });
    
    console.log('Products added to container');
    
    // Sembunyikan loading dan tampilkan container
    if (containerId === 'produk-container') {
        const loadingElement = document.getElementById('loading');
        if (loadingElement) loadingElement.style.display = 'none';
        container.style.display = 'flex';
    }
    
    // Untuk homepage, pastikan container terlihat
    if (containerId === 'produk-unggulan') {
        container.style.display = 'flex';
        console.log('Homepage products container made visible');
    }
}

// Fungsi untuk menangani tombol "Lihat Lainnya"
function handleViewMoreButton(totalProducts) {
    const viewMoreContainer = document.getElementById('view-more-container');
    
    if (!viewMoreContainer) {
        console.error('View more container not found');
        return;
    }
    
    // Jika produk lebih dari 6, tampilkan tombol "Lihat Lainnya"
    if (totalProducts > 6) {
        viewMoreContainer.style.display = 'block';
        const button = viewMoreContainer.querySelector('.btn');
        if (button) {
            button.innerHTML = '<i class="fas fa-plus me-2"></i>Lihat Lainnya';
        }
    } else if (totalProducts > 0) {
        // Jika produk 1-6, tampilkan "Lihat Semua Produk"
        viewMoreContainer.style.display = 'block';
        const button = viewMoreContainer.querySelector('.btn');
        if (button) {
            button.innerHTML = 'Lihat Semua Produk <i class="fas fa-arrow-right ms-2"></i>';
        }
    } else {
        // Jika tidak ada produk, sembunyikan tombol
        viewMoreContainer.style.display = 'none';
    }
}

// Fungsi untuk membuat card produk
function createProductCard(product) {
    return createProductCardWithCategory(product);
}

// Update createProductCard to show category badge
function createProductCardWithCategory(product) {
    const col = document.createElement('div');
    col.className = 'col-lg-4 col-md-6 mb-4';
    col.setAttribute('data-category', product.category_id || 'uncategorized');
    
    const whatsappLink = createWhatsAppLink(product.nama_produk);
    
    // Use separate short and full descriptions
    const shortDescription = product.deskripsi_singkat || 'Tidak ada deskripsi singkat';
    const fullDescription = product.deskripsi || 'Tidak ada deskripsi lengkap';
    
    // Generate unique ID for this product
    const productId = `product-${product.id}`;
    
    // Check if we need "Lihat Detail" button - simplified logic
    const needsDetailButton = fullDescription.length > shortDescription.length + 20;
    
    console.log('Product detail button check:', {
        productId: productId,
        productName: product.nama_produk,
        shortLength: shortDescription.length,
        fullLength: fullDescription.length,
        needsDetailButton: needsDetailButton
    });
    
    // Category badge
    let categoryBadge = '';
    if (product.nama_kategori) {
        categoryBadge = `
            <div class="position-absolute top-0 start-0 m-2">
                <span class="badge bg-primary">
                    <i class="${product.category_icon || 'fas fa-tag'} me-1"></i>
                    ${product.nama_kategori}
                </span>
            </div>
        `;
    }
    
    // Format tanggal untuk sorting info
    const createdDate = new Date(product.created_at).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
    
    col.innerHTML = `
        <div class="card product-card h-100 shadow-sm" onclick="window.location.href='detail-produk.html?id=${product.id}'" style="cursor: pointer;">
            <div class="position-relative">
                <img src="${product.foto}" class="card-img-top product-image" alt="${product.nama_produk}" loading="lazy">
                ${categoryBadge}
                <div class="position-absolute bottom-0 end-0 m-2">
                    <small class="badge bg-dark bg-opacity-75">
                        <i class="fas fa-calendar me-1"></i>${createdDate}
                    </small>
                </div>
            </div>
            <div class="card-body d-flex flex-column">
                <h5 class="card-title fw-bold">${product.nama_produk}</h5>
                <div class="product-description flex-grow-1">
                    <div id="${productId}-short" class="description-short">
                        <!-- Short description will be set via innerHTML -->
                    </div>
                    ${needsDetailButton ? `
                        <div id="${productId}-full" class="description-full" style="display: none;">
                            <!-- Full description will be set via innerHTML -->
                        </div>
                        <button class="btn btn-link p-0 mt-2 detail-toggle" 
                                onclick="event.stopPropagation(); toggleDescription('${productId}')" 
                                id="${productId}-toggle">
                            <small>Lihat Detail <i class="fas fa-chevron-down ms-1"></i></small>
                        </button>
                    ` : ''}
                </div>
                <div class="mt-3">
                    <div class="row g-2">
                        <div class="col-8">
                            <a href="${whatsappLink}" target="_blank" class="btn btn-success w-100" onclick="event.stopPropagation()">
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
    `;
    
    // Immediately set innerHTML for description divs after the card is created
    const shortDiv = col.querySelector(`#${productId}-short`);
    const fullDiv = col.querySelector(`#${productId}-full`);
    
    if (shortDiv) {
        shortDiv.innerHTML = shortDescription;
    }
    if (fullDiv) {
        fullDiv.innerHTML = fullDescription;
    }
    
    return col;
}

// Fungsi untuk membuka Google Maps dengan alamat FP Store
function showAddress() {
    const address = "Jl. Raya Pahlawan No.29, Cinangka, Kec. Sawangan, Kota Depok, Jawa Barat 16515, Indonesia";
    const encodedAddress = encodeURIComponent(address);
    
    // Deteksi jika mobile device
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    let mapsUrl;
    if (isMobile) {
        // Untuk mobile, coba buka aplikasi Maps dulu, fallback ke web
        mapsUrl = `https://maps.google.com/?q=${encodedAddress}`;
    } else {
        // Untuk desktop, buka Google Maps web
        mapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodedAddress}`;
    }
    
    // Buka Google Maps di tab baru
    window.open(mapsUrl, '_blank');
}

// Event listener ketika DOM sudah siap
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing FP Store...');
    
    // Load pengaturan dari database terlebih dahulu
    loadSettings().then(() => {
        console.log('Settings loaded, now loading products...');
        
        // Setelah pengaturan dimuat, baru load produk
        const currentPage = window.location.pathname.split('/').pop();
        console.log('Current page:', currentPage);
        
        if (currentPage === 'index.php' || currentPage === 'index.html' || currentPage === '') {
            console.log('Loading products for homepage...');
            // Halaman home - cek total produk dulu, lalu tampilkan 6 pertama
            loadProducts().then(allProducts => {
                console.log('All products loaded:', allProducts.length);
                const totalProducts = allProducts.length;
                const displayedProducts = allProducts.slice(0, 6);
                console.log('Displaying products:', displayedProducts.length);
                displayProducts(displayedProducts, 'produk-unggulan');
                handleViewMoreButton(totalProducts);
            }).catch(error => {
                console.error('Error loading products for homepage:', error);
            });
        } else if (currentPage === 'produk.html') {
            console.log('Loading products for product page...');
            // Initialize category filtering for products page
            initializeCategoryFiltering();
        }
    }).catch(error => {
        console.error('Error loading settings:', error);
        // Fallback: load products anyway
        const currentPage = window.location.pathname.split('/').pop();
        if (currentPage === 'index.php' || currentPage === 'index.html' || currentPage === '') {
            loadProducts().then(allProducts => {
                const totalProducts = allProducts.length;
                const displayedProducts = allProducts.slice(0, 6);
                displayProducts(displayedProducts, 'produk-unggulan');
                handleViewMoreButton(totalProducts);
            });
        } else if (currentPage === 'produk.html') {
            // Initialize category filtering for products page
            initializeCategoryFiltering();
        }
    });
    
    // Smooth scrolling untuk anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('shadow');
        } else {
            navbar.classList.remove('shadow');
        }
    });
});

// Fungsi untuk memuat pengaturan dari database
async function loadSettings() {
    try {
        console.log('Loading settings from API...');
        const response = await fetch('api/get_settings.php');
        const data = await response.json();
        
        console.log('Settings API response:', data);
        
        if (data.success && data.settings) {
            // Update konfigurasi WhatsApp
            if (data.settings.whatsapp_link) {
                WHATSAPP_CONFIG.link = data.settings.whatsapp_link;
                console.log('WhatsApp link updated:', WHATSAPP_CONFIG.link);
            }
            if (data.settings.whatsapp_channel) {
                WHATSAPP_CONFIG.channel = data.settings.whatsapp_channel;
                console.log('WhatsApp channel updated:', WHATSAPP_CONFIG.channel);
            }
            
            // Update nomor telepon di halaman kontak
            if (data.settings.site_phone) {
                const phoneElement = document.getElementById('phone-number');
                if (phoneElement) {
                    phoneElement.textContent = data.settings.site_phone;
                    console.log('Phone number updated on page:', data.settings.site_phone);
                } else {
                    console.log('Phone number element not found on this page');
                }
            }
            
            // Update link WhatsApp di halaman jika ada
            updateWhatsAppLinks();
            
            console.log('Settings loaded successfully:', data.settings);
        } else {
            console.error('Settings API failed:', data.message);
        }
    } catch (error) {
        console.error('Error loading settings:', error);
        // Gunakan konfigurasi default jika gagal load
    }
}

// Fungsi untuk update link WhatsApp di halaman
function updateWhatsAppLinks() {
    // Update link WhatsApp umum
    const generalWhatsAppLinks = document.querySelectorAll('a[href*="wa.me"]');
    generalWhatsAppLinks.forEach(link => {
        const currentHref = link.getAttribute('href');
        if (currentHref.includes('wa.me/')) {
            // Ambil pesan dari link lama jika ada
            const urlParams = new URLSearchParams(currentHref.split('?')[1] || '');
            const message = urlParams.get('text') || '';
            
            // Buat link baru
            const separator = WHATSAPP_CONFIG.link.includes('?') ? '&' : '?';
            const newHref = message ? 
                `${WHATSAPP_CONFIG.link}${separator}text=${encodeURIComponent(message)}` : 
                WHATSAPP_CONFIG.link;
            
            link.setAttribute('href', newHref);
        }
    });
    
    // Update link channel WhatsApp
    const channelLinks = document.querySelectorAll('a[href*="whatsapp.com/channel"]');
    channelLinks.forEach(link => {
        link.setAttribute('href', WHATSAPP_CONFIG.channel);
    });
}

// Fungsi utility untuk format tanggal
function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        timeZone: 'Asia/Jakarta'
    };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

// Fungsi untuk menampilkan notifikasi
function showNotification(message, type = 'info') {
    // Buat elemen notifikasi
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 100px; right: 20px; z-index: 9999; min-width: 300px;';
    
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove setelah 5 detik
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Fungsi untuk membuka email client dengan template
function openEmail() {
    const email = 'fpstore@gmail.com';
    const subject = 'Pertanyaan tentang Produk FP Store';
    const body = `Halo FP Store,

Saya ingin bertanya tentang produk Anda.

Terima kasih.`;
    
    const encodedSubject = encodeURIComponent(subject);
    const encodedBody = encodeURIComponent(body);
    const mailtoUrl = `mailto:${email}?subject=${encodedSubject}&body=${encodedBody}`;
    
    // Buka email client
    window.location.href = mailtoUrl;
}

// Cache Busting Functions
function forceCacheRefresh() {
    // Force refresh dengan bypass cache
    if (confirm('Refresh halaman untuk melihat update terbaru?')) {
        // Hard refresh - bypass cache
        window.location.reload(true);
    }
}

function checkForUpdates() {
    // Cek apakah ada update dengan menambahkan timestamp ke request
    const timestamp = new Date().getTime();
    const testUrl = `api/get_products.php?cache_bust=${timestamp}`;
    
    fetch(testUrl)
        .then(response => response.json())
        .then(data => {
            console.log('Update check completed at:', new Date().toLocaleTimeString());
        })
        .catch(error => {
            console.log('Update check failed:', error);
        });
}

// Auto check for updates setiap 5 menit (untuk development)
if (window.location.hostname === 'localhost' || window.location.hostname.includes('127.0.0.1')) {
    setInterval(checkForUpdates, 300000); // 5 menit
}

// Export fungsi untuk digunakan di halaman lain
window.createWhatsAppLink = createWhatsAppLink;
window.showAddress = showAddress;
window.openEmail = openEmail;
window.loadSettings = loadSettings;
window.toggleDescription = toggleDescription;
window.forceCacheRefresh = forceCacheRefresh;

// Fungsi untuk toggle deskripsi lengkap/singkat (pastikan tersedia secara global)
// Fungsi untuk toggle deskripsi lengkap/singkat (pastikan tersedia secara global)
function toggleDescription(productId) {
    console.log('toggleDescription called for:', productId);
    
    const shortDiv = document.getElementById(`${productId}-short`);
    const fullDiv = document.getElementById(`${productId}-full`);
    const toggleBtn = document.getElementById(`${productId}-toggle`);
    
    console.log('Elements found:', {
        shortDiv: !!shortDiv,
        fullDiv: !!fullDiv,
        toggleBtn: !!toggleBtn
    });
    
    if (shortDiv && fullDiv && toggleBtn) {
        const isFullHidden = !fullDiv.classList.contains('show');
        console.log('Current state - full description hidden:', isFullHidden);
        
        if (isFullHidden) {
            // Show full description, hide short
            shortDiv.style.display = 'none';
            fullDiv.classList.add('show');
            toggleBtn.innerHTML = '<small>Lihat Ringkasan <i class="fas fa-chevron-up ms-1"></i></small>';
            console.log('Switched to full description');
        } else {
            // Show short description, hide full
            fullDiv.classList.remove('show');
            shortDiv.style.display = 'block';
            toggleBtn.innerHTML = '<small>Lihat Detail <i class="fas fa-chevron-down ms-1"></i></small>';
            console.log('Switched to short description');
        }
    } else {
        console.error('Toggle elements not found for product:', productId);
        console.error('Missing elements:', {
            shortDiv: !shortDiv ? 'missing' : 'found',
            fullDiv: !fullDiv ? 'missing' : 'found', 
            toggleBtn: !toggleBtn ? 'missing' : 'found'
        });
    }
}

// Make sure it's available globally
window.toggleDescription = toggleDescription;

// Admin Access - Hidden shortcut (Ctrl+Shift+A)
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.shiftKey && e.key === 'A') {
        e.preventDefault();
        window.open('admin/login.php', '_blank');
    }
});

// Simple Dark Mode Toggle - More Reliable
function initDarkMode() {
    // Get saved theme or default to light
    const savedTheme = localStorage.getItem('theme') || 'light';
    
    // Apply theme immediately
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    // Update all toggle button icons
    updateToggleIcons(savedTheme);
    
    // Add click listeners to all toggle buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.theme-toggle')) {
            e.preventDefault();
            toggleDarkMode();
        }
    });
}

function toggleDarkMode() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    
    // Apply new theme
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    
    // Update button icons
    updateToggleIcons(newTheme);
    
    // Button animation
    const toggleBtn = document.querySelector('.theme-toggle');
    if (toggleBtn) {
        toggleBtn.style.transform = 'scale(0.95)';
        setTimeout(() => {
            toggleBtn.style.transform = 'scale(1)';
        }, 100);
    }
    
    console.log('Theme switched to:', newTheme);
}

function updateToggleIcons(theme) {
    const toggleButtons = document.querySelectorAll('.theme-toggle i');
    toggleButtons.forEach(icon => {
        icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    });
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initDarkMode);

// Also initialize immediately in case DOM is already ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDarkMode);
} else {
    initDarkMode();
}
// Global variables for category filtering and sorting
let allProducts = [];
let allCategories = [];
let filteredProducts = [];
let currentCategory = 'all';
let currentSort = 'newest';

// Fungsi untuk memuat kategori dari database
async function loadCategories() {
    try {
        console.log('Loading categories...');
        
        const response = await fetch('api/get_categories.php');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        console.log('Categories API response:', data);
        
        if (data.success) {
            console.log('Categories loaded successfully:', data.categories.length, 'items');
            allCategories = data.categories;
            return data.categories;
        } else {
            console.error('Error loading categories:', data.message);
            return [];
        }
    } catch (error) {
        console.error('Error fetching categories:', error);
        return [];
    }
}

// Fungsi untuk menampilkan filter kategori
function displayCategoryFilter(categories) {
    const filterContainer = document.getElementById('category-filter');
    
    if (!filterContainer) {
        console.log('Category filter container not found');
        return;
    }
    
    // Clear existing buttons except "Semua Produk"
    const allButton = filterContainer.querySelector('[data-category="all"]');
    filterContainer.innerHTML = '';
    filterContainer.appendChild(allButton);
    
    // Add category buttons
    categories.forEach(category => {
        const button = document.createElement('button');
        button.className = 'btn btn-outline-primary';
        button.setAttribute('data-category', category.id);
        button.innerHTML = `<i class="${category.icon} me-2"></i>${category.nama_kategori} <span class="badge bg-secondary ms-1">${category.product_count}</span>`;
        
        button.addEventListener('click', () => filterProductsByCategory(category.id));
        
        filterContainer.appendChild(button);
    });
    
    // Add event listener for "Semua Produk" button
    allButton.addEventListener('click', () => filterProductsByCategory('all'));
}

// Fungsi untuk filter produk berdasarkan kategori
function filterProductsByCategory(categoryId) {
    console.log('Filtering products by category:', categoryId);
    console.log('All products:', allProducts.length);
    console.log('Sample product category_id:', allProducts[0]?.category_id);
    
    currentCategory = categoryId;
    
    // Update active button
    const filterButtons = document.querySelectorAll('#category-filter button');
    filterButtons.forEach(btn => btn.classList.remove('active'));
    
    const activeButton = document.querySelector(`[data-category="${categoryId}"]`);
    if (activeButton) {
        activeButton.classList.add('active');
    }
    
    // Filter products
    if (categoryId === 'all') {
        filteredProducts = [...allProducts];
        console.log('Showing all products:', filteredProducts.length);
    } else {
        filteredProducts = allProducts.filter(product => {
            const matches = product.category_id && product.category_id == categoryId;
            console.log(`Product "${product.nama_produk}" (category_id: ${product.category_id}) matches category ${categoryId}:`, matches);
            return matches;
        });
        console.log('Filtered products for category', categoryId, ':', filteredProducts.length);
    }
    
    // Apply current sorting
    sortProducts(currentSort);
    
    // Update product count
    updateProductCount();
    
    // Show/hide clear filters button
    updateClearFiltersButton();
    
    console.log('Final filtered products:', filteredProducts.length);
    
    // Display filtered products
    displayProducts(filteredProducts, 'produk-container');
}

// Fungsi untuk sorting produk
function sortProducts(sortType) {
    console.log('Sorting products by:', sortType);
    
    currentSort = sortType;
    
    switch (sortType) {
        case 'newest':
            filteredProducts.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            break;
        case 'oldest':
            filteredProducts.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
            break;
        case 'name-asc':
            filteredProducts.sort((a, b) => a.nama_produk.localeCompare(b.nama_produk));
            break;
        case 'name-desc':
            filteredProducts.sort((a, b) => b.nama_produk.localeCompare(a.nama_produk));
            break;
        default:
            console.warn('Unknown sort type:', sortType);
    }
    
    // Display sorted products
    displayProducts(filteredProducts, 'produk-container');
}

// Fungsi untuk update product count display
function updateProductCount() {
    const countElement = document.getElementById('product-count');
    if (!countElement) return;
    
    const totalProducts = allProducts.length;
    const displayedProducts = filteredProducts.length;
    
    let countText = '';
    if (currentCategory === 'all') {
        countText = `<i class="fas fa-box me-2"></i>Menampilkan ${displayedProducts} produk`;
    } else {
        const categoryName = allCategories.find(cat => cat.id == currentCategory)?.nama_kategori || 'Kategori';
        countText = `<i class="fas fa-box me-2"></i>Menampilkan ${displayedProducts} produk dari kategori "${categoryName}"`;
    }
    
    countElement.innerHTML = countText;
}

// Fungsi untuk update clear filters button
function updateClearFiltersButton() {
    const clearButton = document.getElementById('clear-filters');
    if (!clearButton) return;
    
    if (currentCategory !== 'all' || currentSort !== 'newest') {
        clearButton.style.display = 'inline-block';
    } else {
        clearButton.style.display = 'none';
    }
}

// Fungsi untuk reset semua filter
function resetAllFilters() {
    console.log('Resetting all filters...');
    
    // Reset to default values
    currentCategory = 'all';
    currentSort = 'newest';
    
    // Update UI
    document.getElementById('sort-select').value = 'newest';
    
    // Apply filters
    filterProductsByCategory('all');
}

// Initialize category filtering and sorting on produk.html
async function initializeCategoryFiltering() {
    if (window.location.pathname.includes('produk.html')) {
        console.log('Initializing category filtering and sorting...');
        
        // Load categories and products
        const [categories, products] = await Promise.all([
            loadCategories(),
            loadProducts()
        ]);
        
        console.log('Loaded data:', {
            categories: categories.length,
            products: products.length,
            sampleProduct: products[0]
        });
        
        allProducts = products;
        filteredProducts = [...products];
        
        // Display category filter
        displayCategoryFilter(categories);
        
        // Setup sorting event listener
        const sortSelect = document.getElementById('sort-select');
        if (sortSelect) {
            sortSelect.addEventListener('change', (e) => {
                sortProducts(e.target.value);
            });
        }
        
        // Setup clear filters button
        const clearButton = document.getElementById('clear-filters');
        if (clearButton) {
            clearButton.addEventListener('click', resetAllFilters);
        }
        
        // Apply initial sorting and display
        sortProducts('newest');
        updateProductCount();
        updateClearFiltersButton();
        
        console.log('Category filtering initialized successfully');
    }
}