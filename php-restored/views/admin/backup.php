<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Database Backup</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Backup</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-all me-2"></i>
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['success']); endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="mdi mdi-block-helper me-2"></i>
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['error']); endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <h4 class="header-title">Create New Backup</h4>
                            <p class="text-muted font-13 mb-4">
                                Create a complete backup of your database. This will include all tables and data.
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end">
                                <form method="POST" style="display: inline;">
                                    <button type="submit" name="create_backup" class="btn btn-success mb-2" onclick="return confirm('Are you sure you want to create a backup?')">
                                        <i class="mdi mdi-database-plus me-1"></i> Create Backup
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info" role="alert">
                        <i class="mdi mdi-information me-2"></i>
                        <strong>Important:</strong> Backups are stored in the <code>/backups</code> directory. Make sure to download and store them in a secure location.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Existing Backups</h4>
                    <p class="text-muted font-13 mb-4">
                        List of all available database backups. You can download or delete them as needed.
                    </p>

                    <?php if (empty($backupFiles)): ?>
                    <div class="text-center py-4">
                        <i class="mdi mdi-database-off h1 text-muted"></i>
                        <h4 class="text-muted">No backups found</h4>
                        <p class="text-muted">Create your first backup using the button above.</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Backup File</th>
                                    <th>Size</th>
                                    <th>Created Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($backupFiles as $backup): ?>
                                <tr>
                                    <td>
                                        <i class="mdi mdi-database me-2 text-primary"></i>
                                        <strong><?= htmlspecialchars($backup['name']) ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <?= formatBytes($backup['size']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted"><?= $backup['date'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="/backups/<?= urlencode($backup['name']) ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Download Backup"
                                               download>
                                                <i class="mdi mdi-download"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="Delete Backup"
                                                    onclick="deleteBackup('<?= htmlspecialchars($backup['name']) ?>')">
                                                <i class="mdi mdi-delete"></i>
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
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Backup Information</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-muted">What's included in backups:</h5>
                            <ul class="list-unstyled">
                                <li><i class="mdi mdi-check text-success me-2"></i>All database tables</li>
                                <li><i class="mdi mdi-check text-success me-2"></i>Table structures (CREATE statements)</li>
                                <li><i class="mdi mdi-check text-success me-2"></i>All data (INSERT statements)</li>
                                <li><i class="mdi mdi-check text-success me-2"></i>Timestamp information</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted">Best practices:</h5>
                            <ul class="list-unstyled">
                                <li><i class="mdi mdi-information text-info me-2"></i>Create regular backups</li>
                                <li><i class="mdi mdi-information text-info me-2"></i>Store backups in secure locations</li>
                                <li><i class="mdi mdi-information text-info me-2"></i>Test restore procedures</li>
                                <li><i class="mdi mdi-information text-info me-2"></i>Keep multiple backup versions</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteBackup(filename) {
    if (confirm('Are you sure you want to delete the backup "' + filename + '"? This action cannot be undone.')) {
        // Create a form to submit the delete request
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/backup';
        
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_backup';
        input.value = filename;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>

<?php
function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}
?>