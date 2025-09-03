<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Articles</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="/admin/dashboard">Home</a></li>
          <li class="breadcrumb-item active">Articles</li>
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
            <h3 class="card-title">All Articles</h3>
            <div class="card-tools">
              <a href="/admin/articles/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Article
              </a>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive p-0">
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

            <table class="table table-hover text-nowrap">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Title</th>
                  <th>Category</th>
                  <th>Author</th>
                  <th>Status</th>
                  <th>Views</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($articles as $article): ?>
                <tr>
                  <td><?= $article['id'] ?></td>
                  <td>
                    <img src="<?= $article['image'] ?? 'dist/img/default-article.png' ?>" alt="Article Image" class="img-size-50 mr-2">
                    <?= htmlspecialchars($article['title']) ?>
                  </td>
                  <td>
                    <span class="badge badge-info"><?= htmlspecialchars($article['category_name']) ?></span>
                  </td>
                  <td><?= htmlspecialchars($article['author_name']) ?></td>
                  <td>
                    <?php if ($article['status'] === 'published'): ?>
                      <span class="badge badge-success">Published</span>
                    <?php else: ?>
                      <span class="badge badge-warning">Draft</span>
                    <?php endif; ?>
                  </td>
                  <td><?= $article['views'] ?></td>
                  <td><?= date('M d, Y', strtotime($article['created_at'])) ?></td>
                  <td>
                    <a href="/admin/articles/edit/<?= $article['id'] ?>" class="btn btn-info btn-sm">
                      <i class="fas fa-pencil-alt"></i> Edit
                    </a>
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete-modal" data-article-id="<?= $article['id'] ?>">
                      <i class="fas fa-trash"></i> Delete
                    </button>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <!-- /.card-body -->
          <div class="card-footer clearfix">
            <ul class="pagination pagination-sm m-0 float-right">
              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>
            </ul>
          </div>
        </div>
        <!-- /.card -->
      </div>
    </div>
    <!-- /.row -->
  </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- Delete Modal -->
<div class="modal fade" id="delete-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Delete Article</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this article?</p>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <form action="/admin/articles/delete" method="post" id="delete-form">
          <input type="hidden" name="id" id="article-id-input">
          <button type="submit" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>
$(function() {
  $('#delete-modal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var articleId = button.data('article-id');
    var modal = $(this);
    modal.find('#article-id-input').val(articleId);
  });
});
</script>