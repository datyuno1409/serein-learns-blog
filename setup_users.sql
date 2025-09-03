-- Tạo bảng users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role ENUM('admin', 'editor', 'user') DEFAULT 'user',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Thêm tài khoản admin mặc định
-- Mật khẩu: admin123 (đã được hash bằng password_hash)
INSERT INTO users (username, email, password, full_name, role, is_active) VALUES 
('admin', 'admin@sereinblog.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', TRUE);

-- Thêm một số user demo
INSERT INTO users (username, email, password, full_name, role, is_active) VALUES 
('editor', 'editor@sereinblog.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Editor User', 'editor', TRUE),
('user1', 'user1@sereinblog.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Regular User', 'user', TRUE);

-- Cập nhật bảng posts để thêm author_id
ALTER TABLE posts ADD COLUMN IF NOT EXISTS author_id INT DEFAULT 1;
ALTER TABLE posts ADD CONSTRAINT fk_posts_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL;

-- Cập nhật các bài viết hiện tại với author_id = 1 (admin)
UPDATE posts SET author_id = 1 WHERE author_id IS NULL;

SELECT 'Database setup completed successfully!' as message;