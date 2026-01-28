// Product Detail Page JavaScript

// Global variables
let currentProduct = null;
let relatedProducts = [];
let productViews = 0;

// Get product ID from URL parameters
function getProductIdFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}

// Load single product by ID
async function loadProductById(productId) {
    try {
        console.log('Loading product with ID:', productId);
        
        const response = await fetch(`api/get_products.php`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success && data.products) {
            const product = data.products.find(p => p.id == productId);
            
            if (product) {
                console.log('Product found:', product);
                return product;
            } else {
                console.error('Product not found with ID:', productId);
                return null;
            }
        } else {
            console.error('Error loading products:', data.message);
            return null;
        }
    } catch (error) {
        console.error('Error fetching product:', error);
        return null;
    }
}

// Load related products (same category, excluding current product)
async function loadRelatedProducts(currentProductId, categoryId, limit = 3) {
    try {
        const response = await fetch('api/get_products.php');
        const data = await response.json();
        
        if (data.success && data.products) {
            let related = data.products.filter(product => 
                product.id != currentProductId && 
                product.category_id == categoryId
            );
            
            // If not enough related products in same category, get random products
            if (related.length < limit) {
                const additional = data.products.filter(product => 
                    product.id != currentProductId && 
                    !related.find(r => r.id === product.id)
                ).slice(0, limit - related.length);
                
                related = [...related, ...additional];
            }
            
            return related.slice(0, limit);
        }
        
        return [];
    } catch (error) {
        console.error('Error loading related products:', error);
        return [];
    }
}

// Display product details
function displayProductDetail(product) {
    console.log('Displaying product detail:', product);
    
    // Update page title
    document.title = `${product.nama_produk} - FP Store`;
    document.getElementById('page-title').textContent = `${product.nama_produk} - FP Store`;
    
    // Update breadcrumb
    document.getElementById('breadcrumb-product').textContent = product.nama_produk;
    
    // Update main image
    const mainImage = document.getElementById('main-product-image');
    mainImage.src = product.foto;
    mainImage.alt = product.nama_produk;
    
    // Update category badge
    const categoryContainer = document.getElementById('category-badge-container');
    if (product.nama_kategori) {
        categoryContainer.innerHTML = `
            <span class="badge bg-primary fs-6">
                <i class="${product.category_icon || 'fas fa-tag'} me-2"></i>
                ${product.nama_kategori}
            </span>
        `;
    }
    
    // Update product title
    document.getElementById('product-title').textContent = product.nama_produk;
    
    // Update product date
    const createdDate = new Date(product.created_at).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
    document.getElementById('product-date').textContent = createdDate;
    
    // Update views (simulate views)
    productViews = Math.floor(Math.random() * 500) + 50;
    document.getElementById('product-views').textContent = productViews;
    
    // Update descriptions
    document.getElementById('product-short-description').innerHTML = product.deskripsi_singkat || 'Tidak ada deskripsi singkat';
    document.getElementById('product-full-description').innerHTML = product.deskripsi || 'Tidak ada deskripsi lengkap';
    
    // Update WhatsApp button
    const whatsappBtn = document.getElementById('whatsapp-btn');
    const whatsappLink = createWhatsAppLink(product.nama_produk);
    whatsappBtn.href = whatsappLink;
    
    // Setup share button
    setupShareButton(product);
    
    // Show the detail section
    document.getElementById('loading-detail').style.display = 'none';
    document.getElementById('product-detail-section').style.display = 'block';
    
    // Track view (save to localStorage for analytics)
    trackProductView(product.id);
}

