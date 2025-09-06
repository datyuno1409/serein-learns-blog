<?php require_once 'views/layouts/frontend.php'; ?>

<!-- Background Effects -->
<div class="fixed-top" style="height: 100vh; overflow: hidden; z-index: -1;">
  <div class="position-absolute w-100 h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); opacity: 0.05;"></div>
  <div class="position-absolute" style="top: 10%; left: 10%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%); border-radius: 50%;"></div>
  <div class="position-absolute" style="top: 60%; right: 15%; width: 200px; height: 200px; background: radial-gradient(circle, rgba(118, 75, 162, 0.1) 0%, transparent 70%); border-radius: 50%;"></div>
</div>

<!-- Hero Section -->
<div class="container-fluid py-5 mb-4" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); position: relative; overflow: hidden;">
  <div class="position-absolute" style="top: -50%; left: -10%; width: 200px; height: 200px; background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05)); border-radius: 50%; transform: rotate(45deg);"></div>
  <div class="position-absolute" style="bottom: -30%; right: -5%; width: 150px; height: 150px; background: linear-gradient(45deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.03)); border-radius: 50%; transform: rotate(-45deg);"></div>
  
  <div class="container">
    <div class="text-center">
      <div class="d-inline-block px-4 py-2 mb-3 rounded-pill" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1)); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2);">
        <i class="fas fa-user-circle me-2" style="color: #667eea;"></i>
        <span style="color: #4a5568; font-weight: 500;">Hồ Sơ Cá Nhân</span>
      </div>
      <h1 class="display-4 fw-bold mb-3" style="background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Quản Lý Thông Tin</h1>
      <p class="lead text-muted mb-0">Cập nhật thông tin cá nhân và theo dõi hoạt động của bạn</p>
    </div>
  </div>
</div>

