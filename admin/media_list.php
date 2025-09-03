<?php
require_once 'helpers/auth_helper.php';
requireAdmin();

$page_title = 'Thư viện Media';
$current_page = 'media';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media_file'])) {
    $uploadDir = 'uploads/media/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $file = $_FILES['media_file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    if ($fileError === 0) {
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt', 'zip'];
        
        if (in_array($fileExt, $allowedTypes)) {
            if ($fileSize < 10000000) { // 10MB limit
                $fileNameNew = uniqid('', true) . '.' . $fileExt;
                $fileDestination = $uploadDir . $fileNameNew;
                
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    // Save to database
                    $stmt = $pdo->prepare("INSERT INTO media (filename, original_name, file_path, file_size, file_type, uploaded_at) VALUES (?, ?, ?, ?, ?, NOW())");
                    $stmt->execute([$fileNameNew, $fileName, $fileDestination, $fileSize, $fileType]);
                    
                    $_SESSION['success'] = 'File uploaded successfully!';
                } else {
                    $_SESSION['error'] = 'Failed to upload file!';
                }
            } else {
                $_SESSION['error'] = 'File size too large! Maximum 10MB allowed.';
            }
        } else {
            $_SESSION['error'] = 'File type not allowed!';
        }
    } else {
        $_SESSION['error'] = 'Error uploading file!';
    }
    
    header('Location: /admin/media');
    exit;
}

// Handle file deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $mediaId = $_GET['delete'];
    
    // Get file info
    $stmt = $pdo->prepare("SELECT * FROM media WHERE id = ?");
    $stmt->execute([$mediaId]);
    $media = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($media) {
        // Delete file from filesystem
        if (file_exists($media['file_path'])) {
            unlink($media['file_path']);
        }
        
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM media WHERE id = ?");
        $stmt->execute([$mediaId]);
        
        $_SESSION['success'] = 'File deleted successfully!';
    } else {
        $_SESSION['error'] = 'File not found!';
    }
    
    header('Location: /admin/media');
    exit;
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$whereClause = '';
$params = [];

if (!empty($search)) {
    $whereClause = 'WHERE original_name LIKE ?';
    $params[] = '%' . $search . '%';
}

// Get total count
$countSql = "SELECT COUNT(*) FROM media $whereClause";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalFiles = $countStmt->fetchColumn();
$totalPages = ceil($totalFiles / $limit);

// Get media files
$sql = "SELECT * FROM media $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$mediaFiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create media table if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS media (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL,
        original_name VARCHAR(255) NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        file_size INT NOT NULL,
        file_type VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    // Table might already exist
}

