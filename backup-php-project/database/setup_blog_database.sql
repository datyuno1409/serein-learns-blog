-- Tạo cấu trúc database cho blog admin dashboard
-- Sử dụng database blogdb đã có

USE blogdb;

-- Tạo bảng categories (danh mục)
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    parent_id INT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Tạo bảng posts (bài viết)
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    slug VARCHAR(500) NOT NULL UNIQUE,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(500),
    author_id INT NOT NULL,
    category_id INT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    is_featured BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    meta_title VARCHAR(255),
    meta_description TEXT,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_published_at (published_at),
    INDEX idx_author_id (author_id),
    INDEX idx_category_id (category_id)
);

-- Tạo bảng comments (bình luận)
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    parent_id INT NULL,
    author_name VARCHAR(255) NOT NULL,
    author_email VARCHAR(255) NOT NULL,
    author_website VARCHAR(500),
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'spam', 'trash') DEFAULT 'pending',
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE,
    INDEX idx_post_id (post_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Tạo bảng tags (thẻ)
CREATE TABLE IF NOT EXISTS tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tạo bảng post_tags (quan hệ nhiều-nhiều giữa posts và tags)
CREATE TABLE IF NOT EXISTS post_tags (
    post_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- Tạo bảng media (quản lý file media)
CREATE TABLE IF NOT EXISTS media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(500) NOT NULL,
    original_name VARCHAR(500) NOT NULL,
    file_path VARCHAR(1000) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    alt_text VARCHAR(500),
    caption TEXT,
    uploaded_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_mime_type (mime_type),
    INDEX idx_uploaded_by (uploaded_by)
);

-- Cập nhật bảng users để thêm các trường cần thiết cho blog
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS bio TEXT,
ADD COLUMN IF NOT EXISTS avatar VARCHAR(500),
ADD COLUMN IF NOT EXISTS website VARCHAR(500),
ADD COLUMN IF NOT EXISTS social_facebook VARCHAR(500),
ADD COLUMN IF NOT EXISTS social_twitter VARCHAR(500),
ADD COLUMN IF NOT EXISTS social_linkedin VARCHAR(500);

-- Thêm dữ liệu mẫu cho categories
INSERT IGNORE INTO categories (name, slug, description) VALUES
('Công nghệ', 'cong-nghe', 'Các bài viết về công nghệ thông tin'),
('Lập trình', 'lap-trinh', 'Hướng dẫn và kinh nghiệm lập trình'),
('Thiết kế', 'thiet-ke', 'Thiết kế web và đồ họa'),
('Kinh doanh', 'kinh-doanh', 'Kinh nghiệm kinh doanh và khởi nghiệp'),
('Đời sống', 'doi-song', 'Chia sẻ về cuộc sống');

-- Thêm dữ liệu mẫu cho tags
INSERT IGNORE INTO tags (name, slug, description) VALUES
('PHP', 'php', 'Ngôn ngữ lập trình PHP'),
('JavaScript', 'javascript', 'Ngôn ngữ lập trình JavaScript'),
('MySQL', 'mysql', 'Hệ quản trị cơ sở dữ liệu MySQL'),
('HTML', 'html', 'Ngôn ngữ đánh dấu HTML'),
('CSS', 'css', 'Cascading Style Sheets'),
('React', 'react', 'Thư viện JavaScript React'),
('Vue.js', 'vuejs', 'Framework JavaScript Vue.js'),
('Laravel', 'laravel', 'Framework PHP Laravel'),
('WordPress', 'wordpress', 'Hệ quản trị nội dung WordPress'),
('SEO', 'seo', 'Tối ưu hóa công cụ tìm kiếm');

-- Thêm bài viết mẫu
INSERT IGNORE INTO posts (title, excerpt, content, author_id, category_id, status, view_count, published_at) VALUES
('Hướng dẫn tạo blog với PHP và MySQL', 
 'Bài viết hướng dẫn chi tiết cách tạo một blog đơn giản sử dụng PHP và MySQL từ đầu.', 
 '<h2>Giới thiệu</h2><p>Trong bài viết này, chúng ta sẽ học cách tạo một blog đơn giản sử dụng PHP và MySQL...</p><h2>Chuẩn bị</h2><p>Trước khi bắt đầu, bạn cần chuẩn bị...</p>', 
 1, 2, 'published', 150, NOW()),
 
('AdminLTE3 - Framework quản trị đẹp cho PHP',
 'AdminLTE3 là một framework frontend miễn phí giúp tạo giao diện quản trị đẹp và chuyên nghiệp.',
 '<h2>AdminLTE3 là gì?</h2><p>AdminLTE3 là phiên bản mới nhất của AdminLTE...</p><h2>Tính năng nổi bật</h2><ul><li>Responsive design</li><li>Nhiều component</li></ul>',
 1, 3, 'published', 89, NOW()),
 
('JavaScript ES6+ - Những tính năng mới cần biết',
 'Tìm hiểu về các tính năng mới trong JavaScript ES6+ như arrow functions, destructuring, async/await.',
 '<h2>Arrow Functions</h2><p>Arrow functions là một cách viết function ngắn gọn hơn...</p><h2>Destructuring</h2><p>Destructuring cho phép bạn trích xuất dữ liệu từ array hoặc object...</p>',
 1, 2, 'draft', 0, NULL),
 
('Thiết kế UI/UX hiện đại cho website',
 'Các nguyên tắc và xu hướng thiết kế UI/UX hiện đại để tạo ra website đẹp và thân thiện với người dùng.',
 '<h2>Nguyên tắc thiết kế</h2><p>Thiết kế UI/UX tốt cần tuân theo các nguyên tắc...</p><h2>Xu hướng 2024</h2><p>Năm 2024, các xu hướng thiết kế nổi bật...</p>',
 1, 3, 'published', 234, NOW());

-- Thêm comments mẫu
INSERT IGNORE INTO comments (post_id, author_name, author_email, content, status) VALUES
(1, 'Nguyễn Văn A', 'nguyenvana@email.com', 'Bài viết rất hữu ích, cảm ơn tác giả!', 'approved'),
(1, 'Trần Thị B', 'tranthib@email.com', 'Mình đã làm theo hướng dẫn và thành công. Thanks!', 'approved'),
(2, 'Lê Văn C', 'levanc@email.com', 'AdminLTE3 thật sự rất đẹp và dễ sử dụng.', 'approved'),
(4, 'Phạm Thị D', 'phamthid@email.com', 'Thiết kế UI/UX là một lĩnh vực rất thú vị.', 'pending');

-- Thêm post_tags mẫu
INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES
(1, 1), (1, 3), (1, 4), (1, 5),  -- PHP, MySQL, HTML, CSS
(2, 1), (2, 4), (2, 5),          -- PHP, HTML, CSS
(3, 2),                          -- JavaScript
(4, 4), (4, 5), (4, 10);        -- HTML, CSS, SEO

SELECT 'Blog database setup completed successfully!' as message;