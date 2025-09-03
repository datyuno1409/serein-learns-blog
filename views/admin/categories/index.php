<?php require_once 'views/layouts/admin.php'; ?>

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Categories</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin/dashboard">Home</a></li>
          <li class="breadcrumb-item active">Categories</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">All Categories</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-create-category">
                <i class="fas fa-plus"></i> Add Category
              </button>
            </div>
          </div>
          <!-- /.card-header -->

          <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <?= $_SESSION['success'] ?>
              <?php unset($_SESSION['success']); ?>
            </div>
          <?php endif; ?>

          <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <?= $_SESSION['error'] ?>
              <?php unset($_SESSION['error']); ?>
            </div>
          <?php endif; ?>

          <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Description</th>
                  <th>Articles</th>
                  <th>Created At</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($categories as $category): ?>
                  <tr>
                    <td><?= $category['id'] ?></td>
                    <td><?= htmlspecialchars($category['name']) ?></td>
                    <td><?= htmlspecialchars($category['description']) ?></td>
                    <td><span class="badge badge-info"><?= $category['article_count'] ?></span></td>
                    <td><?= date('Y-m-d H:i', strtotime($category['created_at'])) ?></td>
                    <td>
                      <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal-edit-category" 
                              data-id="<?= $category['id'] ?>" 
                              data-name="<?= htmlspecialchars($category['name']) ?>"
                              data-description="<?= htmlspecialchars($category['description']) ?>">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modal-delete-category" 
                              data-id="<?= $category['id'] ?>"
                              data-name="<?= htmlspecialchars($category['name']) ?>">
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
    </div>
  </div>
</section>

<!-- Create Category Modal -->
<div class="modal fade" id="modal-create-category">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add New Category</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/admin/categories/create" method="post">
        <div class="modal-body">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Create</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Edit Category Modal -->
<div class="modal fade" id="modal-edit-category">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edit Category</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/admin/categories/update" method="post">
        <input type="hidden" name="id" id="edit-category-id">
        <div class="modal-body">
          <div class="form-group">
            <label for="edit-name">Name</label>
            <input type="text" class="form-control" id="edit-name" name="name" required>
          </div>
          <div class="form-group">
            <label for="edit-description">Description</label>
            <textarea class="form-control" id="edit-description" name="description" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Delete Category Modal -->
<div class="modal fade" id="modal-delete-category">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Delete Category</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/admin/categories/delete" method="post">
        <input type="hidden" name="id" id="delete-category-id">
        <div class="modal-body">
          <p>Are you sure you want to delete category "<span id="delete-category-name"></span>"?</p>
          <p class="text-warning">This action cannot be undone if the category contains articles.</p>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>
$(function () {
  // Handle edit category modal
  $('#modal-edit-category').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var name = button.data('name');
    var description = button.data('description');
    
    var modal = $(this);
    modal.find('#edit-category-id').val(id);
    modal.find('#edit-name').val(name);
    modal.find('#edit-description').val(description);
  });
  
  // Handle delete category modal
  $('#modal-delete-category').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var name = button.data('name');
    
    var modal = $(this);
    modal.find('#delete-category-id').val(id);
    modal.find('#delete-category-name').text(name);
  });
});
</script>