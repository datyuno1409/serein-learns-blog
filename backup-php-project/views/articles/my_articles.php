<!-- Trang hiển thị bài viết của người dùng -->
<div class="container mx-auto px-4 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Bài viết của tôi</h1>
            <a href="/articles/create" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                <i class="fas fa-plus mr-2"></i>Tạo bài viết mới
            </a>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= $_SESSION['success']; ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($articles)): ?>
            <div class="bg-gray-100 dark:bg-gray-700 p-6 rounded-lg text-center">
                <p class="text-gray-600 dark:text-gray-300">Bạn chưa có bài viết nào. Hãy tạo bài viết mới!</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-800">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Tiêu đề</th>
                            <th class="py-3 px-6 text-left">Danh mục</th>
                            <th class="py-3 px-6 text-center">Trạng thái</th>
                            <th class="py-3 px-6 text-center">Ngày tạo</th>
                            <th class="py-3 px-6 text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 dark:text-gray-300">
                        <?php foreach ($articles as $article): ?>
                            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="py-3 px-6 text-left">
                                    <div class="flex items-center">
                                        <?php if (isset($article['featured_image']) && !empty($article['featured_image'])): ?>
                                            <div class="mr-2">
                                                <img src="<?= htmlspecialchars($article['featured_image']) ?>" alt="" class="w-8 h-8 rounded-full object-cover">
                                            </div>
                                        <?php endif; ?>
                                        <span><?= htmlspecialchars($article['title']) ?></span>
                                        <?php if ($article['is_featured']): ?>
                                            <span class="ml-2 px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs rounded-full">Nổi bật</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-left">
                                    <?= htmlspecialchars($article['category_name'] ?? 'Không có danh mục') ?>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <?php if ($article['status'] == 'published'): ?>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Công khai</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Bản nháp</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <?= date('d/m/Y', strtotime($article['created_at'])) ?>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex item-center justify-center">
                                        <a href="/article/<?= $article['id'] ?>" class="w-4 mr-4 transform hover:text-blue-500 hover:scale-110" title="Xem">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/articles/edit?id=<?= $article['id'] ?>" class="w-4 mr-4 transform hover:text-yellow-500 hover:scale-110" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/articles/delete?id=<?= $article['id'] ?>" class="w-4 transform hover:text-red-500 hover:scale-110" 
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Phân trang -->
            <?php if ($total_pages > 1): ?>
                <div class="flex justify-center mt-6">
                    <nav>
                        <ul class="flex">
                            <?php if ($current_page > 1): ?>
                                <li>
                                    <a href="?page=<?= $current_page - 1 ?>" class="px-3 py-1 bg-gray-200 text-gray-700 mx-1 rounded hover:bg-gray-300">
                                        &laquo; Trước
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li>
                                    <a href="?page=<?= $i ?>" class="px-3 py-1 mx-1 rounded <?= $i == $current_page ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($current_page < $total_pages): ?>
                                <li>
                                    <a href="?page=<?= $current_page + 1 ?>" class="px-3 py-1 bg-gray-200 text-gray-700 mx-1 rounded hover:bg-gray-300">
                                        Sau &raquo;
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>