<!-- Hero Section -->
<section class="hero-section relative bg-gradient-to-br from-serein-500 via-serein-600 to-teal-700 text-white py-24 overflow-hidden">
  <!-- Background Pattern -->
  <div class="absolute inset-0 opacity-10">
    <div class="absolute top-0 left-0 w-full h-full" style="background-image: radial-gradient(circle at 25% 25%, white 2px, transparent 2px); background-size: 50px 50px;"></div>
  </div>
  
  <div class="container mx-auto text-center relative z-10">
    <!-- Welcome Badge -->
    <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-medium mb-6">
      <i class="fas fa-rocket text-yellow-300"></i>
      <span>Chào mừng đến với Learning with Serein</span>
    </div>
    
    <h1 class="hero-title text-5xl md:text-7xl font-bold mb-6 leading-tight">
      <span class="bg-gradient-to-r from-white to-serein-100 bg-clip-text text-transparent">
        Học Tập & Phát Triển
      </span>
      <br>
      <span class="text-yellow-300">Learning With Serein</span>
    </h1>
    
    <p class="hero-subtitle text-xl md:text-2xl mb-10 text-serein-50 max-w-4xl mx-auto leading-relaxed">
      Khám phá thế giới công nghệ, bảo mật mạng và phát triển phần mềm qua những bài viết chất lượng và dự án thực tế
    </p>
    
    <div class="btn-group flex flex-col sm:flex-row gap-4 justify-center mb-12">
      <a href="/articles" class="group bg-white text-serein-600 px-8 py-4 rounded-xl font-semibold hover:bg-serein-50 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
        <i class="fas fa-book-open mr-2 group-hover:rotate-12 transition-transform"></i>
        Khám Phá Bài Viết
      </a>
      <a href="/myprojects" class="group border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white hover:text-serein-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
        <i class="fas fa-code mr-2 group-hover:rotate-12 transition-transform"></i>
        Xem Dự Án
      </a>
      <a href="/about" class="group bg-gradient-to-r from-yellow-400 to-orange-500 text-gray-900 px-8 py-4 rounded-xl font-semibold hover:from-yellow-300 hover:to-orange-400 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
        <i class="fas fa-user mr-2 group-hover:rotate-12 transition-transform"></i>
        Về Tôi
      </a>
    </div>
    
    <!-- Stats Section -->
    <div class="stats-grid grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
      <div class="stat-item bg-white/10 backdrop-blur-sm rounded-xl p-4">
        <div class="stat-number text-2xl md:text-3xl font-bold text-yellow-300">50+</div>
        <div class="text-sm text-serein-100">Bài Viết</div>
      </div>
      <div class="stat-item bg-white/10 backdrop-blur-sm rounded-xl p-4">
        <div class="stat-number text-2xl md:text-3xl font-bold text-yellow-300">20+</div>
        <div class="text-sm text-serein-100">Dự Án</div>
      </div>
      <div class="stat-item bg-white/10 backdrop-blur-sm rounded-xl p-4">
        <div class="stat-number text-2xl md:text-3xl font-bold text-yellow-300">1000+</div>
        <div class="text-sm text-serein-100">Lượt Xem</div>
      </div>
      <div class="stat-item bg-white/10 backdrop-blur-sm rounded-xl p-4">
        <div class="stat-number text-2xl md:text-3xl font-bold text-yellow-300">5+</div>
        <div class="text-sm text-serein-100">Năm Kinh Nghiệm</div>
      </div>
    </div>
  </div>
</section>

