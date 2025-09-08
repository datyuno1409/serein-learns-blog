<!-- Hero Section với Background Pattern -->
<div class="relative bg-gradient-to-br from-serein-50 via-blue-50 to-purple-50 py-20 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-20 h-20 bg-serein-400 rounded-full blur-xl"></div>
        <div class="absolute top-32 right-20 w-32 h-32 bg-blue-400 rounded-full blur-xl"></div>
        <div class="absolute bottom-20 left-1/4 w-24 h-24 bg-purple-400 rounded-full blur-xl"></div>
        <div class="absolute bottom-32 right-1/3 w-16 h-16 bg-serein-300 rounded-full blur-xl"></div>
    </div>
    
    <!-- Grid Pattern -->
    <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <!-- Welcome Badge -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 bg-white/80 backdrop-blur-sm px-4 py-2 rounded-full border border-serein-200 shadow-sm">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm font-medium text-gray-700">Khám phá kiến thức mới mỗi ngày</span>
            </div>
        </div>
        
        <div class="text-center mb-12">
            <h1 class="text-5xl md:text-6xl font-bold mb-6">
                <span class="bg-gradient-to-r from-serein-600 via-blue-600 to-purple-600 bg-clip-text text-transparent">
                    <?= __('articles.page_title') ?>
                </span>
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                <?= __('articles.page_subtitle') ?>
            </p>
        </div>

        <!-- Enhanced Search Bar -->
        <div class="search-container max-w-4xl mx-auto">
            <form method="GET" action="/articles" class="search-form relative mb-8">
                <div class="relative group">
                    <!-- Glow Effect -->
                    <div class="absolute -inset-1 bg-gradient-to-r from-serein-400 to-blue-400 rounded-3xl blur opacity-25 group-hover:opacity-40 transition duration-300"></div>
                    
                    <div class="relative bg-white/90 backdrop-blur-sm rounded-2xl border border-white/50 shadow-xl">
                        <input 
                            type="text" 
                            name="search" 
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                            placeholder="<?= __('articles.search_placeholder') ?>"
                            class="search-input w-full px-6 py-5 pl-14 pr-32 text-lg bg-transparent border-0 rounded-2xl focus:outline-none focus:ring-0 placeholder-gray-500"
                        >
                        <i class="fas fa-search absolute left-5 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                        <button type="submit" class="search-btn absolute right-3 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-serein-500 to-serein-600 text-white px-8 py-3 rounded-xl hover:from-serein-600 hover:to-serein-700 transition-all duration-300 shadow-lg hover:shadow-xl font-medium">
                            <i class="fas fa-search mr-2"></i><?= __('articles.search_button') ?>
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Quick Search Tags -->
            <div class="text-center">
                <p class="text-sm text-gray-500 mb-3">Tìm kiếm phổ biến:</p>
                <div class="flex flex-wrap justify-center gap-2">
                    <a href="/articles?search=PHP" class="bg-white/70 backdrop-blur-sm text-gray-700 px-4 py-2 rounded-full text-sm hover:bg-serein-100 hover:text-serein-700 transition-all duration-300 border border-gray-200">
                        #PHP
                    </a>
                    <a href="/articles?search=JavaScript" class="bg-white/70 backdrop-blur-sm text-gray-700 px-4 py-2 rounded-full text-sm hover:bg-serein-100 hover:text-serein-700 transition-all duration-300 border border-gray-200">
                        #JavaScript
                    </a>
                    <a href="/articles?search=Tutorial" class="bg-white/70 backdrop-blur-sm text-gray-700 px-4 py-2 rounded-full text-sm hover:bg-serein-100 hover:text-serein-700 transition-all duration-300 border border-gray-200">
                        #Tutorial
                    </a>
                    <a href="/articles?search=Web Development" class="bg-white/70 backdrop-blur-sm text-gray-700 px-4 py-2 rounded-full text-sm hover:bg-serein-100 hover:text-serein-700 transition-all duration-300 border border-gray-200">
                        #Web Development
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content với Background -->
<div class="relative bg-gradient-to-b from-gray-50 to-white py-16">
    <!-- Subtle Pattern -->
    <div class="absolute inset-0 opacity-30">
        <div class="absolute top-20 right-10 w-64 h-64 bg-gradient-to-br from-serein-100 to-blue-100 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-10 w-48 h-48 bg-gradient-to-br from-purple-100 to-serein-100 rounded-full blur-3xl"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
    <div class="main-grid grid grid-cols-1 lg:grid-cols-4 gap-8">
        <div class="lg:col-span-3">
            <?php if (!empty($featured_articles) && empty($_GET['search']) && empty($_GET['category']) && empty($_GET['tag'])): ?>
            <section class="mb-16">
                <div class="text-center mb-10">
                    <div class="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-100 to-orange-100 px-4 py-2 rounded-full border border-yellow-200 mb-4">
                        <i class="fas fa-star text-yellow-600"></i>
                        <span class="text-sm font-medium text-yellow-800">Nổi bật</span>
                    </div>
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent mb-2">
                        <?= __('articles.featured_articles') ?>
                    </h2>
                    <p class="text-gray-600">Những bài viết được đề xuất dành cho bạn</p>
                </div>
                <div class="featured-articles-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($featured_articles as $featured): ?>
                    <article class="relative bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden group border border-gray-100">
                        <!-- Glow Effect -->
                        <div class="absolute -inset-1 bg-gradient-to-r from-serein-400 to-blue-400 rounded-3xl blur opacity-0 group-hover:opacity-20 transition duration-500"></div>
                        <div class="relative">
                            <?php if (isset($featured['featured_image']) && !empty($featured['featured_image'])): ?>
                            <div class="relative aspect-video overflow-hidden">
                                <img 
                                    src="<?= htmlspecialchars($featured['featured_image']) ?>" 
                                    alt="<?= htmlspecialchars($featured['title']) ?>"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                >
                                <!-- Overlay Gradient -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <!-- Featured Badge -->
                                <div class="absolute top-4 left-4">
                                    <div class="bg-gradient-to-r from-yellow-400 to-orange-400 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                        <i class="fas fa-star mr-1"></i>FEATURED
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="p-8">
                                <div class="flex items-center gap-2 mb-4">
                                    <span class="bg-gradient-to-r from-serein-100 to-serein-200 text-serein-800 text-xs font-semibold px-3 py-1 rounded-full border border-serein-300">
                                        <?= htmlspecialchars($featured['category_name']) ?>
                                    </span>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>
                                        <span>5 phút đọc</span>
                                    </div>
                                </div>
                                <h3 class="font-bold text-xl text-gray-900 mb-3 line-clamp-2 group-hover:text-serein-600 transition-colors duration-300 leading-tight">
                                    <a href="/article/<?= $featured['id'] ?>">
                                        <?= htmlspecialchars($featured['title']) ?>
                                    </a>
                                </h3>
                                <div class="flex items-center text-sm text-gray-500 mb-6">
                                    <div class="flex items-center mr-4">
                                        <i class="fas fa-user mr-1"></i>
                                        <span><?= htmlspecialchars($featured['author_name']) ?></span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar mr-1"></i>
                                        <span><?= date('d/m/Y', strtotime($featured['created_at'])) ?></span>
                                    </div>
                                </div>
                                <a href="/article/<?= $featured['id'] ?>" class="inline-flex items-center bg-gradient-to-r from-serein-500 to-serein-600 text-white px-6 py-3 rounded-xl hover:from-serein-600 hover:to-serein-700 transition-all duration-300 font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <?= __('articles.read_more') ?> <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <section>
                <?php if (!empty($_GET['search']) || !empty($_GET['category']) || !empty($_GET['tag'])): ?>
                <div class="mb-10">
                    <div class="bg-gradient-to-r from-serein-50 to-blue-50 rounded-2xl p-6 border border-serein-100">
                        <h2 class="text-3xl font-bold mb-3">
                            <span class="bg-gradient-to-r from-serein-600 to-blue-600 bg-clip-text text-transparent">
                                <?php if (!empty($_GET['search'])): ?>
                                    <?= __('articles.search_results') ?> "<?= htmlspecialchars($_GET['search']) ?>"
                                <?php elseif (!empty($_GET['category'])): ?>
                                    <?= __('articles.category_articles') ?>
                                <?php elseif (!empty($_GET['tag'])): ?>
                                    <?= __('articles.tag_articles') ?>
                                <?php endif; ?>
                            </span>
                        </h2>
                        <div class="flex items-center gap-4">
                            <p class="text-gray-600 flex items-center">
                                <i class="fas fa-file-alt mr-2 text-serein-500"></i>
                                <?= $total_count ?> <?= __('articles.articles_count') ?>
                            </p>
                            <a href="/articles" class="text-serein-600 hover:text-serein-700 text-sm font-medium flex items-center">
                                <i class="fas fa-times mr-1"></i>Xóa bộ lọc
                            </a>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="mb-10">
                    <div class="text-center mb-6">
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent mb-2">
                            <?= __('articles.all_articles') ?>
                        </h2>
                        <p class="text-gray-600 flex items-center justify-center">
                            <i class="fas fa-file-alt mr-2 text-serein-500"></i>
                            <?= $total_count ?> <?= __('articles.articles_count') ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (empty($articles)): ?>
                <div class="text-center py-16">
                    <div class="relative mb-8">
                        <div class="w-32 h-32 bg-gradient-to-br from-serein-100 to-blue-100 rounded-full mx-auto flex items-center justify-center">
                            <i class="fas fa-search text-4xl text-serein-400"></i>
                        </div>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3"><?= __('articles.no_articles_found') ?></h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto"><?= __('articles.try_different_search') ?></p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="/articles" class="bg-gradient-to-r from-serein-500 to-serein-600 text-white px-8 py-3 rounded-xl hover:from-serein-600 hover:to-serein-700 transition-all duration-300 font-medium shadow-lg hover:shadow-xl">
                            <i class="fas fa-list mr-2"></i><?= __('articles.view_all_articles') ?>
                        </a>
                        <a href="/" class="bg-white text-serein-600 px-8 py-3 rounded-xl border-2 border-serein-200 hover:bg-serein-50 transition-all duration-300 font-medium">
                            <i class="fas fa-home mr-2"></i>Về trang chủ
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <div class="articles-list-grid grid grid-cols-1 md:grid-cols-2 gap-8">
                    <?php foreach ($articles as $article): ?>
                    <article class="relative bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden group border border-gray-100">
                        <!-- Glow Effect -->
                        <div class="absolute -inset-1 bg-gradient-to-r from-serein-400 to-blue-400 rounded-3xl blur opacity-0 group-hover:opacity-15 transition duration-500"></div>
                        <div class="relative">
                            <?php if (isset($article['featured_image']) && !empty($article['featured_image'])): ?>
                            <div class="relative aspect-video overflow-hidden">
                                <img 
                                    src="<?= htmlspecialchars($article['featured_image']) ?>" 
                                    alt="<?= htmlspecialchars($article['title']) ?>"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                >
                                <!-- Overlay Gradient -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <?php if (isset($article['featured']) && $article['featured']): ?>
                                <!-- Featured Badge -->
                                <div class="absolute top-4 left-4">
                                    <div class="bg-gradient-to-r from-yellow-400 to-orange-400 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                        <i class="fas fa-star mr-1"></i>FEATURED
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <div class="p-8">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <span class="bg-gradient-to-r from-serein-100 to-serein-200 text-serein-800 text-xs font-semibold px-3 py-1 rounded-full border border-serein-300">
                                            <?= htmlspecialchars($article['category_name']) ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>
                                        <span>5 phút đọc</span>
                                    </div>
                                </div>
                                <h3 class="font-bold text-xl text-gray-900 mb-3 line-clamp-2 group-hover:text-serein-600 transition-colors duration-300 leading-tight">
                                    <a href="/article/<?= $article['id'] ?>">
                                        <?= htmlspecialchars($article['title']) ?>
                                    </a>
                                </h3>
                                <p class="text-gray-600 mb-4 line-clamp-3">
                                    <?= substr(strip_tags($article['content']), 0, 150) ?>...
                                </p>
                                <div class="flex items-center justify-between text-sm text-gray-500 mb-6">
                                    <div class="flex items-center gap-4">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-user"></i>
                                            <?= htmlspecialchars($article['author_name']) ?>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-calendar"></i>
                                            <?= date('d/m/Y', strtotime($article['created_at'])) ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="flex items-center bg-gray-100 px-2 py-1 rounded-full">
                                            <i class="fas fa-eye mr-1 text-gray-400"></i>
                                            <?= $article['views'] ?>
                                        </span>
                                        <span class="flex items-center bg-gray-100 px-2 py-1 rounded-full">
                                            <i class="fas fa-comments mr-1 text-gray-400"></i>
                                            <?= $article['comment_count'] ?>
                                        </span>
                                    </div>
                                </div>
                                <?php if (isset($article['tags']) && !empty($article['tags']) && is_array($article['tags'])): ?>
                                <div class="flex flex-wrap gap-2 mb-6">
                                    <?php foreach (array_slice($article['tags'], 0, 3) as $tag): ?>
                                    <a href="/articles?tag=<?= $tag['id'] ?>" class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 text-xs px-3 py-1 rounded-full hover:from-serein-100 hover:to-serein-200 hover:text-serein-700 transition-all duration-300 border border-gray-300">
                                        #<?= htmlspecialchars($tag['name']) ?>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                <a href="/article/<?= $article['id'] ?>" class="inline-flex items-center bg-gradient-to-r from-serein-500 to-serein-600 text-white px-6 py-3 rounded-xl hover:from-serein-600 hover:to-serein-700 transition-all duration-300 font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <?= __('articles.read_more') ?> <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ($total_pages > 1): ?>
                <div class="mt-16">
                    <div class="bg-gradient-to-r from-gray-50 to-serein-50 rounded-3xl p-8 border border-gray-200">
                        <div class="text-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Khám Phá Thêm</h3>
                            <p class="text-gray-600 text-sm">Trang <?= $current_page ?> / <?= $total_pages ?> - Tổng cộng <?= $total_count ?> bài viết</p>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                            <!-- Quick Navigation -->
                            <div class="flex items-center gap-3">
                                <?php if ($current_page > 1): ?>
                                <a href="?page=1<?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['category']) ? '&category=' . $_GET['category'] : '' ?><?= !empty($_GET['tag']) ? '&tag=' . $_GET['tag'] : '' ?>" class="bg-white text-gray-600 px-4 py-2 rounded-xl border border-gray-300 hover:bg-serein-50 hover:text-serein-600 transition-all duration-300 text-sm font-medium">
                                    <i class="fas fa-angle-double-left mr-1"></i>Trang đầu
                                </a>
                                <?php endif; ?>
                                <?php if ($current_page < $total_pages): ?>
                                <a href="?page=<?= $total_pages ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['category']) ? '&category=' . $_GET['category'] : '' ?><?= !empty($_GET['tag']) ? '&tag=' . $_GET['tag'] : '' ?>" class="bg-white text-gray-600 px-4 py-2 rounded-xl border border-gray-300 hover:bg-serein-50 hover:text-serein-600 transition-all duration-300 text-sm font-medium">
                                    Trang cuối<i class="fas fa-angle-double-right ml-1"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Main Pagination -->
                            <nav class="flex items-center space-x-2">
                                <?php if ($current_page > 1): ?>
                                <a href="?page=<?= $current_page - 1 ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['category']) ? '&category=' . $_GET['category'] : '' ?><?= !empty($_GET['tag']) ? '&tag=' . $_GET['tag'] : '' ?>" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-white hover:bg-serein-500 rounded-xl transition-all duration-300 border border-gray-300 hover:border-serein-500">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                                <a href="?page=<?= $i ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['category']) ? '&category=' . $_GET['category'] : '' ?><?= !empty($_GET['tag']) ? '&tag=' . $_GET['tag'] : '' ?>" class="w-10 h-10 flex items-center justify-center <?= $i == $current_page ? 'bg-gradient-to-r from-serein-500 to-serein-600 text-white shadow-lg' : 'text-gray-700 hover:text-white hover:bg-serein-500 border border-gray-300 hover:border-serein-500' ?> rounded-xl transition-all duration-300 font-medium">
                                    <?= $i ?>
                                </a>
                                <?php endfor; ?>
                                
                                <?php if ($current_page < $total_pages): ?>
                                <a href="?page=<?= $current_page + 1 ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['category']) ? '&category=' . $_GET['category'] : '' ?><?= !empty($_GET['tag']) ? '&tag=' . $_GET['tag'] : '' ?>" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-white hover:bg-serein-500 rounded-xl transition-all duration-300 border border-gray-300 hover:border-serein-500">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                                <?php endif; ?>
                            </nav>
                            
                            <!-- Go to Page -->
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600">Đi đến:</span>
                                <form method="GET" class="flex items-center gap-2">
                                    <?php foreach ($_GET as $key => $value): ?>
                                        <?php if ($key !== 'page'): ?>
                                        <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <input type="number" name="page" min="1" max="<?= $total_pages ?>" value="<?= $current_page ?>" class="w-16 px-2 py-1 text-center border border-gray-300 rounded-lg focus:border-serein-500 focus:ring-1 focus:ring-serein-500 text-sm">
                                    <button type="submit" class="bg-serein-500 text-white px-3 py-1 rounded-lg hover:bg-serein-600 transition-colors text-sm">
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </section>
        </div>

        <aside class="lg:col-span-1">
            <div class="sticky top-8 space-y-6">
                <div class="bg-white rounded-3xl shadow-lg p-8 border border-gray-100 relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-serein-100 to-blue-100 rounded-full -translate-y-16 translate-x-16 opacity-50"></div>
                    <div class="relative">
                        <h3 class="font-bold text-xl text-gray-900 mb-6 flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-serein-500 to-serein-600 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-folder text-white"></i>
                            </div>
                            <?= __('articles.categories') ?>
                        </h3>
                        <div class="space-y-3">
                            <?php foreach ($categories as $cat): ?>
                            <a href="/articles?category=<?= $cat['id'] ?>" 
                               class="group flex items-center justify-between text-gray-600 hover:text-serein-600 hover:bg-gradient-to-r hover:from-serein-50 hover:to-blue-50 px-4 py-3 rounded-xl transition-all duration-300 border border-transparent hover:border-serein-200">
                                <div class="flex items-center">
                                    <span class="font-medium"><?= htmlspecialchars($cat['name']) ?></span>
                                    <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full ml-2"><?= $cat['article_count'] ?></span>
                                </div>
                                <i class="fas fa-chevron-right text-xs opacity-0 group-hover:opacity-100 transition-opacity"></i>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-lg p-8 border border-gray-100 relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-blue-100 to-serein-100 rounded-full -translate-y-12 -translate-x-12 opacity-50"></div>
                    <div class="relative">
                        <h3 class="font-bold text-xl text-gray-900 mb-6 flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-serein-500 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-tags text-white"></i>
                            </div>
                            <?= __('articles.popular_tags') ?>
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <?php foreach ($popular_tags as $tag): ?>
                            <a href="/articles?tag=<?= $tag['id'] ?>" 
                               class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 text-sm px-4 py-2 rounded-full hover:from-serein-100 hover:to-serein-200 hover:text-serein-700 transition-all duration-300 border border-gray-300 hover:border-serein-300 hover:shadow-md transform hover:-translate-y-0.5">
                                #<?= htmlspecialchars($tag['name']) ?>
                                <span class="ml-1 text-xs">(<?= $tag['article_count'] ?>)</span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-serein-500 via-serein-600 to-blue-600 rounded-3xl shadow-xl p-8 text-white relative overflow-hidden">
                    <!-- Background Effects -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-16 translate-x-16"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-12 -translate-x-12"></div>
                    
                    <div class="relative">
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-envelope text-2xl text-white"></i>
                            </div>
                            <h3 class="font-bold text-xl mb-2"><?= __('articles.newsletter_signup') ?></h3>
                            <p class="text-white/90 text-sm leading-relaxed"><?= __('articles.newsletter_description') ?></p>
                        </div>
                        
                        <!-- Benefits -->
                        <div class="mb-6 space-y-2">
                            <div class="flex items-center text-sm text-white/90">
                                <i class="fas fa-check-circle mr-2 text-white"></i>
                                Nhận bài viết mới nhất
                            </div>
                            <div class="flex items-center text-sm text-white/90">
                                <i class="fas fa-check-circle mr-2 text-white"></i>
                                Tips & tricks độc quyền
                            </div>
                            <div class="flex items-center text-sm text-white/90">
                                <i class="fas fa-check-circle mr-2 text-white"></i>
                                Không spam, hủy bất cứ lúc nào
                            </div>
                        </div>
                        
                        <form class="space-y-4">
                            <div class="relative">
                                <input 
                                    type="email" 
                                    placeholder="<?= __('articles.email_placeholder') ?>"
                                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/50 focus:bg-white/20 transition-all duration-300"
                                >
                                <i class="fas fa-envelope absolute right-3 top-1/2 transform -translate-y-1/2 text-white/50"></i>
                            </div>
                            <button type="submit" class="w-full bg-white text-serein-600 py-3 rounded-xl font-semibold hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <i class="fas fa-paper-plane mr-2"></i><?= __('articles.subscribe_button') ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>