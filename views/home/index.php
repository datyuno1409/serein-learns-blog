<!-- Hero Section -->
<section class="bg-gradient-to-br from-serein-500 to-serein-600 text-white py-20">
  <div class="container mx-auto text-center">
    <h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fadeIn">
      <?= __('home.hero_title') ?>
    </h1>
    <p class="text-xl md:text-2xl mb-8 text-serein-100 max-w-3xl mx-auto">
      <?= __('home.hero_subtitle') ?>
    </p>
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
      <a href="/articles" class="bg-white text-serein-600 px-8 py-3 rounded-lg font-semibold hover:bg-serein-50 transition-colors">
        <?= __('home.hero_cta_articles') ?>
      </a>
      <a href="/about" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-serein-600 transition-colors">
        <?= __('home.hero_cta_about') ?>
      </a>
    </div>
  </div>
</section>

<!-- Search Bar Section -->
<section class="bg-white py-8 shadow-sm">
  <div class="container mx-auto">
    <div class="max-w-2xl mx-auto">
      <form action="/search" method="GET" class="flex gap-2">
        <input 
          type="text" 
          name="q" 
          placeholder="<?= __('home.search_placeholder') ?>" 
          class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-serein-500 focus:border-transparent"
          value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
        >
        <button 
          type="submit" 
          class="bg-serein-500 text-white px-6 py-3 rounded-lg hover:bg-serein-600 transition-colors flex items-center gap-2"
        >
          <i class="fas fa-search"></i>
          <?= __('articles.search_button') ?>
        </button>
      </form>
    </div>
  </div>
</section>

<!-- Popular Topics -->
<section class="bg-gray-50 py-8">
  <div class="container mx-auto">
    <h2 class="text-2xl font-bold text-center mb-6"><?= __('home.popular_topics') ?></h2>
    <div class="flex flex-wrap justify-center gap-3">
      <?php foreach (array_slice($categories, 0, 8) as $category): ?>
        <a href="/category/<?= $category['id'] ?>" 
           class="bg-white px-4 py-2 rounded-full text-gray-700 hover:bg-serein-500 hover:text-white transition-colors shadow-sm">
          <?= htmlspecialchars($category['name']) ?>
          <span class="ml-1 text-xs opacity-75">(<?= $category['article_count'] ?>)</span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Featured Article -->
<?php if (!empty($articles) && isset($articles[0])): ?>
<section class="py-16">
  <div class="container mx-auto">
    <h2 class="text-3xl font-bold text-center mb-10"><?= __('home.featured_articles') ?></h2>
    <?php $featured = $articles[0]; ?>
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
      <?php if ($featured['featured_image']): ?>
        <div class="h-64 md:h-80 bg-cover bg-center" style="background-image: url('<?= htmlspecialchars($featured['featured_image']) ?>')"></div>
      <?php endif; ?>
      <div class="p-8">
        <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
          <span class="flex items-center gap-1">
            <i class="fas fa-user"></i>
            <?= htmlspecialchars($featured['author_name']) ?>
          </span>
          <span class="flex items-center gap-1">
            <i class="fas fa-calendar"></i>
            <?= date('M j, Y', strtotime($featured['created_at'])) ?>
          </span>
          <span class="flex items-center gap-1">
            <i class="fas fa-eye"></i>
            <?= $featured['views'] ?> views
          </span>
        </div>
        <h3 class="text-2xl md:text-3xl font-bold mb-4">
          <a href="/article/<?= $featured['id'] ?>" class="text-gray-900 hover:text-serein-600 transition-colors">
            <?= htmlspecialchars($featured['title']) ?>
          </a>
        </h3>
        <p class="text-gray-600 mb-6 leading-relaxed">
          <?= substr(strip_tags($featured['content']), 0, 300) ?>...
        </p>
        <div class="flex flex-wrap gap-2 mb-6">
          <?php foreach ($featured['tags'] as $tag): ?>
            <a href="/tag/<?= $tag['id'] ?>" class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm hover:bg-serein-100 hover:text-serein-700 transition-colors">
              <?= htmlspecialchars($tag['name']) ?>
            </a>
          <?php endforeach; ?>
        </div>
        <a href="/article/<?= $featured['id'] ?>" class="inline-flex items-center gap-2 bg-serein-500 text-white px-6 py-3 rounded-lg hover:bg-serein-600 transition-colors">
          <?= __('home.read_more') ?>
          <i class="fas fa-arrow-right"></i>
        </a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Latest Articles -->
