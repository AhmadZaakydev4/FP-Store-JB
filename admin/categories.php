<?php
require_once 'includes/session_check.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        // Add new category
        $nama_kategori = trim($_POST['nama_kategori']);
        $deskripsi = trim($_POST['deskripsi']);
        $icon = trim($_POST['icon']) ?: 'fas fa-folder';
        
        if (empty($nama_kategori)) {
            $message = 'Nama kategori harus diisi!';
            $message_type = 'danger';
        } else {
            try {
                $query = "INSERT INTO categories (nama_kategori, deskripsi, icon) VALUES (:nama_kategori, :deskripsi, :icon)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':nama_kategori', $nama_kategori);
                $stmt->bindParam(':deskripsi', $deskripsi);
                $stmt->bindParam(':icon', $icon);
                
                if ($stmt->execute()) {
                    $message = 'Kategori berhasil ditambahkan!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menambahkan kategori!';
                    $message_type = 'danger';
                }
            } catch (Exception $e) {
                $message = 'Error: ' . $e->getMessage();
                $message_type = 'danger';
            }
        }
    } elseif ($action === 'edit') {
        // Edit category
        $id = (int)$_POST['id'];
        $nama_kategori = trim($_POST['nama_kategori']);
        $deskripsi = trim($_POST['deskripsi']);
        $icon = trim($_POST['icon']) ?: 'fas fa-folder';
        
        if (empty($nama_kategori)) {
            $message = 'Nama kategori harus diisi!';
            $message_type = 'danger';
        } else {
            try {
                $query = "UPDATE categories SET nama_kategori = :nama_kategori, deskripsi = :deskripsi, icon = :icon WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':nama_kategori', $nama_kategori);
                $stmt->bindParam(':deskripsi', $deskripsi);
                $stmt->bindParam(':icon', $icon);
                $stmt->bindParam(':id', $id);
                
                if ($stmt->execute()) {
                    $message = 'Kategori berhasil diupdate!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal mengupdate kategori!';
                    $message_type = 'danger';
                }
            } catch (Exception $e) {
                $message = 'Error: ' . $e->getMessage();
                $message_type = 'danger';
            }
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    try {
        // Check if category has products
        $checkQuery = "SELECT COUNT(*) FROM products WHERE category_id = :id";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':id', $id);
        $checkStmt->execute();
        $productCount = $checkStmt->fetchColumn();
        
        if ($productCount > 0) {
            $message = "Tidak dapat menghapus kategori karena masih ada $productCount produk yang menggunakan kategori ini!";
            $message_type = 'warning';
        } else {
            $query = "DELETE FROM categories WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                $message = 'Kategori berhasil dihapus!';
                $message_type = 'success';
            } else {
                $message = 'Gagal menghapus kategori!';
                $message_type = 'danger';
            }
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'danger';
    }
}

// Get all categories with product count
$query = "SELECT c.*, COUNT(p.id) as product_count 
          FROM categories c 
          LEFT JOIN products p ON c.id = p.category_id 
          GROUP BY c.id 
          ORDER BY c.nama_kategori";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - FP Store Admin</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="admin-page-title">
                    <i class="fas fa-tags me-2"></i>
                    Kelola Kategori
                </h2>
                <p class="text-muted">Atur kategori produk untuk memudahkan navigasi customer</p>
            </div>
            <button type="button" class="btn admin-btn admin-btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="addCategory()">
                <i class="fas fa-plus me-2"></i>Tambah Kategori
            </button>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : ($message_type === 'warning' ? 'exclamation-triangle' : 'exclamation-circle'); ?> me-2"></i>
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card admin-card border-0 shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Daftar Kategori (<?php echo count($categories); ?>)
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($categories)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada kategori</h5>
                        <p class="text-muted">Klik tombol "Tambah Kategori" untuk menambah kategori pertama</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table admin-table">
                            <thead>
                                <tr>
                                    <th>Icon</th>
                                    <th>Nama Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah Produk</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td>
                                            <i class="<?php echo htmlspecialchars($category['icon']); ?> fa-lg text-primary"></i>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($category['nama_kategori']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                <?php echo htmlspecialchars(substr($category['deskripsi'], 0, 100)); ?>
                                                <?php if (strlen($category['deskripsi']) > 100): ?>...<?php endif; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo $category['product_count']; ?> produk
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('d/m/Y H:i', strtotime($category['created_at'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="editCategory(<?php echo htmlspecialchars(json_encode($category)); ?>)"
                                                        title="Edit Kategori">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if ($category['product_count'] == 0): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmDelete('<?php echo htmlspecialchars($category['nama_kategori']); ?>', 'categories.php?delete=<?php echo $category['id']; ?>')"
                                                            title="Hapus Kategori">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                            disabled
                                                            title="Tidak dapat dihapus karena masih ada produk">
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Tambah Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="formAction" value="add">
                        <input type="hidden" name="id" id="categoryId">
                        
                        <div class="mb-3">
                            <label for="nama_kategori" class="admin-form-label">Nama Kategori *</label>
                            <input type="text" class="form-control admin-form-control" id="nama_kategori" name="nama_kategori" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="admin-form-label">Deskripsi</label>
                            <textarea class="form-control admin-form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi kategori (opsional)"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="icon" class="admin-form-label">Icon (FontAwesome)</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i id="iconPreview" class="fas fa-folder"></i>
                                </span>
                                <input type="text" class="form-control admin-form-control" id="icon" name="icon" 
                                       placeholder="fas fa-gamepad" value="fas fa-folder"
                                       onkeyup="updateIconPreview()">
                            </div>
                            <div class="form-text">
                                Contoh: fas fa-gamepad, fas fa-mobile-alt, fas fa-share-alt
                                <br><a href="https://fontawesome.com/icons" target="_blank">Lihat icon lainnya</a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn admin-btn admin-btn-primary" id="submitBtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
    <script src="../assets/js/admin-sidebar-fix.js"></script>
    <script>
        function addCategory() {
            document.getElementById('modalTitle').textContent = 'Tambah Kategori';
            document.getElementById('formAction').value = 'add';
            document.getElementById('categoryId').value = '';
            document.getElementById('nama_kategori').value = '';
            document.getElementById('deskripsi').value = '';
            document.getElementById('icon').value = 'fas fa-folder';
            document.getElementById('submitBtn').textContent = 'Simpan';
            updateIconPreview();
        }
        
        function editCategory(category) {
            document.getElementById('modalTitle').textContent = 'Edit Kategori';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('categoryId').value = category.id;
            document.getElementById('nama_kategori').value = category.nama_kategori;
            document.getElementById('deskripsi').value = category.deskripsi || '';
            document.getElementById('icon').value = category.icon || 'fas fa-folder';
            document.getElementById('submitBtn').textContent = 'Update';
            updateIconPreview();
            
            new bootstrap.Modal(document.getElementById('categoryModal')).show();
        }
        
        function updateIconPreview() {
            const iconInput = document.getElementById('icon');
            const iconPreview = document.getElementById('iconPreview');
            const iconClass = iconInput.value || 'fas fa-folder';
            
            iconPreview.className = iconClass;
        }
    </script>
</body>
</html>