<!-- Search Bar Section -->
<section class="py-20 bg-gradient-to-b from-gray-50 to-white">
  <div class="container mx-auto">
    <div class="max-w-5xl mx-auto">
      <div class="text-center mb-12">
        <h2 class="text-4xl md:text-5xl font-bold mb-4 bg-gradient-to-r from-serein-600 to-teal-600 bg-clip-text text-transparent">
          Tìm Kiếm Nội Dung
        </h2>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
          Khám phá hàng trăm bài viết về công nghệ, lập trình và bảo mật mạng
        </p>
      </div>
      
      <form action="/search" method="GET" class="relative">
        <div class="relative group">
          <div class="absolute inset-0 bg-gradient-to-r from-serein-500 to-teal-500 rounded-2xl blur opacity-25 group-hover:opacity-40 transition-opacity"></div>
          <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="flex flex-col md:flex-row">
              <div class="flex-1 relative">
                <i class="fas fa-search absolute left-6 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                <input type="text" name="q" placeholder="Tìm kiếm bài viết, dự án, chủ đề..." 
                       value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                       class="w-full pl-14 pr-6 py-6 text-lg border-0 focus:ring-0 focus:outline-none bg-transparent placeholder-gray-400">
              </div>
              <button type="submit" class="bg-gradient-to-r from-serein-600 to-teal-600 text-white px-10 py-6 font-semibold hover:from-serein-700 hover:to-teal-700 transition-all duration-300 md:rounded-r-2xl">
                <i class="fas fa-search mr-2"></i>
                <span class="hidden md:inline">Tìm Kiếm</span>
              </button>
            </div>
          </div>
        </div>
      </form>
      
      <!-- Quick Search Tags -->
      <div class="mt-8 text-center">
        <p class="text-sm text-gray-500 mb-4">Tìm kiếm phổ biến:</p>
        <div class="flex flex-wrap justify-center gap-3">
          <a href="/search?q=cybersecurity" class="bg-white border border-gray-200 px-4 py-2 rounded-full text-sm hover:border-serein-300 hover:bg-serein-50 transition-colors">
            <i class="fas fa-shield-alt mr-1 text-serein-500"></i>
            Cybersecurity
          </a>
          <a href="/search?q=web+development" class="bg-white border border-gray-200 px-4 py-2 rounded-full text-sm hover:border-serein-300 hover:bg-serein-50 transition-colors">
            <i class="fas fa-code mr-1 text-serein-500"></i>
            Web Development
          </a>
          <a href="/search?q=machine+learning" class="bg-white border border-gray-200 px-4 py-2 rounded-full text-sm hover:border-serein-300 hover:bg-serein-50 transition-colors">
            <i class="fas fa-brain mr-1 text-serein-500"></i>
            Machine Learning
          </a>
          <a href="/search?q=devops" class="bg-white border border-gray-200 px-4 py-2 rounded-full text-sm hover:border-serein-300 hover:bg-serein-50 transition-colors">
            <i class="fas fa-cogs mr-1 text-serein-500"></i>
            DevOps
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Popular Topics -->
<section class="py-16 bg-gradient-to-br from-white via-gray-50 to-serein-50">
  <div class="container mx-auto">
    <div class="text-center mb-12">
      <h2 class="text-3xl md:text-4xl font-bold mb-4 bg-gradient-to-r from-gray-800 to-serein-600 bg-clip-text text-transparent">
        Chủ Đề Phổ Biến
      </h2>
      <p class="text-gray-600 max-w-2xl mx-auto">
        Khám phá các chủ đề được quan tâm nhiều nhất trong cộng đồng
      </p>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4 max-w-6xl mx-auto">
      <?php foreach (array_slice($categories, 0, 8) as $index => $category): ?>
        <a href="/category/<?= $category['id'] ?>" 
           class="group relative bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 hover:border-serein-200">
          <div class="absolute inset-0 bg-gradient-to-br from-serein-500/5 to-teal-500/5 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
          <div class="relative text-center">
            <div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-serein-100 to-teal-100 rounded-lg flex items-center justify-center group-hover:from-serein-500 group-hover:to-teal-500 transition-all duration-300">
              <i class="fas <?= ['fa-code', 'fa-shield-alt', 'fa-brain', 'fa-mobile-alt', 'fa-database', 'fa-cloud', 'fa-cogs', 'fa-chart-line'][$index % 8] ?> text-serein-600 group-hover:text-white transition-colors"></i>
            </div>
            <h3 class="font-semibold text-gray-800 group-hover:text-serein-600 transition-colors mb-1">
              <?= htmlspecialchars($category['name']) ?>
            </h3>
            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full group-hover:bg-serein-100 group-hover:text-serein-600 transition-colors">
              <?= $category['article_count'] ?> bài viết
            </span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-10">
      <a href="/categories" class="inline-flex items-center gap-2 bg-gradient-to-r from-serein-500 to-teal-500 text-white px-8 py-3 rounded-xl font-semibold hover:from-serein-600 hover:to-teal-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
        <i class="fas fa-th-large"></i>
        Xem Tất Cả Chủ Đề
      </a>
    </div>
  </div>