<div class="container">
<div class="profile-row row">
  <div class="col-md-4">
    <!-- Profile Card -->
    <div class="card border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.8)); backdrop-filter: blur(10px); border-radius: 20px; overflow: hidden;">
      <div class="position-absolute w-100" style="height: 100px; background: linear-gradient(135deg, #667eea, #764ba2); opacity: 0.1;"></div>
      
      <div class="card-body text-center p-4" style="position: relative;">
        <div class="position-relative d-inline-block mb-3">
          <img class="rounded-circle border border-4 border-white shadow-lg" 
               src="<?= $user['avatar'] ?: '/img/default-avatar.png' ?>" 
               alt="User profile picture"
               style="width: 120px; height: 120px; object-fit: cover;">
          <div class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-3 border-white" style="width: 24px; height: 24px;"></div>
        </div>

        <h3 class="fw-bold mb-2" style="color: #2d3748;"><?= htmlspecialchars($user['username']) ?></h3>
        
        <div class="d-inline-block px-3 py-1 rounded-pill mb-3" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; font-size: 0.85rem; font-weight: 500;">
          <i class="fas fa-crown me-1"></i>
          <?= ucfirst($user['role']) ?>
        </div>
        
        <p class="text-muted mb-4">
          <i class="fas fa-calendar-alt me-2"></i>
          Thành viên từ <?= date('F Y', strtotime($user['created_at'])) ?>
        </p>

        <!-- Stats -->
        <div class="row g-3">
          <div class="col-6">
            <div class="p-3 rounded-3" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(102, 126, 234, 0.05));">
              <div class="d-flex align-items-center justify-content-center mb-2">
                <i class="fas fa-newspaper" style="color: #667eea; font-size: 1.5rem;"></i>
              </div>
              <h4 class="fw-bold mb-1" style="color: #2d3748;"><?= $user['article_count'] ?></h4>
              <small class="text-muted">Bài viết</small>
            </div>
          </div>
          <div class="col-6">
            <div class="p-3 rounded-3" style="background: linear-gradient(135deg, rgba(118, 75, 162, 0.1), rgba(118, 75, 162, 0.05));">
              <div class="d-flex align-items-center justify-content-center mb-2">
                <i class="fas fa-comments" style="color: #764ba2; font-size: 1.5rem;"></i>
              </div>
              <h4 class="fw-bold mb-1" style="color: #2d3748;"><?= $user['comment_count'] ?></h4>
              <small class="text-muted">Bình luận</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="profile-main col-md-8">
    <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.9)); backdrop-filter: blur(10px); border-radius: 20px; overflow: hidden;">
      <div class="position-absolute w-100" style="height: 4px; background: linear-gradient(90deg, #667eea, #764ba2);"></div>
      
      <div class="card-header border-0 bg-transparent p-4">
        <ul class="nav nav-pills justify-content-center" style="background: rgba(102, 126, 234, 0.05); border-radius: 15px; padding: 8px;">
          <li class="nav-item">
            <a class="nav-link active px-4 py-2 rounded-pill fw-semibold" href="#profile" data-toggle="tab" 
               style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; transition: all 0.3s ease;">
              <i class="fas fa-user me-2"></i>Hồ Sơ
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-4 py-2 rounded-pill fw-semibold" href="#articles" data-toggle="tab" 
               style="color: #667eea; border: none; transition: all 0.3s ease;">
              <i class="fas fa-newspaper me-2"></i>Bài Viết
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-4 py-2 rounded-pill fw-semibold" href="#comments" data-toggle="tab" 
               style="color: #667eea; border: none; transition: all 0.3s ease;">
              <i class="fas fa-comments me-2"></i>Bình Luận
            </a>
          </li>
        </ul>
      </div>
      <div class="card-body p-4">
        <div class="tab-content">
          <!-- Profile Tab -->
          <div class="active tab-pane" id="profile">
            <?php if (isset($_SESSION['success'])): ?>
              <div class="alert border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(34, 197, 94, 0.05)); border-left: 4px solid #22c55e !important;" role="alert">
                <div class="d-flex align-items-center">
                  <i class="fas fa-check-circle me-3" style="color: #22c55e; font-size: 1.2rem;"></i>
                  <div>
                    <strong style="color: #166534;">Thành công!</strong>
                    <div style="color: #166534;"><?= $_SESSION['success'] ?></div>
                  </div>
                  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              </div>
              <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
              <div class="alert border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05)); border-left: 4px solid #ef4444 !important;" role="alert">
                <div class="d-flex align-items-center">
                  <i class="fas fa-exclamation-circle me-3" style="color: #ef4444; font-size: 1.2rem;"></i>
                  <div>
                    <strong style="color: #991b1b;">Lỗi!</strong>
                    <div style="color: #991b1b;"><?= $_SESSION['error'] ?></div>
                  </div>
                  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              </div>
              <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="row">
              <div class="col-lg-8 mx-auto">
                <form method="POST" action="/profile/update" enctype="multipart/form-data">
                  <div class="mb-4">
                    <label for="username" class="form-label fw-semibold" style="color: #374151;">
                      <i class="fas fa-user me-2" style="color: #667eea;"></i>Tên người dùng
                    </label>
                    <input type="text" class="form-control form-control-lg border-0 shadow-sm" id="username" name="username" 
                           value="<?= htmlspecialchars($user['username']) ?>" required
                           style="background: rgba(102, 126, 234, 0.05); border-radius: 12px; padding: 12px 16px;">
                  </div>
                  
                  <div class="mb-4">
                    <label for="email" class="form-label fw-semibold" style="color: #374151;">
                      <i class="fas fa-envelope me-2" style="color: #667eea;"></i>Email
                    </label>
                    <input type="email" class="form-control form-control-lg border-0 shadow-sm" id="email" name="email" 
                           value="<?= htmlspecialchars($user['email']) ?>" required
                           style="background: rgba(102, 126, 234, 0.05); border-radius: 12px; padding: 12px 16px;">
                  </div>

                  <div class="mb-4">
                    <label for="avatar" class="form-label fw-semibold" style="color: #374151;">
                      <i class="fas fa-image me-2" style="color: #667eea;"></i>Ảnh đại diện
                    </label>
                    <input type="file" class="form-control form-control-lg border-0 shadow-sm" id="avatar" name="avatar" accept="image/*"
                           style="background: rgba(102, 126, 234, 0.05); border-radius: 12px; padding: 12px 16px;">
                    <small class="text-muted mt-2 d-block">
                      <i class="fas fa-info-circle me-1"></i>Để trống nếu không muốn thay đổi ảnh đại diện
                    </small>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-4">
                        <label for="current_password" class="form-label fw-semibold" style="color: #374151;">
                          <i class="fas fa-lock me-2" style="color: #667eea;"></i>Mật khẩu hiện tại
                        </label>
                        <input type="password" class="form-control form-control-lg border-0 shadow-sm" id="current_password" name="current_password"
                               style="background: rgba(102, 126, 234, 0.05); border-radius: 12px; padding: 12px 16px;">
                        <small class="text-muted mt-2 d-block">
                          <i class="fas fa-info-circle me-1"></i>Chỉ cần nhập khi thay đổi mật khẩu
                        </small>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-4">
                        <label for="new_password" class="form-label fw-semibold" style="color: #374151;">
                          <i class="fas fa-key me-2" style="color: #667eea;"></i>Mật khẩu mới
                        </label>
                        <input type="password" class="form-control form-control-lg border-0 shadow-sm" id="new_password" name="new_password"
                               style="background: rgba(102, 126, 234, 0.05); border-radius: 12px; padding: 12px 16px;">
                        <small class="text-muted mt-2 d-block">
                          <i class="fas fa-info-circle me-1"></i>Để trống nếu không muốn thay đổi
                        </small>
                      </div>
                    </div>
                  </div>

                  <div class="text-center pt-3">
                    <button type="submit" class="btn btn-lg px-5 py-3 border-0 shadow-lg fw-semibold" 
                            style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; border-radius: 15px; transition: all 0.3s ease;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.3)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.2)'">
                      <i class="fas fa-save me-2"></i>Cập Nhật Hồ Sơ
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Articles Tab -->
          <div class="tab-pane" id="articles">
            <div class="row g-4">
              <?php if (!empty($articles)): ?>
                <?php foreach ($articles as $article): ?>
                  <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.8)); border-radius: 15px; overflow: hidden; transition: all 0.3s ease;"
                         onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.1)'"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.05)'">
                      <div class="position-absolute w-100" style="height: 4px; background: linear-gradient(90deg, #667eea, #764ba2);"></div>
                      
                      <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-3" style="color: #2d3748; line-height: 1.4;">
                          <a href="/article/<?= $article['id'] ?>" class="text-decoration-none" style="color: inherit;">
                            <?= htmlspecialchars($article['title']) ?>
                          </a>
                        </h5>
                        
                        <div class="d-flex align-items-center mb-3">
                          <div class="d-inline-block px-3 py-1 rounded-pill me-3" style="background: rgba(102, 126, 234, 0.1); color: #667eea; font-size: 0.8rem; font-weight: 500;">
                            <i class="fas fa-calendar-alt me-1"></i>
                            <?= date('d/m/Y', strtotime($article['created_at'])) ?>
                          </div>
                        </div>
                        
                        <p class="card-text text-muted mb-4" style="line-height: 1.6;">
                          <?= substr(strip_tags($article['content']), 0, 120) ?>...
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                          <div class="d-flex gap-3">
                            <small class="d-flex align-items-center" style="color: #667eea;">
                              <i class="fas fa-eye me-1"></i> <?= $article['views'] ?>
                            </small>
                            <small class="d-flex align-items-center" style="color: #764ba2;">
                              <i class="fas fa-comments me-1"></i> <?= $article['comment_count'] ?>
                            </small>
                          </div>
                          <a href="/article/<?= $article['id'] ?>" class="btn btn-sm px-3 py-2 border-0" 
                             style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; border-radius: 8px; font-weight: 500;">
                            <i class="fas fa-arrow-right me-1"></i>Đọc
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="col-12">
                  <div class="text-center py-5">
                    <div class="mb-4">
                      <i class="fas fa-newspaper" style="font-size: 4rem; color: #e2e8f0;"></i>
                    </div>
                    <h4 class="fw-bold mb-3" style="color: #4a5568;">Chưa có bài viết nào</h4>
                    <p class="text-muted mb-4">Bạn chưa viết bài viết nào. Hãy bắt đầu chia sẻ kiến thức của mình!</p>
                    <a href="/articles/create" class="btn btn-lg px-4 py-3 border-0 shadow-lg" 
                       style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; border-radius: 12px; font-weight: 500;">
                      <i class="fas fa-plus me-2"></i>Viết Bài Đầu Tiên
                    </a>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Comments Tab -->
          <div class="tab-pane" id="comments">
            <div class="row g-4">
              <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                  <div class="col-12">
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.8)); border-radius: 15px; overflow: hidden; transition: all 0.3s ease;"
                         onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.1)'"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.05)'">
                      <div class="position-absolute w-100" style="height: 4px; background: linear-gradient(90deg, #667eea, #764ba2);"></div>
                      
                      <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                          <div>
                            <h6 class="fw-bold mb-2" style="color: #2d3748;">
                              <a href="/article/<?= $comment['article_id'] ?>" class="text-decoration-none" style="color: inherit;">
                                <i class="fas fa-newspaper me-2" style="color: #667eea;"></i>
                                <?= htmlspecialchars($comment['article_title']) ?>
                              </a>
                            </h6>
                            <small class="text-muted">
                              <i class="fas fa-clock me-1"></i>
                              <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                            </small>
                          </div>
                          <?php if ($comment['status'] === 'pending'): ?>
                            <span class="badge px-3 py-2 rounded-pill" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; font-weight: 500;">
                              <i class="fas fa-clock me-1"></i>Chờ duyệt
                            </span>
                          <?php else: ?>
                            <span class="badge px-3 py-2 rounded-pill" style="background: linear-gradient(135deg, #10b981, #059669); color: white; font-weight: 500;">
                              <i class="fas fa-check me-1"></i>Đã duyệt
                            </span>
                          <?php endif; ?>
                        </div>
                        
                        <div class="p-3 rounded-3" style="background: rgba(102, 126, 234, 0.05); border-left: 3px solid #667eea;">
                          <p class="mb-0" style="color: #4a5568; line-height: 1.6;">
                            <?= nl2br(htmlspecialchars($comment['content'])) ?>
                          </p>
                        </div>
                        
                        <div class="mt-3 d-flex justify-content-end">
                          <a href="/article/<?= $comment['article_id'] ?>" class="btn btn-sm px-3 py-2 border-0" 
                             style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; border-radius: 8px; font-weight: 500;">
                            <i class="fas fa-external-link-alt me-1"></i>Xem Bài Viết
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="col-12">
                  <div class="text-center py-5">
                    <div class="mb-4">
                      <i class="fas fa-comments" style="font-size: 4rem; color: #e2e8f0;"></i>
                    </div>
                    <h4 class="fw-bold mb-3" style="color: #4a5568;">Chưa có bình luận nào</h4>
                    <p class="text-muted mb-4">Bạn chưa bình luận bài viết nào. Hãy tham gia thảo luận với cộng đồng!</p>
                    <a href="/" class="btn btn-lg px-4 py-3 border-0 shadow-lg" 
                       style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; border-radius: 12px; font-weight: 500;">
                      <i class="fas fa-home me-2"></i>Khám Phá Bài Viết
                    </a>
                  </div>
                </div>
              <?php endif; ?>
            </div>
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