<?php require_once 'views/layouts/admin.php'; ?>

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Comments</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin/dashboard">Home</a></li>
          <li class="breadcrumb-item active">Comments</li>
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
            <h3 class="card-title">All Comments</h3>
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

          <div class="card-body">
            <form action="/admin/comments/bulk-action" method="post" id="comments-form">
              <div class="d-flex justify-content-between mb-3">
                <div class="bulk-actions">
                  <select class="form-control" name="action" id="bulk-action">
                    <option value="">Bulk Actions</option>
                    <option value="approve">Approve</option>
                    <option value="reject">Reject</option>
                    <option value="delete">Delete</option>
                  </select>
                  <button type="submit" class="btn btn-primary ml-2" id="apply-bulk-action" disabled>Apply</button>
                </div>
              </div>

              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th width="30px">
                        <div class="icheck-primary">
                          <input type="checkbox" id="check-all">
                          <label for="check-all"></label>
                        </div>
                      </th>
                      <th>Author</th>
                      <th>Comment</th>
                      <th>Article</th>
                      <th>Status</th>
                      <th>Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($comments as $comment): ?>
                      <tr>
                        <td>
                          <div class="icheck-primary">
                            <input type="checkbox" name="comment_ids[]" value="<?= $comment['id'] ?>" id="check<?= $comment['id'] ?>">
                            <label for="check<?= $comment['id'] ?>"></label>
                          </div>
                        </td>
                        <td><?= htmlspecialchars($comment['user_name']) ?></td>
                        <td>
                          <?= nl2br(htmlspecialchars($comment['content'])) ?>
                        </td>
                        <td>
                          <a href="/articles/<?= $comment['article_id'] ?>" target="_blank">
                            <?= htmlspecialchars($comment['article_title']) ?>
                          </a>
                        </td>
                        <td>
                          <span class="badge badge-<?= $comment['status'] === 'approved' ? 'success' : ($comment['status'] === 'pending' ? 'warning' : 'danger') ?>">
                            <?= ucfirst($comment['status']) ?>
                          </span>
                        </td>
                        <td><?= date('Y-m-d H:i', strtotime($comment['created_at'])) ?></td>
                        <td>
                          <?php if ($comment['status'] !== 'approved'): ?>
                            <button type="button" class="btn btn-sm btn-success" onclick="updateStatus(<?= $comment['id'] ?>, 'approved')">
                              <i class="fas fa-check"></i>
                            </button>
                          <?php endif; ?>
                          
                          <?php if ($comment['status'] !== 'rejected'): ?>
                            <button type="button" class="btn btn-sm btn-warning" onclick="updateStatus(<?= $comment['id'] ?>, 'rejected')">
                              <i class="fas fa-ban"></i>
                            </button>
                          <?php endif; ?>
                          
                          <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modal-delete-comment" 
                                  data-id="<?= $comment['id'] ?>">
                            <i class="fas fa-trash"></i>
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </form>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
    </div>
  </div>
</section>

<!-- Delete Comment Modal -->
<div class="modal fade" id="modal-delete-comment">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Delete Comment</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/admin/comments/delete" method="post">
        <input type="hidden" name="id" id="delete-comment-id">
        <div class="modal-body">
          <p>Are you sure you want to delete this comment?</p>
          <p class="text-warning">This action cannot be undone.</p>
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

<!-- iCheck -->
<link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">

<script>
$(function () {
  // Handle check all checkbox
  $('#check-all').change(function() {
    $('input[name="comment_ids[]"]').prop('checked', $(this).prop('checked'));
    updateBulkActionButton();
  });
  
  // Handle individual checkboxes
  $('input[name="comment_ids[]"]').change(function() {
    updateBulkActionButton();
  });
  
  // Handle bulk action button state
  function updateBulkActionButton() {
    var checkedCount = $('input[name="comment_ids[]"]:checked').length;
    $('#apply-bulk-action').prop('disabled', checkedCount === 0);
  }
  
  // Handle delete comment modal
  $('#modal-delete-comment').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var modal = $(this);
    modal.find('#delete-comment-id').val(id);
  });
  
  // Handle bulk action form submission
  $('#comments-form').submit(function(e) {
    var action = $('#bulk-action').val();
    if (!action) {
      e.preventDefault();
      alert('Please select an action');
    }
  });
});

// Function to update comment status
function updateStatus(id, status) {
  var form = document.createElement('form');
  form.method = 'POST';
  form.action = '/admin/comments/update';
  
  var idInput = document.createElement('input');
  idInput.type = 'hidden';
  idInput.name = 'id';
  idInput.value = id;
  form.appendChild(idInput);
  
  var statusInput = document.createElement('input');
  statusInput.type = 'hidden';
  statusInput.name = 'status';
  statusInput.value = status;
  form.appendChild(statusInput);
  
  document.body.appendChild(form);
  form.submit();
}</script>