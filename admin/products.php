<?php
require_once 'includes/session_check.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get all categories for dropdown
$categoriesQuery = "SELECT id, nama_kategori FROM categories ORDER BY nama_kategori";
$categoriesStmt = $db->prepare($categoriesQuery);
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'toggle_status') {
        // Toggle status produk
        $id = (int)$_POST['id'];
        $new_status = (int)$_POST['status'];
        
        try {
            $query = "UPDATE products SET is_active = :status WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':status', $new_status);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                $status_text = $new_status ? 'diaktifkan' : 'dinonaktifkan';
                $message = "Produk berhasil {$status_text}!";
                $message_type = 'success';
            } else {
                $message = 'Gagal mengubah status produk!';
                $message_type = 'danger';
            }
        } catch(Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $message_type = 'danger';
        }
    } elseif ($action === 'add') {
        // Tambah produk baru
        $nama_produk = trim($_POST['nama_produk']);
        $deskripsi_singkat = trim($_POST['deskripsi_singkat']);
        $deskripsi = trim($_POST['deskripsi']);
        $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        
        if (empty($nama_produk) || empty($deskripsi_singkat) || empty($deskripsi)) {
            $message = 'Nama produk, deskripsi singkat, dan deskripsi lengkap harus diisi!';
            $message_type = 'danger';
        } else {
            // Handle upload gambar
            $foto_path = '';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $foto_path = handleImageUpload($_FILES['foto']);
                if (!$foto_path) {
                    $message = 'Gagal mengupload gambar!';
                    $message_type = 'danger';
                }
            } else {
                $foto_path = 'assets/images/no-image.jpg';
            }
            
            if ($foto_path) {
                try {
                    $query = "INSERT INTO products (nama_produk, deskripsi_singkat, deskripsi, foto, category_id) VALUES (:nama_produk, :deskripsi_singkat, :deskripsi, :foto, :category_id)";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':nama_produk', $nama_produk);
                    $stmt->bindParam(':deskripsi_singkat', $deskripsi_singkat);
                    $stmt->bindParam(':deskripsi', $deskripsi);
                    $stmt->bindParam(':foto', $foto_path);
                    $stmt->bindParam(':category_id', $category_id);
                    
                    if ($stmt->execute()) {
                        $message = 'Produk berhasil ditambahkan!';
                        $message_type = 'success';
                    } else {
                        $message = 'Gagal menambahkan produk!';
                        $message_type = 'danger';
                    }
                } catch(Exception $e) {
                    $message = 'Error: ' . $e->getMessage();
                    $message_type = 'danger';
                }
            }
        }
    } elseif ($action === 'edit') {
        // Edit produk
        $id = (int)$_POST['id'];
        $nama_produk = trim($_POST['nama_produk']);
        $deskripsi_singkat = trim($_POST['deskripsi_singkat']);
        $deskripsi = trim($_POST['deskripsi']);
        $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        
        if (empty($nama_produk) || empty($deskripsi_singkat) || empty($deskripsi)) {
            $message = 'Nama produk, deskripsi singkat, dan deskripsi lengkap harus diisi!';
            $message_type = 'danger';
        } else {
            try {
                // Ambil data produk lama
                $query = "SELECT foto FROM products WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $old_product = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $foto_path = $old_product['foto'];
                
                // Handle upload gambar baru
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                    $new_foto_path = handleImageUpload($_FILES['foto']);
                    if ($new_foto_path) {
                        // Hapus gambar lama jika bukan default
                        if ($old_product['foto'] !== 'assets/images/no-image.jpg' && file_exists('../' . $old_product['foto'])) {
                            unlink('../' . $old_product['foto']);
                        }
                        $foto_path = $new_foto_path;
                    }
                }
                
                // Update produk
                $query = "UPDATE products SET nama_produk = :nama_produk, deskripsi_singkat = :deskripsi_singkat, deskripsi = :deskripsi, foto = :foto, category_id = :category_id WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':nama_produk', $nama_produk);
                $stmt->bindParam(':deskripsi_singkat', $deskripsi_singkat);
                $stmt->bindParam(':deskripsi', $deskripsi);
                $stmt->bindParam(':foto', $foto_path);
                $stmt->bindParam(':category_id', $category_id);
                $stmt->bindParam(':id', $id);
                
                if ($stmt->execute()) {
                    $message = 'Produk berhasil diupdate!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal mengupdate produk!';
                    $message_type = 'danger';
                }
            } catch(Exception $e) {
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
        // Ambil data produk untuk hapus gambar
        $query = "SELECT foto FROM products WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Hapus produk dari database
        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            // Hapus file gambar jika bukan default
            if ($product && $product['foto'] !== 'assets/images/no-image.jpg' && file_exists('../' . $product['foto'])) {
                unlink('../' . $product['foto']);
            }
            $message = 'Produk berhasil dihapus!';
            $message_type = 'success';
        } else {
            $message = 'Gagal menghapus produk!';
            $message_type = 'danger';
        }
    } catch(Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'danger';
    }
}

