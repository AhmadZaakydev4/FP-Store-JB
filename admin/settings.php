<?php
require_once 'includes/session_check.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'whatsapp_link' => trim($_POST['whatsapp_link']),
        'whatsapp_channel' => trim($_POST['whatsapp_channel']),
        'site_name' => trim($_POST['site_name']),
        'site_email' => trim($_POST['site_email']),
        'site_phone' => trim($_POST['site_phone']),
        'site_address' => trim($_POST['site_address'])
    ];
    
    try {
        foreach ($settings as $key => $value) {
            $query = "UPDATE settings SET setting_value = :value WHERE setting_key = :key";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':key', $key);
            $stmt->execute();
        }
        
        $message = 'Pengaturan berhasil disimpan!';
        $message_type = 'success';
        
    } catch(Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'danger';
    }
}

// Ambil pengaturan saat ini
try {
    $query = "SELECT setting_key, setting_value FROM settings";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $settings_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch(Exception $e) {
    $settings_data = [];
    $message = 'Error mengambil data pengaturan: ' . $e->getMessage();
    $message_type = 'danger';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Admin Toko Online</title>
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
                        <h2 class="fw-bold">Pengaturan Website</h2>
                        <div class="text-muted">
                            <i class="fas fa-cog me-2"></i>
                            Konfigurasi
                        </div>
                    </div>
                    
                    <!-- Alert Messages -->
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Settings Form -->
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card admin-card">
                                <div class="card-header">
                                    <h5 class="mb-0 fw-bold">
                                        <i class="fas fa-cog me-2"></i>Pengaturan Umum
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="">
                                        <!-- WhatsApp Settings -->
                                        <div class="mb-4">
                                            <h6 class="fw-bold text-success mb-3">
                                                <i class="fab fa-whatsapp me-2"></i>Pengaturan WhatsApp
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="whatsapp_link" class="admin-form-label">Link WhatsApp Chat *</label>
                                                    <input type="url" class="form-control admin-form-control" id="whatsapp_link" 
                                                           name="whatsapp_link" placeholder="https://wa.me/628123456789" required
                                                           value="<?php echo htmlspecialchars($settings_data['whatsapp_link'] ?? ''); ?>">
                                                    <div class="form-text">Format: https://wa.me/628123456789</div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="whatsapp_channel" class="admin-form-label">Link Channel WhatsApp</label>
                                                    <input type="url" class="form-control admin-form-control" id="whatsapp_channel" 
                                                           name="whatsapp_channel" placeholder="https://whatsapp.com/channel/..."
                                                           value="<?php echo htmlspecialchars($settings_data['whatsapp_channel'] ?? ''); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <hr>
                                        
                                        <!-- Site Settings -->
                                        <div class="mb-4">
                                            <h6 class="fw-bold text-primary mb-3">
                                                <i class="fas fa-globe me-2"></i>Informasi Website
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="site_name" class="admin-form-label">Nama Toko/Website *</label>
                                                    <input type="text" class="form-control admin-form-control" id="site_name" 
                                                           name="site_name" required
                                                           value="<?php echo htmlspecialchars($settings_data['site_name'] ?? ''); ?>">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="site_email" class="admin-form-label">Email Kontak</label>
                                                    <input type="email" class="form-control admin-form-control" id="site_email" 
                                                           name="site_email" placeholder="info@tokoonline.com"
                                                           value="<?php echo htmlspecialchars($settings_data['site_email'] ?? ''); ?>">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="site_phone" class="admin-form-label">Nomor Telepon</label>
                                                    <input type="text" class="form-control admin-form-control" id="site_phone" 
                                                           name="site_phone" placeholder="+62 812-3456-7890"
                                                           value="<?php echo htmlspecialchars($settings_data['site_phone'] ?? ''); ?>">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="site_address" class="admin-form-label">Alamat Toko</label>
                                                    <textarea class="form-control admin-form-control" id="site_address" name="site_address" 
                                                              rows="3" placeholder="Alamat lengkap toko..."><?php echo htmlspecialchars($settings_data['site_address'] ?? ''); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex gap-3">
                                            <button type="submit" class="btn admin-btn admin-btn-primary">
                                                <i class="fas fa-save me-2"></i>Simpan Pengaturan
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="testWhatsApp()">
                                                <i class="fab fa-whatsapp me-2"></i>Test WhatsApp
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Info Panel -->
                        <div class="col-lg-4">
                            <div class="card admin-card border-0 shadow-sm">
                                <div class="card-header">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="fas fa-info-circle me-2"></i>Informasi
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Link WhatsApp:</strong><br>
                                        <span class="text-muted" id="current-whatsapp">
                                            <?php echo htmlspecialchars($settings_data['whatsapp_link'] ?? 'Belum diatur'); ?>
                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Format Pesan:</strong><br>
                                        <small class="text-muted">
                                            "Halo admin, saya tertarik dengan produk [nama_produk]. Apakah masih tersedia?"
                                        </small>
                                    </div>
                                    <div class="alert admin-alert admin-alert-info">
                                        <i class="fas fa-lightbulb me-2"></i>
                                        <strong>Tips:</strong><br>
                                        Setelah mengubah link WhatsApp, website akan otomatis menggunakan link baru untuk semua tombol chat.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card admin-card border-0 shadow-sm mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="fas fa-tools me-2"></i>Aksi Cepat
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <a href="../index.php" target="_blank" class="btn btn-outline-primary w-100 mb-2">
                                        <i class="fas fa-external-link-alt me-2"></i>Preview Website
                                    </a>
                                    <a href="products.php" class="btn btn-outline-success w-100">
                                        <i class="fas fa-box me-2"></i>Kelola Produk
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
    <script src="../assets/js/admin-sidebar-fix.js"></script>
    <script>
        function testWhatsApp() {
            const link = document.getElementById('whatsapp_link').value;
            if (link) {
                const message = encodeURIComponent('Test pesan dari Admin Panel');
                const url = link.includes('?') ? `${link}&text=${message}` : `${link}?text=${message}`;
                window.open(url, '_blank');
            } else {
                showAdminNotification('Masukkan link WhatsApp terlebih dahulu', 'warning');
            }
        }
        
        // Update current WhatsApp display when input changes
        document.getElementById('whatsapp_link').addEventListener('input', function() {
            document.getElementById('current-whatsapp').textContent = this.value || 'Belum diatur';
        });
    </script>
</body>
</html>