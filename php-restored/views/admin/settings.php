<!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Success/Error Messages -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> Settings saved successfully!
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> 
                        <?php if ($_GET['error'] === 'database'): ?>
                            Database connection error. Please try again.
                        <?php elseif ($_GET['error'] === 'save'): ?>
                            Error saving settings. Please try again.
                        <?php else: ?>
                            An error occurred. Please try again.
                        <?php endif; ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Settings Form -->
                <div class="card card-primary card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" id="settings-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="general-tab" data-toggle="pill" href="#general" role="tab">
                                    <i class="fas fa-cog"></i> General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="seo-tab" data-toggle="pill" href="#seo" role="tab">
                                    <i class="fas fa-search"></i> SEO
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="social-tab" data-toggle="pill" href="#social" role="tab">
                                    <i class="fas fa-share-alt"></i> Social Media
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="advanced-tab" data-toggle="pill" href="#advanced" role="tab">
                                    <i class="fas fa-tools"></i> Advanced
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/admin/settings/save" id="settings-form">
                            <div class="tab-content" id="settings-tab-content">
                                <!-- General Settings -->
                                <div class="tab-pane fade show active" id="general" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="site_name"><i class="fas fa-globe"></i> Site Name</label>
                                                <input type="text" class="form-control" id="site_name" name="site_name" 
                                                       value="<?= htmlspecialchars($settings['site_name'] ?? 'Serein Blog') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="admin_email"><i class="fas fa-envelope"></i> Admin Email</label>
                                                <input type="email" class="form-control" id="admin_email" name="admin_email" 
                                                       value="<?= htmlspecialchars($settings['admin_email'] ?? 'admin@example.com') ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="site_description"><i class="fas fa-align-left"></i> Site Description</label>
                                        <textarea class="form-control" id="site_description" name="site_description" rows="3"><?= htmlspecialchars($settings['site_description'] ?? 'A modern blog platform') ?></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="site_url"><i class="fas fa-link"></i> Site URL</label>
                                                <input type="url" class="form-control" id="site_url" name="site_url" 
                                                       value="<?= htmlspecialchars($settings['site_url'] ?? 'http://localhost:8000') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="posts_per_page"><i class="fas fa-list"></i> Posts Per Page</label>
                                                <input type="number" class="form-control" id="posts_per_page" name="posts_per_page" 
                                                       value="<?= htmlspecialchars($settings['posts_per_page'] ?? '10') ?>" min="1" max="50">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="timezone"><i class="fas fa-clock"></i> Timezone</label>
                                        <select class="form-control" id="timezone" name="timezone">
                                            <option value="Asia/Ho_Chi_Minh" <?= ($settings['timezone'] ?? 'Asia/Ho_Chi_Minh') === 'Asia/Ho_Chi_Minh' ? 'selected' : '' ?>>Asia/Ho_Chi_Minh</option>
                                            <option value="UTC" <?= ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : '' ?>>UTC</option>
                                            <option value="America/New_York" <?= ($settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>America/New_York</option>
                                            <option value="Europe/London" <?= ($settings['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' ?>>Europe/London</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- SEO Settings -->
                                <div class="tab-pane fade" id="seo" role="tabpanel">
                                    <div class="form-group">
                                        <label for="meta_title"><i class="fas fa-tag"></i> Meta Title</label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                               value="<?= htmlspecialchars($settings['meta_title'] ?? 'Serein Blog - Modern Blog Platform') ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="meta_description"><i class="fas fa-file-alt"></i> Meta Description</label>
                                        <textarea class="form-control" id="meta_description" name="meta_description" rows="3"><?= htmlspecialchars($settings['meta_description'] ?? 'A modern blog platform built with PHP and MySQL') ?></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="meta_keywords"><i class="fas fa-tags"></i> Meta Keywords</label>
                                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                                               value="<?= htmlspecialchars($settings['meta_keywords'] ?? 'blog, php, mysql, programming') ?>">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="google_analytics"><i class="fab fa-google"></i> Google Analytics ID</label>
                                                <input type="text" class="form-control" id="google_analytics" name="google_analytics" 
                                                       value="<?= htmlspecialchars($settings['google_analytics'] ?? '') ?>" placeholder="G-XXXXXXXXXX">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="google_search_console"><i class="fas fa-search"></i> Google Search Console</label>
                                                <input type="text" class="form-control" id="google_search_console" name="google_search_console" 
                                                       value="<?= htmlspecialchars($settings['google_search_console'] ?? '') ?>" placeholder="Verification code">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Social Media Settings -->
                                <div class="tab-pane fade" id="social" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="facebook_url"><i class="fab fa-facebook"></i> Facebook URL</label>
                                                <input type="url" class="form-control" id="facebook_url" name="facebook_url" 
                                                       value="<?= htmlspecialchars($settings['facebook_url'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="twitter_url"><i class="fab fa-twitter"></i> Twitter URL</label>
                                                <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                                                       value="<?= htmlspecialchars($settings['twitter_url'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="instagram_url"><i class="fab fa-instagram"></i> Instagram URL</label>
                                                <input type="url" class="form-control" id="instagram_url" name="instagram_url" 
                                                       value="<?= htmlspecialchars($settings['instagram_url'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="linkedin_url"><i class="fab fa-linkedin"></i> LinkedIn URL</label>
                                                <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                                                       value="<?= htmlspecialchars($settings['linkedin_url'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Advanced Settings -->
                                <div class="tab-pane fade" id="advanced" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="enable_comments" name="enable_comments" 
                                                           <?= ($settings['enable_comments'] ?? true) ? 'checked' : '' ?>>
                                                    <label class="custom-control-label" for="enable_comments">
                                                        <i class="fas fa-comments"></i> Enable Comments
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="enable_registration" name="enable_registration" 
                                                           <?= ($settings['enable_registration'] ?? true) ? 'checked' : '' ?>>
                                                    <label class="custom-control-label" for="enable_registration">
                                                        <i class="fas fa-user-plus"></i> Enable Registration
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="maintenance_mode" name="maintenance_mode" 
                                                           <?= ($settings['maintenance_mode'] ?? false) ? 'checked' : '' ?>>
                                                    <label class="custom-control-label" for="maintenance_mode">
                                                        <i class="fas fa-tools"></i> Maintenance Mode
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Warning:</strong> Enabling maintenance mode will make your site inaccessible to regular users.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Settings
                                </button>
                                <button type="reset" class="btn btn-secondary ml-2">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>