<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Sao lưu</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Sao lưu</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <!-- Backup Actions -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tạo sao lưu</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="create_db_backup">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-database"></i> Sao lưu Database
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="create_files_backup">
                                        <button type="submit" class="btn btn-success btn-block">
                                            <i class="fas fa-file-archive"></i> Sao lưu Files
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Lưu ý:</h6>
                                <ul class="mb-0">
                                    <li>Sao lưu cơ sở dữ liệu: Tạo file SQL chứa toàn bộ dữ liệu</li>
                                    <li>Sao lưu tệp tin: Tạo file ZIP chứa uploads và config</li>
                                    <li>Các file sao lưu được lưu trong thư mục /backups</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Thông tin sao lưu</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-archive"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Tổng số sao lưu</span>
                                    <span class="info-box-number"><?php echo count($backups); ?></span>
                                </div>
                            </div>
                            
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-hdd"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Tổng dung lượng</span>
                                    <span class="info-box-number"><?php echo formatBytes($totalSize); ?></span>
                                </div>
                            </div>
                            
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Sao lưu gần nhất</span>
                                    <span class="info-box-number">
                                        <?php echo !empty($backups) ? date('d/m/Y H:i', strtotime($backups[0]['created_at'])) : 'Chưa có'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Backup List -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách sao lưu</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($backups)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-archive fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có sao lưu nào được tạo.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Loại</th>
                                        <th>Tên file</th>
                                        <th>Dung lượng</th>
                                        <th>Người tạo</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($backups as $backup): ?>
                                        <tr>
                                            <td>
                                                <?php if ($backup['type'] === 'database'): ?>
                                                    <span class="badge badge-primary"><i class="fas fa-database"></i> Database</span>
                                                <?php else: ?>
                                                    <span class="badge badge-success"><i class="fas fa-file-archive"></i> Files</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($backup['filename']); ?></td>
                                            <td><?php echo formatBytes($backup['size']); ?></td>
                                            <td><?php echo htmlspecialchars($backup['username'] ?? 'Unknown'); ?></td>
                                            <td><?php echo date('d/m/Y H:i:s', strtotime($backup['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?php echo $backup['filepath']; ?>" 
                                                       class="btn btn-sm btn-info" 
                                                       download="<?php echo $backup['filename']; ?>">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            onclick="deleteBackup(<?php echo $backup['id']; ?>, '<?php echo htmlspecialchars($backup['filename']); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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
    </section>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Xác nhận xóa</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa sao lưu <strong id="backupName"></strong>?</p>
                <p class="text-danger">Hành động này không thể hoàn tác!</p>
            </div>
            <div class="modal-footer">
                <form method="POST" id="deleteForm">
                    <input type="hidden" name="action" value="delete_backup">
                    <input type="hidden" name="backup_id" id="backupId">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteBackup(id, filename) {
    document.getElementById('backupId').value = id;
    document.getElementById('backupName').textContent = filename;
    $('#deleteModal').modal('show');
}

// Auto refresh page after backup operations
<?php if ($message): ?>
    setTimeout(function() {
        location.reload();
    }, 2000);
<?php endif; ?>
</script>