<section class="py-16 bg-gray-50">
  <div class="container mx-auto">
    <h2 class="text-3xl font-bold text-center mb-10"><?= __('home.latest_articles') ?></h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php foreach (array_slice($articles, 1, 6) as $article): ?>
        <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
          <?php if ($article['featured_image']): ?>
            <div class="h-48 bg-cover bg-center" style="background-image: url('<?= htmlspecialchars($article['featured_image']) ?>')"></div>
          <?php endif; ?>
          <div class="p-6">
            <div class="flex items-center gap-2 text-sm text-gray-600 mb-3">
              <span class="bg-serein-100 text-serein-700 px-2 py-1 rounded text-xs">
                <?= htmlspecialchars($article['category_name']) ?>
              </span>
              <span><?= date('M j', strtotime($article['created_at'])) ?></span>
            </div>
            <h3 class="text-xl font-bold mb-3">
              <a href="/article/<?= $article['id'] ?>" class="text-gray-900 hover:text-serein-600 transition-colors">
                <?= htmlspecialchars($article['title']) ?>
              </a>
            </h3>
            <p class="text-gray-600 mb-4 leading-relaxed">
              <?= substr(strip_tags($article['content']), 0, 120) ?>...
            </p>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4 text-sm text-gray-500">
                <span class="flex items-center gap-1">
                  <i class="fas fa-eye"></i>
                  <?= $article['views'] ?>
                </span>
                <span class="flex items-center gap-1">
                  <i class="fas fa-comments"></i>
                  <?= $article['comment_count'] ?>
                </span>
              </div>
              <a href="/article/<?= $article['id'] ?>" class="text-serein-600 hover:text-serein-700 font-medium">
                <?= __('home.read_more') ?> →
              </a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-10">
      <a href="/articles" class="inline-flex items-center gap-2 bg-serein-500 text-white px-8 py-3 rounded-lg hover:bg-serein-600 transition-colors">
        <?= __('home.view_all') ?>
        <i class="fas fa-arrow-right"></i>
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
<?php if (!empty($cybersecurity_articles)): ?>
<section class="py-16">
  <div class="container mx-auto">
    <h2 class="text-3xl font-bold text-center mb-10"><?= __('home.cybersecurity_section') ?></h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php foreach (array_slice($cybersecurity_articles, 0, 3) as $article): ?>
        <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
          <?php if ($article['featured_image']): ?>
            <div class="h-48 bg-cover bg-center" style="background-image: url('<?= htmlspecialchars($article['featured_image']) ?>')"></div>
          <?php endif; ?>
          <div class="p-6">
            <div class="flex items-center gap-2 text-sm text-gray-600 mb-3">
              <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs">
                <i class="fas fa-shield-alt mr-1"></i>
                <?= __('home.security_label') ?>
              </span>
              <span><?= date('M j', strtotime($article['created_at'])) ?></span>
            </div>
            <h3 class="text-xl font-bold mb-3">
              <a href="/article/<?= $article['id'] ?>" class="text-gray-900 hover:text-serein-600 transition-colors">
                <?= htmlspecialchars($article['title']) ?>
              </a>
            </h3>
            <p class="text-gray-600 mb-4 leading-relaxed">
              <?= substr(strip_tags($article['content']), 0, 120) ?>...
            </p>
            <a href="/article/<?= $article['id'] ?>" class="text-serein-600 hover:text-serein-700 font-medium">
              <?= __('home.read_more') ?> →
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Newsletter Section -->
<section class="bg-serein-500 text-white py-16">
  <div class="container mx-auto text-center">
    <h2 class="text-3xl font-bold mb-4"><?= __('home.newsletter_title') ?></h2>
    <p class="text-xl text-serein-100 mb-8 max-w-2xl mx-auto">
      <?= __('home.newsletter_subtitle') ?>
    </p>
    <form action="/newsletter/subscribe" method="POST" class="max-w-md mx-auto flex gap-2">
      <input 
        type="email" 
        name="email" 
        placeholder="<?= __('home.newsletter_placeholder') ?>" 
        required
        class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-white"
      >
      <button 
        type="submit" 
        class="bg-white text-serein-600 px-6 py-3 rounded-lg font-semibold hover:bg-serein-50 transition-colors"
      >
        <?= __('home.newsletter_button') ?>
      </button>
    </form>
    <p class="text-sm text-serein-200 mt-4">
      <?= __('home.newsletter_disclaimer') ?>
    </p>
  </div>
</section>

<!-- Pagination -->
<?php if ($total_pages > 1): ?>
<section class="py-8">
  <div class="container mx-auto">
    <nav class="flex justify-center">
      <div class="flex items-center gap-2">
        <?php if ($current_page > 1): ?>
          <a href="/?page=<?= $current_page - 1 ?>" class="px-3 py-2 text-gray-600 hover:text-serein-600 transition-colors">
            <i class="fas fa-chevron-left"></i>
          </a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <a href="/?page=<?= $i ?>" class="px-3 py-2 rounded <?= $i === $current_page ? 'bg-serein-500 text-white' : 'text-gray-600 hover:text-serein-600' ?> transition-colors">
            <?= $i ?>
          </a>
        <?php endfor; ?>
        
        <?php if ($current_page < $total_pages): ?>
          <a href="/?page=<?= $current_page + 1 ?>" class="px-3 py-2 text-gray-600 hover:text-serein-600 transition-colors">
            <i class="fas fa-chevron-right"></i>
          </a>
        <?php endif; ?>
      </div>
    </nav>
  </div>
</section>
<?php endif; ?>