<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="/assets/images/avatar.svg" alt="Profile" class="rounded-circle mb-3" width="150" height="150" style="object-fit: cover;">
                        <h1 class="h2"><?= __('about.name') ?></h1>
                        <p class="text-muted"><?= __('about.title') ?></p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h3><?= __('about.about_me') ?></h3>
                            <p><?= __('about.description') ?></p>
                            
                            <h4><?= __('about.skills') ?></h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-code text-primary"></i> PHP & Laravel</li>
                                        <li><i class="fas fa-code text-primary"></i> JavaScript & React</li>
                                        <li><i class="fas fa-code text-primary"></i> Python & Django</li>
                                        <li><i class="fas fa-database text-primary"></i> MySQL & PostgreSQL</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="fab fa-html5 text-primary"></i> HTML5 & CSS3</li>
                                        <li><i class="fab fa-bootstrap text-primary"></i> Bootstrap & Tailwind</li>
                                        <li><i class="fab fa-git-alt text-primary"></i> Git & GitHub</li>
                                        <li><i class="fas fa-server text-primary"></i> Linux & Docker</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <h4><?= __('about.experience') ?></h4>
                            <div class="timeline">
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1"><?= __('about.current_position') ?></h6>
                                        <p class="text-muted small mb-1"><?= __('about.current_duration') ?></p>
                                        <p><?= __('about.current_description') ?></p>
                                    </div>
                                </div>
                                
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker bg-secondary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1"><?= __('about.previous_position') ?></h6>
                                        <p class="text-muted small mb-1"><?= __('about.previous_duration') ?></p>
                                        <p><?= __('about.previous_description') ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <h4><?= __('about.education') ?></h4>
                            <div class="mb-3">
                                <h6><?= __('about.degree') ?></h6>
                                <p class="text-muted"><?= __('about.university') ?> - <?= __('about.graduation_year') ?></p>
                            </div>
                            
                            <h4><?= __('about.contact') ?></h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><i class="fas fa-envelope text-primary"></i> <a href="mailto:contact@serein.dev">contact@serein.dev</a></p>
                                    <p><i class="fab fa-github text-primary"></i> <a href="https://github.com/serein" target="_blank">github.com/serein</a></p>
                                </div>
                                <div class="col-md-6">
                                    <p><i class="fab fa-linkedin text-primary"></i> <a href="https://linkedin.com/in/serein" target="_blank">linkedin.com/in/serein</a></p>
                                    <p><i class="fas fa-globe text-primary"></i> <a href="https://serein.dev" target="_blank">serein.dev</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
}

.timeline-marker {
    position: absolute;
    left: -37px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}
</style>