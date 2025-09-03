<!-- Trang tạo bài viết mới -->
<div class="container mx-auto px-4 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Tạo bài viết mới</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <form action="/articles/create" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="title" class="block text-gray-700 dark:text-gray-300 mb-2">Tiêu đề <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
            </div>
            
            <div class="mb-4">
                <label for="category_id" class="block text-gray-700 dark:text-gray-300 mb-2">Danh mục <span class="text-red-500">*</span></label>
                <select id="category_id" name="category_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="tags" class="block text-gray-700 dark:text-gray-300 mb-2">Thẻ</label>
                <select id="tags" name="tags[]" multiple
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <?php foreach ($tags as $tag): ?>
                        <option value="<?= $tag['id'] ?>" <?= (isset($_POST['tags']) && in_array($tag['id'], $_POST['tags'])) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tag['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-gray-500">Giữ Ctrl để chọn nhiều thẻ</small>
            </div>
            
            <div class="mb-4">
                <label for="featured_image" class="block text-gray-700 dark:text-gray-300 mb-2">Ảnh đại diện</label>
                <input type="file" id="featured_image" name="featured_image" accept="image/jpeg,image/png,image/webp"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <small class="text-gray-500">Chấp nhận JPG, PNG, WEBP. Tối đa 5MB.</small>
            </div>
            
            <div class="mb-4">
                <label for="excerpt" class="block text-gray-700 dark:text-gray-300 mb-2">Tóm tắt</label>
                <textarea id="excerpt" name="excerpt" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?= isset($_POST['excerpt']) ? htmlspecialchars($_POST['excerpt']) : '' ?></textarea>
            </div>
            
            <div class="mb-4">
                <label for="content" class="block text-gray-700 dark:text-gray-300 mb-2">Nội dung <span class="text-red-500">*</span></label>
                <textarea id="content" name="content" rows="10" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?= isset($_POST['content']) ? htmlspecialchars($_POST['content']) : '' ?></textarea>
            </div>
            
            <div class="mb-4">
                <label for="status" class="block text-gray-700 dark:text-gray-300 mb-2">Trạng thái</label>
                <select id="status" name="status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="draft" <?= (!isset($_POST['status']) || $_POST['status'] == 'draft') ? 'selected' : '' ?>>Bản nháp</option>
                    <option value="published" <?= (isset($_POST['status']) && $_POST['status'] == 'published') ? 'selected' : '' ?>>Công khai</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" <?= (isset($_POST['is_featured'])) ? 'checked' : '' ?>
                        class="mr-2 focus:ring-2 focus:ring-blue-500">
                    <span class="text-gray-700 dark:text-gray-300">Đánh dấu là bài viết nổi bật</span>
                </label>
            </div>
            
            <div class="flex justify-between">
                <a href="/articles" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Hủy</a>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Tạo bài viết</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Thêm trình soạn thảo rich text nếu cần
    document.addEventListener('DOMContentLoaded', function() {
        // Có thể thêm code khởi tạo trình soạn thảo ở đây
    });
</script>