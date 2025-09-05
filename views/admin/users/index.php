<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Users</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin/dashboard">Home</a></li>
          <li class="breadcrumb-item active">Users</li>
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
            <h3 class="card-title">All Users</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-create-user">
                <i class="fas fa-user-plus"></i> Add User
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
                  <th>Username</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Articles</th>
                  <th>Comments</th>
                  <th>Created At</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $user): ?>
                  <tr>
                    <td><?= $user['id'] ?></td>
                    <td>
                      <?= htmlspecialchars($user['username']) ?>
                      <?php if ($user['id'] === $_SESSION['user_id']): ?>
                        <span class="badge badge-info">You</span>
                      <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                      <span class="badge badge-<?= $user['role'] === 'admin' ? 'danger' : 'success' ?>">
                        <?= ucfirst($user['role']) ?>
                      </span>
                    </td>
                    <td><span class="badge badge-info"><?= $user['article_count'] ?></span></td>
                    <td><span class="badge badge-info"><?= $user['comment_count'] ?></span></td>
                    <td><?= date('Y-m-d H:i', strtotime($user['created_at'])) ?></td>
                    <td>
                      <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal-edit-user" 
                              data-id="<?= $user['id'] ?>"
                              data-username="<?= htmlspecialchars($user['username']) ?>"
                              data-email="<?= htmlspecialchars($user['email']) ?>"
                              data-role="<?= $user['role'] ?>">
                        <i class="fas fa-edit"></i>
                      </button>
                      <?php if ($user['id'] !== $_SESSION['user_id'] && $user['id'] !== 1): ?>
                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modal-delete-user" 
                                data-id="<?= $user['id'] ?>"
                                data-username="<?= htmlspecialchars($user['username']) ?>">
                          <i class="fas fa-trash"></i>
                        </button>
                      <?php endif; ?>
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

<!-- Create User Modal -->
<div class="modal fade" id="modal-create-user">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add New User</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/admin/users/create" method="post">
        <div class="modal-body">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <div class="form-group">
            <label for="role">Role</label>
            <select class="form-control" id="role" name="role">
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
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

<!-- Edit User Modal -->
<div class="modal fade" id="modal-edit-user">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edit User</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/admin/users/update" method="post">
        <input type="hidden" name="id" id="edit-user-id">
        <div class="modal-body">
          <div class="form-group">
            <label for="edit-username">Username</label>
            <input type="text" class="form-control" id="edit-username" name="username" required>
          </div>
          <div class="form-group">
            <label for="edit-email">Email</label>
            <input type="email" class="form-control" id="edit-email" name="email" required>
          </div>
          <div class="form-group">
            <label for="edit-password">Password</label>
            <input type="password" class="form-control" id="edit-password" name="password">
            <small class="form-text text-muted">Leave empty to keep current password</small>
          </div>
          <div class="form-group">
            <label for="edit-role">Role</label>
            <select class="form-control" id="edit-role" name="role">
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
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

<!-- Delete User Modal -->
<div class="modal fade" id="modal-delete-user">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Delete User</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/admin/users/delete" method="post">
        <input type="hidden" name="id" id="delete-user-id">
        <div class="modal-body">
          <p>Are you sure you want to delete user "<span id="delete-user-name"></span>"?</p>
          <p class="text-warning">This action cannot be undone. The user's articles will be reassigned to admin.</p>
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
  // Handle edit user modal
  $('#modal-edit-user').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var username = button.data('username');
    var email = button.data('email');
    var role = button.data('role');
    
    var modal = $(this);
    modal.find('#edit-user-id').val(id);
    modal.find('#edit-username').val(username);
    modal.find('#edit-email').val(email);
    modal.find('#edit-role').val(role);
  });
  
  // Handle delete user modal
  $('#modal-delete-user').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var username = button.data('username');
    
    var modal = $(this);
    modal.find('#delete-user-id').val(id);
    modal.find('#delete-user-name').text(username);
  });
});</script>