// Ambil semua produk dengan kategori
try {
    $query = "SELECT p.*, c.nama_kategori, c.icon as category_icon 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              ORDER BY p.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    $products = [];
    $message = 'Error mengambil data produk: ' . $e->getMessage();
    $message_type = 'danger';
}

// Fungsi untuk handle upload gambar
function handleImageUpload($file) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }
    
    if ($file['size'] > $max_size) {
        return false;
    }
    
    $upload_dir = '../assets/images/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return 'assets/images/' . $new_filename;
    }
    
    return false;
}

// Get product for edit
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    try {
        $query = "SELECT * FROM products WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $edit_id);
        $stmt->execute();
        $edit_product = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(Exception $e) {
        $edit_product = null;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin Toko Online</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <link href="../assets/css/admin-sidebar-fix.css" rel="stylesheet">
    <!-- Quill Editor CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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
                        <h2 class="fw-bold">Kelola Produk</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                            <i class="fas fa-plus me-2"></i>Tambah Produk
                        </button>
                    </div>
                    
                    <!-- Alert Messages -->
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Products Table -->
                    <div class="card admin-card border-0 shadow-sm">
                        <div class="card-body">
                            <?php if (empty($products)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <h4>Belum ada produk</h4>
                                    <p class="text-muted">Klik tombol "Tambah Produk" untuk menambahkan produk pertama</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table admin-table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Gambar</th>
                                                <th>Nama Produk</th>
                                                <th>Kategori</th>
                                                <th>Deskripsi</th>
                                                <th>Status</th>
                                                <th>Tanggal</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products as $product): ?>
                                                <tr>
                                                    <td>
                                                        <img src="../<?php echo htmlspecialchars($product['foto']); ?>" 
                                                             alt="<?php echo htmlspecialchars($product['nama_produk']); ?>" 
                                                             class="product-image">
                                                    </td>
                                                    <td class="fw-semibold"><?php echo htmlspecialchars($product['nama_produk']); ?></td>
                                                    <td>
                                                        <?php if ($product['nama_kategori']): ?>
                                                            <span class="badge bg-primary">
                                                                <i class="<?php echo htmlspecialchars($product['category_icon']); ?> me-1"></i>
                                                                <?php echo htmlspecialchars($product['nama_kategori']); ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Tanpa Kategori</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted">
                                                            <?php 
                                                            // Strip HTML tags and truncate for table display
                                                            $plainText = strip_tags($product['deskripsi']);
                                                            echo strlen($plainText) > 100 ? 
                                                                substr(htmlspecialchars($plainText), 0, 100) . '...' : 
                                                                htmlspecialchars($plainText); 
                                                            ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   id="status_<?php echo $product['id']; ?>"
                                                                   <?php echo ($product['is_active'] ?? 1) ? 'checked' : ''; ?>
                                                                   onchange="toggleProductStatus(<?php echo $product['id']; ?>, this.checked)">
                                                            <label class="form-check-label" for="status_<?php echo $product['id']; ?>">
                                                                <span class="badge <?php echo ($product['is_active'] ?? 1) ? 'bg-success' : 'bg-secondary'; ?>" 
                                                                      id="badge_<?php echo $product['id']; ?>">
                                                                    <?php echo ($product['is_active'] ?? 1) ? 'Aktif' : 'Nonaktif'; ?>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="text-muted">
                                                        <?php echo date('d/m/Y', strtotime($product['created_at'])); ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary me-1" 
                                                                onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" 
                                                                onclick="confirmDelete('<?php echo htmlspecialchars($product['nama_produk']); ?>', 'products.php?delete=<?php echo $product['id']; ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
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
            </div>
        </div>
    </div>

    <!-- Product Modal -->
    <div class="modal fade admin-modal" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="productForm" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" id="formAction" value="add">
                        <input type="hidden" name="id" id="productId">
                        
                        <div class="mb-3">
                            <label for="nama_produk" class="admin-form-label">Nama Produk *</label>
                            <input type="text" class="form-control admin-form-control" id="nama_produk" name="nama_produk" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="admin-form-label">Kategori</label>
                            <select class="form-select admin-form-control" id="category_id" name="category_id">
                                <option value="">Pilih Kategori (Opsional)</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['nama_kategori']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Pilih kategori untuk memudahkan customer menemukan produk</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi_singkat" class="admin-form-label">Deskripsi Singkat *</label>
                            <div id="quill-editor-short" style="height: 150px;"></div>
                            <textarea class="form-control d-none" id="deskripsi_singkat" name="deskripsi_singkat" required></textarea>
                            <div class="form-text">Deskripsi singkat yang akan ditampilkan di card produk (maksimal 2-3 baris)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="admin-form-label">Deskripsi Lengkap *</label>
                            <div id="quill-editor" style="height: 300px;"></div>
                            <textarea class="form-control d-none" id="deskripsi" name="deskripsi" required></textarea>
                            <div class="form-text">
                                <strong>Shortcut Keys:</strong> Ctrl+B (Bold), Ctrl+I (Italic), Ctrl+U (Underline), Shift+Enter (Line Break)<br>
                                <strong>Auto-format:</strong> **text** untuk bold, *text* untuk italic
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="foto" class="admin-form-label">Foto Produk</label>
                            <input type="file" class="form-control admin-form-control" id="foto" name="foto" accept="image/*">
                            <div class="form-text">Format: JPG, PNG, WEBP. Maksimal 5MB.</div>
                            <div id="currentImage" class="mt-2" style="display: none;">
                                <small class="text-muted">Gambar saat ini:</small><br>
                                <img id="currentImagePreview" src="" alt="Current Image" style="max-width: 200px; height: auto; border-radius: 8px;">
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
    <!-- Quill Editor JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <!-- Emoji Picker -->
    <script src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js" type="module"></script>
    <style>
        /* Emoji picker styling */
        .emoji-picker-container {
            position: relative;
            display: inline-block;
        }
        
        .emoji-button {
            background: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 5px 8px;
            cursor: pointer;
            font-size: 16px;
            margin-left: 5px;
        }
        
        .emoji-button:hover {
            background-color: #f0f0f0;
        }
        
        .emoji-picker {
            position: absolute;
            top: 100%;
            right: 0;
            z-index: 1000;
            background: white;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 10px;
            display: none;
            max-height: 300px;
            overflow-y: auto;
            width: 300px;
        }
        
        .emoji-picker.show {
            display: block;
        }
        
        .emoji-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 5px;
        }
        
        .emoji-item {
            background: none;
            border: none;
            font-size: 20px;
            padding: 5px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        
        .emoji-item:hover {
            background-color: #f0f0f0;
        }
        
        .emoji-category {
            font-weight: bold;
            margin: 10px 0 5px 0;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
        }
    </style>
    <script>
        // Initialize Enhanced Quill Editor with Word-like features
        var quill = new Quill('#quill-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    [{ 'font': [] }],
                    [{ 'size': ['small', false, 'large', 'huge'] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'script': 'sub'}, { 'script': 'super' }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'direction': 'rtl' }],
                    [{ 'align': [] }],
                    ['link', 'image', 'video'],
                    ['blockquote', 'code-block'],
                    ['clean'],
                    ['emoji'] // Custom emoji button
                ],
                keyboard: {
                    bindings: {
                        'bold': {
                            key: 'B',
                            ctrlKey: true,
                            handler: function(range, context) {
                                this.quill.format('bold', !context.format.bold);
                            }
                        },
                        'italic': {
                            key: 'I',
                            ctrlKey: true,
                            handler: function(range, context) {
                                this.quill.format('italic', !context.format.italic);
                            }
                        },
                        'underline': {
                            key: 'U',
                            ctrlKey: true,
                            handler: function(range, context) {
                                this.quill.format('underline', !context.format.underline);
                            }
                        },
                        'linebreak': {
                            key: 13,
                            shiftKey: true,
                            handler: function(range, context) {
                                this.quill.insertText(range.index, '\n');
                                this.quill.setSelection(range.index + 1);
                            }
                        }
                    }
                }
            },
            placeholder: 'Deskripsi lengkap produk dengan format HTML...',
            formats: [
                'header', 'font', 'size',
                'bold', 'italic', 'underline', 'strike',
                'color', 'background',
                'script',
                'list', 'bullet', 'indent',
                'direction', 'align',
                'link', 'image', 'video',
                'blockquote', 'code-block'
            ]
        });
        
        // Initialize Short Description Editor (simpler toolbar)
        var quillShort = new Quill('#quill-editor-short', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'color': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['clean'],
                    ['emoji'] // Add emoji button to short editor too
                ]
            },
            placeholder: 'Deskripsi singkat untuk tampilan di card produk...',
            formats: ['bold', 'italic', 'underline', 'color', 'list', 'bullet']
        });
        
        // Auto-sync content for both editors
        quill.on('text-change', function() {
            document.getElementById('deskripsi').value = quill.root.innerHTML;
        });
        
        quillShort.on('text-change', function() {
            document.getElementById('deskripsi_singkat').value = quillShort.root.innerHTML;
        });
        
        // Add emoji support to short description editor
        const toolbarShort = quillShort.getModule('toolbar');
        toolbarShort.addHandler('emoji', function() {
            showEmojiPickerShort();
        });
        
        // Add emoji button styling for short editor
        const emojiButtonShort = document.querySelectorAll('.ql-emoji')[1]; // Second emoji button
        if (emojiButtonShort) {
            emojiButtonShort.innerHTML = '😀';
            emojiButtonShort.title = 'Insert Emoji';
        }
        
        function showEmojiPickerShort() {
            let picker = document.getElementById('emojiPickerShort');
            if (!picker) {
                createEmojiPickerShort();
                picker = document.getElementById('emojiPickerShort');
            }
            
            // Position picker near the emoji button
            const emojiButton = document.querySelectorAll('.ql-emoji')[1];
            const rect = emojiButton.getBoundingClientRect();
            picker.style.position = 'fixed';
            picker.style.top = (rect.bottom + 5) + 'px';
            picker.style.left = (rect.left - 250) + 'px';
            
            picker.classList.toggle('show');
        }
        
        function createEmojiPickerShort() {
            const picker = document.createElement('div');
            picker.id = 'emojiPickerShort';
            picker.className = 'emoji-picker';
            
            let html = '<div class="emoji-category">Pilih Emoji untuk Deskripsi Singkat:</div><div class="emoji-grid">';
            // Use a smaller set for short description
            const shortEmojiList = ['😀', '😃', '😄', '😍', '🥰', '😎', '🤩', '👍', '👎', '❤️', '💚', '💙', '🔥', '⭐', '✨', '🎉', '🎮', '🎯', '🏆', '💎', '🚀', '💯', '🔝', '👑'];
            shortEmojiList.forEach(emoji => {
                html += `<button type="button" class="emoji-item" onclick="insertEmojiShort('${emoji}')">${emoji}</button>`;
            });
            html += '</div>';
            
            picker.innerHTML = html;
            document.body.appendChild(picker);
            
            // Close picker when clicking outside
            document.addEventListener('click', function(e) {
                if (!picker.contains(e.target) && !e.target.closest('.ql-emoji')) {
                    picker.classList.remove('show');
                }
            });
        }
        
        function insertEmojiShort(emoji) {
            const range = quillShort.getSelection();
            if (range) {
                quillShort.insertText(range.index, emoji);
                quillShort.setSelection(range.index + emoji.length);
            } else {
                quillShort.insertText(quillShort.getLength() - 1, emoji);
            }
            
            // Hide picker
            document.getElementById('emojiPickerShort').classList.remove('show');
            
            // Update hidden textarea
            document.getElementById('deskripsi_singkat').value = quillShort.root.innerHTML;
        }
        
        // Make functions global
        window.insertEmojiShort = insertEmojiShort;
        
        // Real-time formatting preview
        quill.on('selection-change', function(range, oldRange, source) {
            if (range) {
                // Show current formatting in console for debugging
                const format = quill.getFormat(range);
                console.log('Current format:', format);
            }
        });
        
        // Add Emoji Picker Functionality
        const emojiList = [
            // Smileys & Emotion
            '😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂', '🙂', '🙃', '😉', '😊', '😇', '🥰', '😍', '🤩',
            '😘', '😗', '☺️', '😚', '😙', '🥲', '😋', '😛', '😜', '🤪', '😝', '🤑', '🤗', '🤭', '🤫', '🤔',
            '🤐', '🤨', '😐', '😑', '😶', '😏', '😒', '🙄', '😬', '🤥', '😔', '😪', '🤤', '😴', '😷', '🤒',
            '🤕', '🤢', '🤮', '🤧', '🥵', '🥶', '🥴', '😵', '🤯', '🤠', '🥳', '🥸', '😎', '🤓', '🧐',
            
            // People & Body
            '👋', '🤚', '🖐️', '✋', '🖖', '👌', '🤌', '🤏', '✌️', '🤞', '🤟', '🤘', '🤙', '👈', '👉', '👆',
            '🖕', '👇', '☝️', '👍', '👎', '👊', '✊', '🤛', '🤜', '👏', '🙌', '👐', '🤲', '🤝', '🙏',
            
            // Animals & Nature
            '🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮', '🐷', '🐸', '🐵', '🙈',
            '🙉', '🙊', '🐒', '🐔', '🐧', '🐦', '🐤', '🐣', '🐥', '🦆', '🦅', '🦉', '🦇', '🐺', '🐗', '🐴',
            '🦄', '🐝', '🐛', '🦋', '🐌', '🐞', '🐜', '🦟', '🦗', '🕷️', '🕸️', '🦂', '🐢', '🐍', '🦎', '🦖',
            
            // Food & Drink
            '🍎', '🍐', '🍊', '🍋', '🍌', '🍉', '🍇', '🍓', '🫐', '🍈', '🍒', '🍑', '🥭', '🍍', '🥥', '🥝',
            '🍅', '🍆', '🥑', '🥦', '🥬', '🥒', '🌶️', '🫑', '🌽', '🥕', '🫒', '🧄', '🧅', '🥔', '🍠', '🥐',
            '🥖', '🍞', '🥨', '🥯', '🧀', '🥚', '🍳', '🧈', '🥞', '🧇', '🥓', '🥩', '🍗', '🍖', '🦴', '🌭',
            '🍔', '🍟', '🍕', '🥪', '🥙', '🧆', '🌮', '🌯', '🫔', '🥗', '🥘', '🫕', '🍝', '🍜', '🍲', '🍛',
            
            // Activities
            '⚽', '🏀', '🏈', '⚾', '🥎', '🎾', '🏐', '🏉', '🥏', '🎱', '🪀', '🏓', '🏸', '🏒', '🏑', '🥍',
            '🏏', '🪃', '🥅', '⛳', '🪁', '🏹', '🎣', '🤿', '🥊', '🥋', '🎽', '🛹', '🛷', '⛸️', '🥌', '🎿',
            
            // Objects
            '⌚', '📱', '📲', '💻', '⌨️', '🖥️', '🖨️', '🖱️', '🖲️', '🕹️', '🗜️', '💽', '💾', '💿', '📀', '📼',
            '📷', '📸', '📹', '🎥', '📽️', '🎞️', '📞', '☎️', '📟', '📠', '📺', '📻', '🎙️', '🎚️', '🎛️', '🧭',
            
            // Symbols
            '❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❣️', '💕', '💞', '💓', '💗', '💖',
            '💘', '💝', '💟', '☮️', '✝️', '☪️', '🕉️', '☸️', '✡️', '🔯', '🕎', '☯️', '☦️', '🛐', '⛎', '♈',
            '♉', '♊', '♋', '♌', '♍', '♎', '♏', '♐', '♑', '♒', '♓', '🆔', '⚛️', '🉑', '☢️', '☣️', '📴', '📳'
        ];
        
        // Create custom emoji button in toolbar
        const toolbar = quill.getModule('toolbar');
        toolbar.addHandler('emoji', function() {
            showEmojiPicker();
        });
        
        // Add emoji button to toolbar
        const emojiButton = document.querySelector('.ql-emoji');
        if (emojiButton) {
            emojiButton.innerHTML = '😀';
            emojiButton.title = 'Insert Emoji';
        }
        
        // Create emoji picker
        function createEmojiPicker() {
            const picker = document.createElement('div');
            picker.id = 'emojiPicker';
            picker.className = 'emoji-picker';
            
            let html = '<div class="emoji-category">Pilih Emoji:</div><div class="emoji-grid">';
            emojiList.forEach(emoji => {
                html += `<button type="button" class="emoji-item" onclick="insertEmoji('${emoji}')">${emoji}</button>`;
            });
            html += '</div>';
            
            picker.innerHTML = html;
            document.body.appendChild(picker);
            
            // Close picker when clicking outside
            document.addEventListener('click', function(e) {
                if (!picker.contains(e.target) && !e.target.closest('.ql-emoji')) {
                    picker.classList.remove('show');
                }
            });
        }
        
        function showEmojiPicker() {
            let picker = document.getElementById('emojiPicker');
            if (!picker) {
                createEmojiPicker();
                picker = document.getElementById('emojiPicker');
            }
            
            // Position picker near the emoji button
            const emojiButton = document.querySelector('.ql-emoji');
            const rect = emojiButton.getBoundingClientRect();
            picker.style.position = 'fixed';
            picker.style.top = (rect.bottom + 5) + 'px';
            picker.style.left = (rect.left - 250) + 'px'; // Adjust position
            
            picker.classList.toggle('show');
        }
        
        function insertEmoji(emoji) {
            const range = quill.getSelection();
            if (range) {
                quill.insertText(range.index, emoji);
                quill.setSelection(range.index + emoji.length);
            } else {
                quill.insertText(quill.getLength() - 1, emoji);
            }
            
            // Hide picker
            document.getElementById('emojiPicker').classList.remove('show');
            
            // Update hidden textarea
            document.getElementById('deskripsi').value = quill.root.innerHTML;
        }
        
        // Make functions global
        window.insertEmoji = insertEmoji;
        
        // Enhanced line break handling
        quill.keyboard.addBinding({
            key: 13, // Enter key
            handler: function(range, context) {
                // Insert line break instead of new paragraph
                this.quill.insertText(range.index, '\n');
                this.quill.setSelection(range.index + 1);
                return false; // Prevent default behavior
            }
        });
        
        function editProduct(product) {
            document.getElementById('modalTitle').textContent = 'Edit Produk';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('productId').value = product.id;
            document.getElementById('nama_produk').value = product.nama_produk;
            
            // Set category
            document.getElementById('category_id').value = product.category_id || '';
            
            // Set content for both Quill editors
            quillShort.root.innerHTML = product.deskripsi_singkat || '';
            document.getElementById('deskripsi_singkat').value = product.deskripsi_singkat || '';
            
            quill.root.innerHTML = product.deskripsi || '';
            document.getElementById('deskripsi').value = product.deskripsi || '';
            
            document.getElementById('submitBtn').textContent = 'Update';
            
            // Show current image
            if (product.foto) {
                document.getElementById('currentImage').style.display = 'block';
                document.getElementById('currentImagePreview').src = '../' + product.foto;
            }
            
            new bootstrap.Modal(document.getElementById('productModal')).show();
        }
        
        // Toggle product status
        function toggleProductStatus(productId, isActive) {
            const formData = new FormData();
            formData.append('action', 'toggle_status');
            formData.append('id', productId);
            formData.append('status', isActive ? 1 : 0);
            
            fetch('products.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // Update badge
                const badge = document.getElementById('badge_' + productId);
                if (badge) {
                    if (isActive) {
                        badge.textContent = 'Aktif';
                        badge.className = 'badge bg-success';
                    } else {
                        badge.textContent = 'Nonaktif';
                        badge.className = 'badge bg-secondary';
                    }
                }
                
                // Show notification
                const statusText = isActive ? 'diaktifkan' : 'dinonaktifkan';
                showAdminNotification(`Produk berhasil ${statusText}!`, 'success');
            })
            .catch(error => {
                console.error('Error:', error);
                showAdminNotification('Gagal mengubah status produk!', 'danger');
                
                // Revert checkbox state
                const checkbox = document.getElementById('status_' + productId);
                if (checkbox) {
                    checkbox.checked = !isActive;
                }
            });
        }
        
        // Reset modal when closed
        document.getElementById('productModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('modalTitle').textContent = 'Tambah Produk';
            document.getElementById('formAction').value = 'add';
            document.getElementById('productId').value = '';
            document.getElementById('productForm').reset();
            
            // Clear both Quill editors
            quillShort.setContents([]);
            document.getElementById('deskripsi_singkat').value = '';
            
            quill.setContents([]);
            document.getElementById('deskripsi').value = '';
            
            document.getElementById('submitBtn').textContent = 'Simpan';
            document.getElementById('currentImage').style.display = 'none';
        });
        
        // Form submit handler with enhanced content processing
        document.getElementById('productForm').addEventListener('submit', function(e) {
            // Process both Quill editors content before submit
            let shortContent = quillShort.root.innerHTML;
            let fullContent = quill.root.innerHTML;
            
            // Sync processed content to textareas
            document.getElementById('deskripsi_singkat').value = shortContent;
            document.getElementById('deskripsi').value = fullContent;
            
            console.log('Submitting short content:', shortContent);
            console.log('Submitting full content:', fullContent);
        });
        
        // Auto-save functionality (optional)
        let autoSaveTimer;
        quill.on('text-change', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function() {
                // Auto-save logic here if needed
                console.log('Auto-saving...', quill.root.innerHTML);
            }, 2000); // Save after 2 seconds of inactivity
        });
    </script>
</body>
</html>