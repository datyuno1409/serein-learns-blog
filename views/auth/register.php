<?php require_once 'views/layouts/frontend.php'; ?>

<div class="row justify-content-center mt-5">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><?= __('auth.register') ?></h3>
      </div>
      <div class="card-body">
        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']); ?>
          </div>
        <?php endif; ?>

        <form action="/register" method="post">
          <div class="form-group">
            <label for="username"><?= __('auth.username') ?></label>
            <input type="text" class="form-control" id="username" name="username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required>
          </div>
          <div class="form-group">
            <label for="email"><?= __('auth.email') ?></label>
            <input type="email" class="form-control" id="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
          </div>
          <div class="form-group">
            <label for="password"><?= __('auth.password') ?></label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <div class="form-group">
            <label for="password_confirm"><?= __('auth.confirm_password') ?></label>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block"><?= __('auth.register') ?></button>
        </form>

        <div class="text-center mt-3">
          <?= __('auth.already_have_account') ?> <a href="/login"><?= __('auth.login_here') ?></a>
        </div>
      </div>
    </div>
  </div>
</div>