</section>

<!-- Featured Article -->
<?php if (!empty($articles) && isset($articles[0])): ?>
<section class="py-20 bg-gradient-to-br from-gray-900 via-serein-900 to-teal-900 text-white relative overflow-hidden">
  <!-- Background Elements -->
  <div class="absolute inset-0 opacity-10">
    <div class="absolute top-10 left-10 w-32 h-32 bg-yellow-400 rounded-full blur-3xl"></div>
    <div class="absolute bottom-10 right-10 w-40 h-40 bg-teal-400 rounded-full blur-3xl"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-60 h-60 bg-serein-400 rounded-full blur-3xl"></div>
  </div>
  
  <div class="container mx-auto relative z-10">
    <div class="text-center mb-16">
      <div class="inline-flex items-center gap-2 bg-yellow-400/20 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-medium mb-6">
        <i class="fas fa-star text-yellow-400"></i>
        <span>Bài Viết Nổi Bật</span>
      </div>
      <h2 class="text-4xl md:text-5xl font-bold mb-4">
        <span class="bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent">
          Đọc Ngay Hôm Nay
        </span>
      </h2>
      <p class="text-xl text-gray-300 max-w-2xl mx-auto">
        Khám phá bài viết được đánh giá cao nhất tuần này
      </p>
    </div>
    
    <?php $featured = $articles[0]; ?>
    <div class="max-w-6xl mx-auto">
      <article class="group relative">
        <!-- Glow Effect -->
        <div class="absolute inset-0 bg-gradient-to-r from-serein-500/20 to-teal-500/20 rounded-3xl blur-xl group-hover:blur-2xl transition-all duration-500 opacity-0 group-hover:opacity-100"></div>
        
        <div class="relative bg-white/10 backdrop-blur-lg rounded-3xl overflow-hidden border border-white/20 hover:border-white/30 transition-all duration-500">
          <div class="lg:flex">
            <div class="lg:w-1/2 relative overflow-hidden">
              <?php if (isset($featured['featured_image']) && !empty($featured['featured_image'])): ?>
                <img src="<?= htmlspecialchars($featured['featured_image']) ?>" 
                     alt="<?= htmlspecialchars($featured['title']) ?>"
                     class="w-full h-80 lg:h-full object-cover group-hover:scale-105 transition-transform duration-700">
              <?php else: ?>
                <div class="w-full h-80 lg:h-full bg-gradient-to-br from-serein-500 to-teal-600 flex items-center justify-center relative">
                  <div class="absolute inset-0 bg-black/20"></div>
                  <i class="fas fa-newspaper text-white text-8xl opacity-60 relative z-10"></i>
                </div>
              <?php endif; ?>
              
              <!-- Overlay Badge -->
              <div class="absolute top-6 left-6">
                <span class="bg-yellow-400 text-gray-900 px-4 py-2 rounded-full text-sm font-bold shadow-lg">
                  <i class="fas fa-fire mr-1"></i>
                  HOT
                </span>
              </div>
            </div>
            
            <div class="lg:w-1/2 p-8 lg:p-12">
              <div class="flex items-center gap-3 mb-6">
                <span class="bg-gradient-to-r from-serein-400 to-teal-400 text-white px-4 py-2 rounded-full text-sm font-semibold">
                  <?= htmlspecialchars($featured['category_name'] ?? 'Bài viết') ?>
                </span>
                <span class="text-gray-300 text-sm flex items-center gap-1">
                  <i class="fas fa-calendar"></i>
                  <?= date('d/m/Y', strtotime($featured['created_at'])) ?>
                </span>
              </div>
              
              <h3 class="text-3xl lg:text-4xl font-bold mb-6 leading-tight group-hover:text-yellow-300 transition-colors duration-300">
                <a href="/article/<?= $featured['id'] ?>" class="hover:underline">
                  <?= htmlspecialchars($featured['title']) ?>
                </a>
              </h3>
              
              <p class="text-gray-200 mb-8 leading-relaxed text-lg">
                <?= htmlspecialchars(substr(strip_tags($featured['content']), 0, 250)) ?>...
              </p>
              
              <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-6 text-sm text-gray-300">
                  <span class="flex items-center gap-1">
                    <i class="fas fa-eye text-yellow-400"></i>
                    <?= number_format($featured['views']) ?>
                  </span>
                  <span class="flex items-center gap-1">
                    <i class="fas fa-comment text-teal-400"></i>
                    <?= $featured['comment_count'] ?? 0 ?>
                  </span>
                  <span class="flex items-center gap-1">
                    <i class="fas fa-user text-serein-400"></i>
                    <?= htmlspecialchars($featured['author_name'] ?? 'Admin') ?>
                  </span>
                </div>
              </div>
              
              <a href="/article/<?= $featured['id'] ?>" 
                 class="group/btn inline-flex items-center gap-3 bg-gradient-to-r from-yellow-400 to-orange-500 text-gray-900 px-8 py-4 rounded-xl font-bold hover:from-yellow-300 hover:to-orange-400 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <span>Đọc Bài Viết</span>
                <i class="fas fa-arrow-right group-hover/btn:translate-x-1 transition-transform"></i>
              </a>
            </div>
          </div>
        </div>
      </article>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Latest Articles -->
