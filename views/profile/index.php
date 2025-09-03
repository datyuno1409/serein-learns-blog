<?php require_once 'views/layouts/frontend.php'; ?>

<div class="row mt-4">
  <div class="col-md-3">
    <!-- Profile Image -->
    <div class="card card-primary card-outline">
      <div class="card-body box-profile">
        <div class="text-center">
          <img class="profile-user-img img-fluid img-circle" 
               src="<?= $user['avatar'] ?: '/img/default-avatar.png' ?>" 
               alt="User profile picture">
        </div>

        <h3 class="profile-username text-center"><?= htmlspecialchars($user['username']) ?></h3>

        <p class="text-muted text-center">
          <?= ucfirst($user['role']) ?><br>
          Member since <?= date('F Y', strtotime($user['created_at'])) ?>
        </p>

        <ul class="list-group list-group-unbordered mb-3">
          <li class="list-group-item">
            <b>Articles</b> <a class="float-right"><?= $user['article_count'] ?></a>
          </li>
          <li class="list-group-item">
            <b>Comments</b> <a class="float-right"><?= $user['comment_count'] ?></a>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="col-md-9">
    <div class="card">
      <div class="card-header p-2">
        <ul class="nav nav-pills">
          <li class="nav-item">
            <a class="nav-link active" href="#profile" data-toggle="tab">Profile</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#articles" data-toggle="tab">Articles</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#comments" data-toggle="tab">Comments</a>
          </li>
        </ul>
      </div>
      <div class="card-body">
        <div class="tab-content">
          <!-- Profile Tab -->
          <div class="active tab-pane" id="profile">
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

            <form class="form-horizontal" action="/profile/update" method="post" enctype="multipart/form-data">
              <div class="form-group row">
                <label for="username" class="col-sm-2 col-form-label">Username</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
              </div>
              <div class="form-group row">
                <label for="email" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                  <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
              </div>
              <div class="form-group row">
                <label for="avatar" class="col-sm-2 col-form-label">Avatar</label>
                <div class="col-sm-10">
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="avatar" name="avatar" accept="image/*">
                    <label class="custom-file-label" for="avatar">Choose file</label>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label for="current_password" class="col-sm-2 col-form-label">Current Password</label>
                <div class="col-sm-10">
                  <input type="password" class="form-control" id="current_password" name="current_password">
                  <small class="form-text text-muted">Required to update profile</small>
                </div>
              </div>
              <div class="form-group row">
                <label for="new_password" class="col-sm-2 col-form-label">New Password</label>
                <div class="col-sm-10">
                  <input type="password" class="form-control" id="new_password" name="new_password">
                  <small class="form-text text-muted">Leave empty to keep current password</small>
                </div>
              </div>
              <div class="form-group row">
                <div class="offset-sm-2 col-sm-10">
                  <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
              </div>
            </form>
          </div>

          <!-- Articles Tab -->
          <div class="tab-pane" id="articles">
            <?php if (empty($articles)): ?>
              <div class="text-center text-muted">
                No articles published yet.
              </div>
            <?php else: ?>
              <?php foreach ($articles as $article): ?>
                <div class="post">
                  <div class="user-block">
                    <span class="username">
                      <a href="/article/<?= $article['id'] ?>"><?= htmlspecialchars($article['title']) ?></a>
                    </span>
                    <span class="description">
                      Published - <?= date('F j, Y', strtotime($article['created_at'])) ?>
                    </span>
                  </div>
                  <p class="mb-0">
                    <?= substr(strip_tags($article['content']), 0, 200) ?>...
                  </p>
                  <p>
                    <span class="text-muted">
                      <i class="fas fa-eye"></i> <?= $article['views'] ?> views
                      <i class="fas fa-comments ml-2"></i> <?= $article['comment_count'] ?> comments
                    </span>
                  </p>
                </div>
                <hr>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <!-- Comments Tab -->
          <div class="tab-pane" id="comments">
            <?php if (empty($comments)): ?>
              <div class="text-center text-muted">
                No comments posted yet.
              </div>
            <?php else: ?>
              <?php foreach ($comments as $comment): ?>
                <div class="post">
                  <div class="user-block">
                    <span class="username">
                      <a href="/article/<?= $comment['article_id'] ?>"><?= htmlspecialchars($comment['article_title']) ?></a>
                    </span>
                    <span class="description">
                      Posted - <?= date('F j, Y g:i a', strtotime($comment['created_at'])) ?>
                    </span>
                  </div>
                  <p>
                    <?= nl2br(htmlspecialchars($comment['content'])) ?>
                  </p>
                  <?php if ($comment['status'] === 'pending'): ?>
                    <span class="badge badge-warning">Pending Approval</span>
                  <?php endif; ?>
                </div>
                <hr>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- bs-custom-file-input -->
<script src="/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<script>
$(function () {
  bsCustomFileInput.init();
});</script>