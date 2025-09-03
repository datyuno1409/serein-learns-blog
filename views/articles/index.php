<div class="bg-gradient-to-br from-serein-50 to-blue-50 py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                <?= __('articles.page_title') ?>
            </h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                <?= __('articles.page_subtitle') ?>
            </p>
        </div>

        <div class="max-w-4xl mx-auto">
            <form method="GET" action="/articles" class="relative mb-8">
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                        placeholder="<?= __('articles.search_placeholder') ?>"
                        class="w-full px-6 py-4 pl-12 text-lg border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-serein-500 focus:border-transparent shadow-sm"
                    >
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-serein-500 text-white px-6 py-2 rounded-xl hover:bg-serein-600 transition-colors">
                    <?= __('articles.search_button') ?>
                </button>
            </form>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <div class="lg:col-span-3">
            <?php if (!empty($featured_articles) && empty($_GET['search']) && empty($_GET['category']) && empty($_GET['tag'])): ?>
            <section class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fas fa-star text-yellow-500"></i>
                    <?= __('articles.featured_articles') ?>
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($featured_articles as $featured): ?>
                    <article class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group">
                        <?php if ($featured['featured_image']): ?>
                        <div class="aspect-video overflow-hidden">
                            <img 
                                src="<?= htmlspecialchars($featured['featured_image']) ?>" 
                                alt="<?= htmlspecialchars($featured['title']) ?>"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            >
                        </div>
                        <?php endif; ?>
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full">
                                    <i class="fas fa-star mr-1"></i><?= __('articles.featured') ?>
                                </span>
                                <span class="bg-serein-100 text-serein-800 text-xs font-medium px-2 py-1 rounded-full">
                                    <?= htmlspecialchars($featured['category_name']) ?>
                                </span>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-2 group-hover:text-serein-600 transition-colors">
                                <a href="/article/<?= $featured['id'] ?>">
                                    <?= htmlspecialchars($featured['title']) ?>
                                </a>
                            </h3>
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <i class="fas fa-user mr-1"></i>
                                <span class="mr-3"><?= htmlspecialchars($featured['author_name']) ?></span>
                                <i class="fas fa-calendar mr-1"></i>
                                <span><?= date('d/m/Y', strtotime($featured['created_at'])) ?></span>
                            </div>
                            <a href="/article/<?= $featured['id'] ?>" class="inline-flex items-center text-serein-600 hover:text-serein-700 font-medium">
                                <?= __('articles.read_more') ?> <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <section>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <?php if (!empty($_GET['search'])): ?>
                            <?= __('articles.search_results') ?>
                        <?php elseif (!empty($_GET['category'])): ?>
                            <?= __('articles.category_articles') ?>
                        <?php elseif (!empty($_GET['tag'])): ?>
                            <?= __('articles.tag_articles') ?>
                        <?php else: ?>
                            <?= __('articles.all_articles') ?>
                        <?php endif; ?>
                    </h2>
                    <span class="text-gray-500"><?= $total_count ?> <?= __('articles.articles_count') ?></span>
                </div>

                <?php if (empty($articles)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2"><?= __('articles.no_articles_found') ?></h3>
                    <p class="text-gray-500"><?= __('articles.try_different_search') ?></p>
                    <a href="/articles" class="inline-block mt-4 bg-serein-500 text-white px-6 py-2 rounded-lg hover:bg-serein-600 transition-colors">
                        <?= __('articles.view_all_articles') ?>
                    </a>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <?php foreach ($articles as $article): ?>
                    <article class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group">
                        <?php if ($article['featured_image']): ?>
                        <div class="aspect-video overflow-hidden">
                            <img 
                                src="<?= htmlspecialchars($article['featured_image']) ?>" 
                                alt="<?= htmlspecialchars($article['title']) ?>"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            >
                        </div>
                        <?php endif; ?>
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="bg-serein-100 text-serein-800 text-xs font-medium px-2 py-1 rounded-full">
                                    <?= htmlspecialchars($article['category_name']) ?>
                                </span>
                                <?php if ($article['featured']): ?>
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full">
                                    <i class="fas fa-star mr-1"></i><?= __('articles.featured') ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <h3 class="font-bold text-xl text-gray-900 mb-3 line-clamp-2 group-hover:text-serein-600 transition-colors">
                                <a href="/article/<?= $article['id'] ?>">
                                    <?= htmlspecialchars($article['title']) ?>
                                </a>
                            </h3>
                            <p class="text-gray-600 mb-4 line-clamp-3">
                                <?= substr(strip_tags($article['content']), 0, 150) ?>...
                            </p>
                            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
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
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-eye"></i>
                                        <?= $article['views'] ?>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-comments"></i>
                                        <?= $article['comment_count'] ?>
                                    </span>
                                </div>
                            </div>
                            <?php if (!empty($article['tags'])): ?>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <?php foreach (array_slice($article['tags'], 0, 3) as $tag): ?>
                                <a href="/articles?tag=<?= $tag['id'] ?>" class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full hover:bg-serein-100 hover:text-serein-700 transition-colors">
                                    #<?= htmlspecialchars($tag['name']) ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            <a href="/article/<?= $article['id'] ?>" class="inline-flex items-center text-serein-600 hover:text-serein-700 font-medium">
                                <?= __('articles.read_more') ?> <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ($total_pages > 1): ?>
                <nav class="flex justify-center mt-12">
                    <div class="flex items-center gap-2">
                        <?php if ($current_page > 1): ?>
                        <a href="?page=<?= $current_page - 1 ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['category']) ? '&category=' . $_GET['category'] : '' ?><?= !empty($_GET['tag']) ? '&tag=' . $_GET['tag'] : '' ?>" 
                           class="px-4 py-2 text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <?php endif; ?>

                        <?php 
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);
                        for ($i = $start_page; $i <= $end_page; $i++): 
                        ?>
                        <a href="?page=<?= $i ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['category']) ? '&category=' . $_GET['category'] : '' ?><?= !empty($_GET['tag']) ? '&tag=' . $_GET['tag'] : '' ?>" 
                           class="px-4 py-2 <?= $i === $current_page ? 'bg-serein-500 text-white' : 'text-gray-600 bg-white hover:bg-gray-50' ?> border border-gray-200 rounded-lg transition-colors">
                            <?= $i ?>
                        </a>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages): ?>
                        <a href="?page=<?= $current_page + 1 ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['category']) ? '&category=' . $_GET['category'] : '' ?><?= !empty($_GET['tag']) ? '&tag=' . $_GET['tag'] : '' ?>" 
                           class="px-4 py-2 text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </nav>
                <?php endif; ?>
            </section>
        </div>

        <aside class="lg:col-span-1">
            <div class="sticky top-8 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-lg text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-folder text-serein-500"></i>
                        <?= __('articles.categories') ?>
                    </h3>
                    <div class="space-y-2">
                        <?php foreach ($categories as $cat): ?>
                        <a href="/articles?category=<?= $cat['id'] ?>" 
                           class="flex items-center justify-between p-3 rounded-lg hover:bg-serein-50 transition-colors group">
                            <span class="text-gray-700 group-hover:text-serein-600"><?= htmlspecialchars($cat['name']) ?></span>
                            <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full group-hover:bg-serein-100 group-hover:text-serein-600">
                                <?= $cat['article_count'] ?>
                            </span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-lg text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-tags text-serein-500"></i>
                        <?= __('articles.popular_tags') ?>
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($popular_tags as $tag): ?>
                        <a href="/articles?tag=<?= $tag['id'] ?>" 
                           class="bg-gray-100 text-gray-700 text-sm px-3 py-1 rounded-full hover:bg-serein-100 hover:text-serein-700 transition-colors">
                            #<?= htmlspecialchars($tag['name']) ?>
                            <span class="ml-1 text-xs">(<?= $tag['article_count'] ?>)</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-serein-500 to-serein-600 rounded-2xl shadow-sm p-6 text-white">
                    <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
                        <i class="fas fa-envelope"></i>
                        <?= __('articles.newsletter_signup') ?>
                    </h3>
                    <p class="text-serein-100 mb-4 text-sm">
                        <?= __('articles.newsletter_description') ?>
                    </p>
                    <form class="space-y-3">
                        <input 
                            type="email" 
                            placeholder="<?= __('articles.email_placeholder') ?>"
                            class="w-full px-4 py-2 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-white"
                        >
                        <button type="submit" class="w-full bg-white text-serein-600 py-2 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                            <?= __('articles.subscribe_button') ?>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
    </div>
</div>