<section class="py-20 bg-gradient-to-b from-white to-gray-50">
  <div class="container mx-auto">
    <div class="text-center mb-16">
      <h2 class="text-4xl md:text-5xl font-bold mb-4 bg-gradient-to-r from-gray-800 to-serein-600 bg-clip-text text-transparent">
        Bài Viết Mới Nhất
      </h2>
      <p class="text-lg text-gray-600 max-w-2xl mx-auto">
        Cập nhật những kiến thức và xu hướng công nghệ mới nhất
      </p>
    </div>
    
    <div class="articles-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
      <?php foreach (array_slice($articles, 1, 6) as $index => $article): ?>
        <article class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-gray-100 hover:border-serein-200 transform hover:-translate-y-2">
          <!-- Gradient Border Effect -->
          <div class="absolute inset-0 bg-gradient-to-r from-serein-500/10 to-teal-500/10 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
          
          <div class="relative">
            <div class="relative overflow-hidden">
              <?php if (isset($article['featured_image']) && !empty($article['featured_image'])): ?>
                <img src="<?= htmlspecialchars($article['featured_image']) ?>" 
                     alt="<?= htmlspecialchars($article['title']) ?>"
                     class="w-full h-56 object-cover group-hover:scale-110 transition-transform duration-700">
              <?php else: ?>
                <div class="w-full h-56 bg-gradient-to-br from-serein-400 via-serein-500 to-teal-600 flex items-center justify-center relative">
                  <div class="absolute inset-0 bg-black/10"></div>
                  <i class="fas fa-newspaper text-white text-5xl opacity-70 relative z-10"></i>
                </div>
              <?php endif; ?>
              
              <!-- Category Badge -->
              <div class="absolute top-4 left-4">
                <span class="bg-gradient-to-r from-serein-500 to-teal-500 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg backdrop-blur-sm">
                  <?= htmlspecialchars($article['category_name']) ?>
                </span>
              </div>
              
              <!-- Reading Time Badge -->
              <div class="absolute top-4 right-4">
                <span class="bg-black/50 backdrop-blur-sm text-white px-2 py-1 rounded-full text-xs">
                  <i class="fas fa-clock mr-1"></i>
                  <?= rand(3, 8) ?> phút
                </span>
              </div>
              
              <!-- Overlay Gradient -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>
            
            <div class="p-6">
              <!-- Meta Info -->
              <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                <span class="flex items-center gap-1">
                  <i class="fas fa-calendar text-serein-500"></i>
                  <?= date('d/m/Y', strtotime($article['created_at'])) ?>
                </span>
                <span class="flex items-center gap-1">
                  <i class="fas fa-eye text-teal-500"></i>
                  <?= number_format($article['views']) ?>
                </span>
              </div>
              
              <!-- Title -->
              <h3 class="text-xl font-bold mb-3 text-gray-800 group-hover:text-serein-600 transition-colors duration-300 leading-tight">
                <a href="/article/<?= $article['id'] ?>" class="line-clamp-2 hover:underline">
                  <?= htmlspecialchars($article['title']) ?>
                </a>
              </h3>
              
              <!-- Excerpt -->
              <p class="text-gray-600 mb-6 line-clamp-3 leading-relaxed">
                <?= htmlspecialchars(substr(strip_tags($article['content']), 0, 140)) ?>...
              </p>
              
              <!-- Footer -->
              <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 bg-gradient-to-r from-serein-400 to-teal-400 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white text-xs"></i>
                  </div>
                  <span class="text-sm text-gray-600 font-medium"><?= htmlspecialchars($article['author_name'] ?? 'Admin') ?></span>
                </div>
                
                <a href="/article/<?= $article['id'] ?>" 
                   class="group/btn inline-flex items-center gap-2 text-serein-600 hover:text-serein-700 font-semibold text-sm transition-all duration-300">
                  <span>Đọc thêm</span>
                  <i class="fas fa-arrow-right text-xs group-hover/btn:translate-x-1 transition-transform"></i>
                </a>
              </div>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    
    <!-- View All Button -->
    <div class="text-center mt-16">
      <a href="/articles" class="group inline-flex items-center gap-3 bg-gradient-to-r from-serein-600 to-teal-600 text-white px-10 py-4 rounded-xl font-bold hover:from-serein-700 hover:to-teal-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
        <i class="fas fa-th-list"></i>
        <span>Xem Tất Cả Bài Viết</span>
        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
      </a>
    </div>
  </div>