// Display related products
function displayRelatedProducts(products) {
    const container = document.getElementById('related-products');
    container.innerHTML = '';
    
    if (products.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center text-muted">
                <p>Tidak ada produk terkait</p>
            </div>
        `;
        return;
    }
    
    products.forEach(product => {
        const productCard = createRelatedProductCard(product);
        container.appendChild(productCard);
    });
}

// Create related product card (smaller version)
function createRelatedProductCard(product) {
    const col = document.createElement('div');
    col.className = 'col-lg-4 col-md-6 mb-4';
    
    const whatsappLink = createWhatsAppLink(product.nama_produk);
    
    col.innerHTML = `
        <div class="card product-card h-100 shadow-sm">
            <div class="position-relative">
                <img src="${product.foto}" class="card-img-top product-image" alt="${product.nama_produk}" loading="lazy">
                ${product.nama_kategori ? `
                    <div class="position-absolute top-0 start-0 m-2">
                        <span class="badge bg-primary">
                            <i class="${product.category_icon || 'fas fa-tag'} me-1"></i>
                            ${product.nama_kategori}
                        </span>
                    </div>
                ` : ''}
            </div>
            <div class="card-body d-flex flex-column">
                <h6 class="card-title fw-bold">${product.nama_produk}</h6>
                <p class="card-text text-muted small flex-grow-1">
                    ${product.deskripsi_singkat ? product.deskripsi_singkat.substring(0, 80) + '...' : 'Tidak ada deskripsi'}
                </p>
                <div class="mt-auto">
                    <div class="row g-2">
                        <div class="col-8">
                            <a href="${whatsappLink}" target="_blank" class="btn btn-success btn-sm w-100">
                                <i class="fab fa-whatsapp me-1"></i>Pesan
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="detail-produk.php?id=${product.id}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    return col;
}

// Setup share button functionality
function setupShareButton(product) {
    const shareBtn = document.getElementById('share-btn');
    
    shareBtn.addEventListener('click', function() {
        const productUrl = window.location.href;
        const shareText = `Check out this product: ${product.nama_produk}`;
        
        if (navigator.share) {
            // Use native share API if available
            navigator.share({
                title: product.nama_produk,
                text: shareText,
                url: productUrl
            }).catch(console.error);
        } else {
            // Fallback: copy to clipboard
            navigator.clipboard.writeText(productUrl).then(() => {
                showNotification('Link produk berhasil disalin!', 'success');
            }).catch(() => {
                // Fallback: show share modal
                showShareModal(product, productUrl);
            });
        }
    });
}

// Track product view
function trackProductView(productId) {
    try {
        const views = JSON.parse(localStorage.getItem('fp_store_product_views') || '{}');
        views[productId] = (views[productId] || 0) + 1;
        localStorage.setItem('fp_store_product_views', JSON.stringify(views));
        
        // Also track in recently viewed
        trackRecentlyViewed(currentProduct);
    } catch (error) {
        console.error('Error tracking view:', error);
    }
}

// Track recently viewed products
function trackRecentlyViewed(product) {
    try {
        const recentlyViewed = JSON.parse(localStorage.getItem('fp_store_recently_viewed') || '[]');
        
        // Remove if already exists
        const filtered = recentlyViewed.filter(item => item.id !== product.id);
        
        // Add to beginning
        filtered.unshift({
            id: product.id,
            nama_produk: product.nama_produk,
            foto: product.foto,
            deskripsi_singkat: product.deskripsi_singkat,
            viewed_at: new Date().toISOString()
        });
        
        // Keep only last 10
        const limited = filtered.slice(0, 10);
        
        localStorage.setItem('fp_store_recently_viewed', JSON.stringify(limited));
    } catch (error) {
        console.error('Error tracking recently viewed:', error);
    }
}

// Show share modal (fallback)
function showShareModal(product, url) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bagikan Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bagikan produk "${product.nama_produk}" melalui:</p>
                    <div class="d-grid gap-2">
                        <a href="https://wa.me/?text=${encodeURIComponent(`Check out: ${product.nama_produk} - ${url}`)}" 
                           target="_blank" class="btn btn-success">
                            <i class="fab fa-whatsapp me-2"></i>WhatsApp
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}" 
                           target="_blank" class="btn btn-primary">
                            <i class="fab fa-facebook me-2"></i>Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=${encodeURIComponent(`Check out: ${product.nama_produk}`)}&url=${encodeURIComponent(url)}" 
                           target="_blank" class="btn btn-info">
                            <i class="fab fa-twitter me-2"></i>Twitter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    modal.addEventListener('hidden.bs.modal', () => {
        document.body.removeChild(modal);
    });
}

// Show error state
function showErrorState() {
    document.getElementById('loading-detail').style.display = 'none';
    document.getElementById('error-state').style.display = 'block';
}

// Initialize product detail page
async function initializeProductDetail() {
    console.log('Initializing product detail page...');
    
    const productId = getProductIdFromURL();
    
    if (!productId) {
        console.error('No product ID provided');
        showErrorState();
        return;
    }
    
    try {
        // Load settings first
        await loadSettings();
        
        // Load product
        const product = await loadProductById(productId);
        
        if (!product) {
            showErrorState();
            return;
        }
        
        currentProduct = product;
        
        // Display product detail
        displayProductDetail(product);
        
        // Load and display related products
        if (product.category_id) {
            const related = await loadRelatedProducts(product.id, product.category_id);
            relatedProducts = related;
            displayRelatedProducts(related);
        }
        
    } catch (error) {
        console.error('Error initializing product detail:', error);
        showErrorState();
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing product detail...');
    initializeProductDetail();
});

// Export functions for global access
window.initializeProductDetail = initializeProductDetail;
window.getProductIdFromURL = getProductIdFromURL;