require_once 'admin/includes/header.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Thư viện Media</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Thư viện Media</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quản lý File Media</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
                            <i class="fas fa-upload"></i> Upload File
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" class="d-flex">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm file..." value="<?php echo htmlspecialchars($search); ?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group float-right" role="group">
                                <button type="button" class="btn btn-outline-secondary active" id="gridView">
                                    <i class="fas fa-th"></i> Grid
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="listView">
                                    <i class="fas fa-list"></i> List
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Drag & Drop Upload Area -->
                    <div class="upload-area mb-4" id="uploadArea">
                        <div class="upload-content text-center py-4">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <h5>Kéo thả file vào đây hoặc <a href="#" data-toggle="modal" data-target="#uploadModal">chọn file</a></h5>
                            <p class="text-muted">Hỗ trợ: JPG, PNG, GIF, PDF, DOC, DOCX, TXT, ZIP (tối đa 10MB)</p>
                        </div>
                    </div>

                    <!-- Media Grid View -->
                    <div class="media-container" id="mediaGrid">
                        <div class="row">
                            <?php if (empty($mediaFiles)): ?>
                                <div class="col-12">
                                    <div class="text-center py-5">
                                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Chưa có file nào được upload.</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($mediaFiles as $file): ?>
                                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                                        <div class="media-item card h-100">
                                            <div class="card-body text-center p-2">
                                                <?php 
                                                $fileExt = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
                                                $imageTypes = ['jpg', 'jpeg', 'png', 'gif'];
                                                ?>
                                                
                                                <div class="media-preview mb-2" onclick="previewMedia(<?php echo htmlspecialchars(json_encode($file)); ?>)" style="cursor: pointer;">
                                                    <?php if (in_array($fileExt, $imageTypes)): ?>
                                                        <img src="/<?php echo $file['file_path']; ?>" class="img-fluid" style="max-height: 80px; border-radius: 4px;">
                                                    <?php else: ?>
                                                        <div class="file-icon">
                                                            <?php
                                                            $iconClass = 'fas fa-file';
                                                            switch($fileExt) {
                                                                case 'pdf': $iconClass = 'fas fa-file-pdf text-danger'; break;
                                                                case 'doc': case 'docx': $iconClass = 'fas fa-file-word text-primary'; break;
                                                                case 'zip': $iconClass = 'fas fa-file-archive text-warning'; break;
                                                                case 'txt': $iconClass = 'fas fa-file-alt text-info'; break;
                                                            }
                                                            ?>
                                                            <i class="<?php echo $iconClass; ?> fa-3x"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <h6 class="card-title small mb-1" title="<?php echo htmlspecialchars($file['original_name']); ?>">
                                                    <?php echo strlen($file['original_name']) > 15 ? substr($file['original_name'], 0, 15) . '...' : $file['original_name']; ?>
                                                </h6>
                                                
                                                <p class="card-text small text-muted mb-2">
                                                    <?php echo number_format($file['file_size'] / 1024, 1); ?> KB
                                                </p>
                                                
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-info btn-sm" onclick="previewMedia(<?php echo htmlspecialchars(json_encode($file)); ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm" onclick="copyToClipboard('/<?php echo $file['file_path']; ?>')">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                    <a href="?delete=<?php echo $file['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa file này?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Media List View -->
                    <div class="media-container d-none" id="mediaList">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="60">Preview</th>
                                        <th>Tên file</th>
                                        <th>Kích thước</th>
                                        <th>Loại</th>
                                        <th>Ngày tạo</th>
                                        <th width="120">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($mediaFiles)): ?>
                                        <?php foreach ($mediaFiles as $file): ?>
                                            <?php 
                                            $fileExt = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
                                            $imageTypes = ['jpg', 'jpeg', 'png', 'gif'];
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php if (in_array($fileExt, $imageTypes)): ?>
                                                        <img src="/<?php echo $file['file_path']; ?>" class="img-thumbnail" style="width: 40px; height: 40px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <i class="fas fa-file fa-2x text-muted"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($file['original_name']); ?></strong><br>
                                                    <small class="text-muted"><?php echo $file['filename']; ?></small>
                                                </td>
                                                <td><?php echo number_format($file['file_size'] / 1024, 2); ?> KB</td>
                                                <td><span class="badge badge-secondary"><?php echo strtoupper($fileExt); ?></span></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($file['created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-info" onclick="previewMedia(<?php echo htmlspecialchars(json_encode($file)); ?>)">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-secondary" onclick="copyToClipboard('/<?php echo $file['file_path']; ?>')">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                        <a href="?delete=<?php echo $file['id']; ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa file này?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <i class="fas fa-folder-open fa-2x text-muted mb-2"></i><br>
                                                <span class="text-muted">Chưa có file nào được upload.</span>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">&laquo; Trước</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">Sau &raquo;</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload File</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="media_file">Chọn file:</label>
                        <input type="file" class="form-control-file" id="media_file" name="media_file" required>
                    </div>
                    <div class="alert alert-info">
                        <strong>Định dạng hỗ trợ:</strong> JPG, JPEG, PNG, GIF, PDF, DOC, DOCX, ZIP, TXT<br>
                        <strong>Kích thước tối đa:</strong> 10MB
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Preview File</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="previewContent"></div>
                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Tên file:</strong> <span id="previewName"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Kích thước:</strong> <span id="previewSize"></span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <strong>Loại:</strong> <span id="previewType"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Ngày tạo:</strong> <span id="previewDate"></span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <strong>Đường dẫn:</strong> 
                        <div class="input-group">
                            <input type="text" class="form-control" id="previewPath" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="copyFromPreview()">Copy</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="previewDownload" class="btn btn-primary" target="_blank">Tải xuống</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
// View toggle functionality
function toggleView(viewType) {
    const gridView = document.getElementById('mediaGrid');
    const listView = document.getElementById('mediaList');
    const gridBtn = document.getElementById('gridView');
    const listBtn = document.getElementById('listView');
    
    if (viewType === 'grid') {
        gridView.classList.remove('d-none');
        listView.classList.add('d-none');
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        localStorage.setItem('mediaViewType', 'grid');
    } else {
        gridView.classList.add('d-none');
        listView.classList.remove('d-none');
        gridBtn.classList.remove('active');
        listBtn.classList.add('active');
        localStorage.setItem('mediaViewType', 'list');
    }
}

// Load saved view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('mediaViewType') || 'grid';
    toggleView(savedView);
    
    // Add event listeners for view toggle buttons
    document.getElementById('gridView').addEventListener('click', () => toggleView('grid'));
    document.getElementById('listView').addEventListener('click', () => toggleView('list'));
});

// Preview media function
function previewMedia(file) {
    const modal = $('#previewModal');
    const previewContent = document.getElementById('previewContent');
    const fileExt = file.original_name.split('.').pop().toLowerCase();
    const imageTypes = ['jpg', 'jpeg', 'png', 'gif'];
    
    // Clear previous content
    previewContent.innerHTML = '';
    
    // Set file info
    document.getElementById('previewName').textContent = file.original_name;
    document.getElementById('previewSize').textContent = (file.file_size / 1024).toFixed(2) + ' KB';
    document.getElementById('previewType').textContent = fileExt.toUpperCase();
    document.getElementById('previewDate').textContent = new Date(file.created_at).toLocaleString('vi-VN');
    document.getElementById('previewPath').value = window.location.origin + '/' + file.file_path;
    document.getElementById('previewDownload').href = '/' + file.file_path;
    
    // Show preview based on file type
    if (imageTypes.includes(fileExt)) {
        previewContent.innerHTML = `<img src="/${file.file_path}" class="img-fluid" style="max-height: 400px;">`;
    } else {
        let iconClass = 'fas fa-file';
        switch(fileExt) {
            case 'pdf': iconClass = 'fas fa-file-pdf text-danger'; break;
            case 'doc': case 'docx': iconClass = 'fas fa-file-word text-primary'; break;
            case 'zip': iconClass = 'fas fa-file-archive text-warning'; break;
            case 'txt': iconClass = 'fas fa-file-alt text-info'; break;
        }
        previewContent.innerHTML = `<i class="${iconClass}" style="font-size: 8rem;"></i>`;
    }
    
    modal.modal('show');
}

// Copy functions
function copyToClipboard(text) {
    navigator.clipboard.writeText(window.location.origin + text).then(function() {
        alert('Đã copy đường dẫn vào clipboard!');
    });
}

function copyFromPreview() {
    const pathInput = document.getElementById('previewPath');
    navigator.clipboard.writeText(pathInput.value).then(function() {
        alert('Đã copy đường dẫn vào clipboard!');
    });
}

// Drag and drop functionality
const uploadArea = document.getElementById('uploadArea');
if (uploadArea) {
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById('media_file').files = files;
            $('#uploadModal').modal('show');
        }
    });
}
</script>

<style>
.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
}

.upload-area:hover,
.upload-area.dragover {
    border-color: #007bff;
    background-color: #e3f2fd;
}

.media-item {
    transition: transform 0.2s ease;
}

.media-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.media-preview {
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.file-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
}
</style>

<?php require_once 'admin/includes/footer.php'; ?>