</section>

<!-- Cybersecurity Section -->
<?php 
$cybersecurity_articles = array_filter($articles, function($article) {
  return stripos($article['category_name'], 'cyber') !== false || 
         stripos($article['category_name'], 'security') !== false;
});
?>
<section class="py-20 bg-gradient-to-br from-gray-900 via-gray-800 to-black relative overflow-hidden">
  <!-- Background Effects -->
  <div class="absolute inset-0">
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-serein-500/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl"></div>
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.02"%3E%3Ccircle cx="30" cy="30" r="1"%3E%3C/circle%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
  </div>
  
  <div class="container mx-auto relative z-10">
    <div class="text-center mb-16">
      <div class="inline-flex items-center gap-2 bg-gradient-to-r from-serein-500/20 to-teal-500/20 backdrop-blur-sm border border-white/10 rounded-full px-6 py-2 mb-6">
        <i class="fas fa-shield-alt text-serein-400"></i>
        <span class="text-white font-medium">Bảo Mật Cyber</span>
      </div>
      
      <h2 class="text-4xl md:text-6xl font-bold text-white mb-6 leading-tight">
        Bảo Vệ <span class="bg-gradient-to-r from-serein-400 to-teal-400 bg-clip-text text-transparent">Thông Tin</span><br>
        Của Bạn
      </h2>
      
      <p class="text-xl text-gray-300 mb-8 leading-relaxed max-w-3xl mx-auto">
        Khám phá các phương pháp bảo mật tiên tiến và cập nhật xu hướng an ninh mạng mới nhất để bảo vệ dữ liệu cá nhân và doanh nghiệp.
      </p>
    </div>
    
    <div class="projects-grid grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
      <div class="group relative">
        <div class="absolute inset-0 bg-gradient-to-r from-serein-500/20 to-teal-500/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-500"></div>
        <div class="relative bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:bg-white/10 transition-all duration-500 hover:border-serein-400/30">
          <div class="w-20 h-20 bg-gradient-to-r from-serein-500 to-teal-500 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
            <i class="fas fa-shield-alt text-white text-3xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-white mb-4 group-hover:text-serein-300 transition-colors">Bảo Mật Dữ Liệu</h3>
          <p class="text-gray-300 leading-relaxed">Tìm hiểu các phương pháp mã hóa và bảo vệ dữ liệu nhạy cảm khỏi các cuộc tấn công mạng.</p>
          <div class="mt-6 flex items-center text-serein-400 font-medium group-hover:text-serein-300 transition-colors">
            <span>Tìm hiểu thêm</span>
            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
          </div>
        </div>
      </div>
      
      <div class="group relative">
        <div class="absolute inset-0 bg-gradient-to-r from-teal-500/20 to-serein-500/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-500"></div>
        <div class="relative bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:bg-white/10 transition-all duration-500 hover:border-teal-400/30">
          <div class="w-20 h-20 bg-gradient-to-r from-teal-500 to-serein-500 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
            <i class="fas fa-lock text-white text-3xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-white mb-4 group-hover:text-teal-300 transition-colors">Xác Thực Bảo Mật</h3>
          <p class="text-gray-300 leading-relaxed">Triển khai các hệ thống xác thực đa yếu tố và quản lý danh tính an toàn.</p>
          <div class="mt-6 flex items-center text-teal-400 font-medium group-hover:text-teal-300 transition-colors">
            <span>Tìm hiểu thêm</span>
            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
          </div>
        </div>
      </div>
      
      <div class="group relative">
        <div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-pink-500/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-500"></div>
        <div class="relative bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 hover:bg-white/10 transition-all duration-500 hover:border-purple-400/30">
          <div class="w-20 h-20 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
            <i class="fas fa-user-secret text-white text-3xl"></i>
          </div>
          <h3 class="text-2xl font-bold text-white mb-4 group-hover:text-purple-300 transition-colors">Quyền Riêng Tư</h3>
          <p class="text-gray-300 leading-relaxed">Bảo vệ thông tin cá nhân và duy trì quyền riêng tư trong thời đại số.</p>
          <div class="mt-6 flex items-center text-purple-400 font-medium group-hover:text-purple-300 transition-colors">
            <span>Tìm hiểu thêm</span>
            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Cybersecurity Articles -->
    <?php if (!empty($cybersecurity_articles)): ?>
    <div class="mt-20">
      <h3 class="text-3xl font-bold text-white text-center mb-12">Bài Viết Bảo Mật Mới Nhất</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach (array_slice($cybersecurity_articles, 0, 3) as $article): ?>
          <article class="group relative bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl overflow-hidden hover:bg-white/10 transition-all duration-500 hover:border-serein-400/30">
            <?php if (isset($article['featured_image']) && !empty($article['featured_image'])): ?>
              <div class="h-48 bg-cover bg-center group-hover:scale-105 transition-transform duration-700" style="background-image: url('<?= htmlspecialchars($article['featured_image']) ?>')"></div>
            <?php else: ?>
              <div class="h-48 bg-gradient-to-br from-serein-500 to-teal-600 flex items-center justify-center">
                <i class="fas fa-shield-alt text-white text-4xl opacity-70"></i>
              </div>
            <?php endif; ?>
            <div class="p-6">
              <div class="flex items-center gap-2 text-sm mb-3">
                <span class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                  <i class="fas fa-shield-alt mr-1"></i>
                  Security
                </span>
                <span class="text-gray-300"><?= date('M j', strtotime($article['created_at'])) ?></span>
              </div>
              <h3 class="text-xl font-bold mb-3 text-white group-hover:text-serein-300 transition-colors">
                <a href="/article/<?= $article['id'] ?>" class="hover:underline">
                  <?= htmlspecialchars($article['title']) ?>
                </a>
              </h3>
              <p class="text-gray-300 mb-4 leading-relaxed">
                <?= substr(strip_tags($article['content']), 0, 120) ?>...
              </p>
              <a href="/article/<?= $article['id'] ?>" class="inline-flex items-center gap-2 text-serein-400 hover:text-serein-300 font-medium transition-colors">
                <span>Đọc thêm</span>
                <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
    
    <!-- CTA Button -->
    <div class="text-center mt-16">
      <a href="/articles?category=cybersecurity" class="group inline-flex items-center gap-3 bg-gradient-to-r from-serein-600 to-teal-600 text-white px-10 py-4 rounded-xl font-bold hover:from-serein-700 hover:to-teal-700 transition-all duration-300 shadow-2xl hover:shadow-serein-500/25 transform hover:-translate-y-1">
        <i class="fas fa-shield-alt"></i>
        <span>Khám Phá Bảo Mật</span>
        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
      </a>
    </div>
  </div>
