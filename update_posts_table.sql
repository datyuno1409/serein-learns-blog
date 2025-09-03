-- Cập nhật bảng posts để thêm các cột cần thiết
USE blogdb;

-- Thêm các cột mới vào bảng posts
ALTER TABLE posts 
ADD COLUMN IF NOT EXISTS slug VARCHAR(500) NOT NULL DEFAULT '' AFTER title,
ADD COLUMN IF NOT EXISTS excerpt TEXT AFTER slug,
ADD COLUMN IF NOT EXISTS featured_image VARCHAR(500) AFTER content,
ADD COLUMN IF NOT EXISTS category_id INT AFTER author_id,
ADD COLUMN IF NOT EXISTS status ENUM('draft', 'published', 'archived') DEFAULT 'draft' AFTER category_id,
ADD COLUMN IF NOT EXISTS is_featured BOOLEAN DEFAULT FALSE AFTER status,
ADD COLUMN IF NOT EXISTS view_count INT DEFAULT 0 AFTER is_featured,
ADD COLUMN IF NOT EXISTS meta_title VARCHAR(255) AFTER view_count,
ADD COLUMN IF NOT EXISTS meta_description TEXT AFTER meta_title,
ADD COLUMN IF NOT EXISTS published_at TIMESTAMP NULL AFTER meta_description;

-- Thêm các index
ALTER TABLE posts 
ADD INDEX IF NOT EXISTS idx_status (status),
ADD INDEX IF NOT EXISTS idx_published_at (published_at),
ADD INDEX IF NOT EXISTS idx_category_id (category_id);

-- Thêm foreign key cho category_id
ALTER TABLE posts 
ADD CONSTRAINT fk_posts_category 
FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;

SELECT 'Posts table updated successfully!' as message;