</section>

<!-- Newsletter Section -->
<section class="py-20 bg-gradient-to-br from-serein-50 via-white to-teal-50 relative overflow-hidden">
  <!-- Background Pattern -->
  <div class="absolute inset-0 opacity-5">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z" fill="%23059669" fill-opacity="0.4" fill-rule="evenodd"/%3E%3C/svg%3E')]"></div>
  </div>
  
  <div class="container mx-auto relative z-10">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-12">
        <div class="inline-flex items-center gap-2 bg-gradient-to-r from-serein-100 to-teal-100 border border-serein-200 rounded-full px-6 py-2 mb-6">
          <i class="fas fa-envelope text-serein-600"></i>
          <span class="text-serein-700 font-medium">Newsletter</span>
        </div>
        
        <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-gradient-to-r from-gray-800 to-serein-600 bg-clip-text text-transparent">
          Đăng Ký Nhận Tin
        </h2>
        
        <p class="text-xl text-gray-600 leading-relaxed max-w-2xl mx-auto">
          Nhận những bài viết mới nhất về công nghệ, lập trình và bảo mật trực tiếp trong hộp thư của bạn.
        </p>
      </div>
      
      <!-- Newsletter Form -->
      <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12 border border-gray-100 relative overflow-hidden">
        <!-- Background Gradient -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-serein-100/50 to-transparent rounded-full -translate-y-32 translate-x-32"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-gradient-to-tr from-teal-100/50 to-transparent rounded-full translate-y-32 -translate-x-32"></div>
        
        <div class="relative z-10">
          <form action="/newsletter/subscribe" method="POST" class="space-y-6">
            <div class="flex flex-col lg:flex-row gap-4">
              <div class="flex-1">
                <input type="email" 
                       name="email"
                       placeholder="Nhập địa chỉ email của bạn" 
                       required
                       class="w-full px-6 py-4 rounded-xl border-2 border-gray-200 focus:outline-none focus:ring-4 focus:ring-serein-500/20 focus:border-serein-500 transition-all duration-300 text-lg placeholder-gray-400">
              </div>
              <button type="submit" 
                      class="group bg-gradient-to-r from-serein-600 to-teal-600 text-white px-8 py-4 rounded-xl font-bold hover:from-serein-700 hover:to-teal-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center justify-center gap-2">
                <i class="fas fa-paper-plane"></i>
                <span>Đăng Ký Ngay</span>
                <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
              </button>
            </div>
            
            <!-- Benefits -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 pt-8 border-t border-gray-100">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-r from-serein-500 to-teal-500 rounded-full flex items-center justify-center flex-shrink-0">
                  <i class="fas fa-check text-white text-sm"></i>
                </div>
                <span class="text-gray-700 font-medium">Bài viết chất lượng cao</span>
              </div>
              
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-r from-teal-500 to-serein-500 rounded-full flex items-center justify-center flex-shrink-0">
                  <i class="fas fa-clock text-white text-sm"></i>
                </div>
                <span class="text-gray-700 font-medium">Cập nhật hàng tuần</span>
              </div>
              
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center flex-shrink-0">
                  <i class="fas fa-gift text-white text-sm"></i>
                </div>
                <span class="text-gray-700 font-medium">Nội dung độc quyền</span>
              </div>
            </div>
          </form>
          
          <p class="text-sm text-gray-500 mt-6 text-center">
            <i class="fas fa-shield-alt mr-1"></i>
            Chúng tôi tôn trọng quyền riêng tư của bạn. Hủy đăng ký bất cứ lúc nào.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Pagination -->
<?php if ($total_pages > 1): ?>
<section class="py-16 bg-gradient-to-r from-gray-50 to-serein-50">
  <div class="container mx-auto">
    <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
      <div class="text-center mb-8">
        <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-serein-600 bg-clip-text text-transparent mb-2">
          Khám Phá Thêm
        </h3>
        <p class="text-gray-600">
          Trang <?= $current_page ?> / <?= $total_pages ?> - Tổng cộng <?= $total_articles ?> bài viết
        </p>
      </div>
      
      <nav class="flex justify-center">
        <div class="flex items-center space-x-2">
          <?php if ($current_page > 1): ?>
            <a href="/?page=<?= $current_page - 1 ?>" 
               class="group flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-xl hover:from-serein-500 hover:to-teal-500 hover:text-white transition-all duration-300 font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1">
              <i class="fas fa-chevron-left group-hover:-translate-x-1 transition-transform"></i>
              <span class="hidden sm:inline">Trước</span>
            </a>
          <?php endif; ?>
          
          <div class="flex items-center space-x-1">
            <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
              <?php if ($i == $current_page): ?>
                <span class="px-4 py-3 bg-gradient-to-r from-serein-600 to-teal-600 text-white rounded-xl font-bold shadow-lg min-w-[3rem] text-center">
                  <?= $i ?>
                </span>
              <?php else: ?>
                <a href="/?page=<?= $i ?>" 
                   class="px-4 py-3 bg-white border-2 border-gray-200 text-gray-700 rounded-xl hover:border-serein-400 hover:bg-serein-50 hover:text-serein-700 transition-all duration-300 font-medium min-w-[3rem] text-center">
                  <?= $i ?>
                </a>
              <?php endif; ?>
            <?php endfor; ?>
          </div>
          
          <?php if ($current_page < $total_pages): ?>
            <a href="/?page=<?= $current_page + 1 ?>" 
               class="group flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-xl hover:from-serein-500 hover:to-teal-500 hover:text-white transition-all duration-300 font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1">
              <span class="hidden sm:inline">Sau</span>
              <i class="fas fa-chevron-right group-hover:translate-x-1 transition-transform"></i>
            </a>
          <?php endif; ?>
        </div>
      </nav>
      
      <!-- Quick Navigation -->
      <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-gray-100">
        <div class="flex items-center gap-4">
          <a href="/?page=1" class="text-serein-600 hover:text-serein-700 font-medium transition-colors">
            <i class="fas fa-fast-backward mr-1"></i>
            Trang đầu
          </a>
          <a href="/?page=<?= $total_pages ?>" class="text-serein-600 hover:text-serein-700 font-medium transition-colors">
            Trang cuối
            <i class="fas fa-fast-forward ml-1"></i>
          </a>
        </div>
        
        <div class="flex items-center gap-2">
          <span class="text-gray-600 text-sm">Đi đến trang:</span>
          <form method="GET" class="flex items-center gap-2">
            <input type="number" 
                   name="page" 
                   min="1" 
                   max="<?= $total_pages ?>" 
                   value="<?= $current_page ?>"
                   class="w-16 px-3 py-2 border border-gray-300 rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-serein-500 focus:border-serein-500 transition-all">
            <button type="submit" 
                    class="px-4 py-2 bg-gradient-to-r from-serein-600 to-teal-600 text-white rounded-lg hover:from-serein-700 hover:to-teal-700 transition-all duration-300 font-medium">
              <i class="fas fa-arrow-right"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>