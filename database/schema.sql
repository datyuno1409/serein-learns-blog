-- Create database
CREATE DATABASE IF NOT EXISTS serein_blog;
USE serein_blog;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    avatar VARCHAR(255),
    is_admin BOOLEAN DEFAULT FALSE,
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create articles table
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    image VARCHAR(255),
    category_id INT,
    user_id INT NOT NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create tags table
CREATE TABLE IF NOT EXISTS tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create article_tags table
CREATE TABLE IF NOT EXISTS article_tags (
    article_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- Create comments table
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    article_id INT NOT NULL,
    user_id INT NOT NULL,
    parent_id INT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
);

-- Create projects table
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    short_description TEXT,
    long_description TEXT,
    image VARCHAR(255),
    github_url VARCHAR(255),
    demo_url VARCHAR(255),
    technologies JSON,
    status ENUM('active', 'archived', 'in_progress') DEFAULT 'active',
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO users (username, password, email, is_admin) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', TRUE);

-- Insert default categories
INSERT INTO categories (name, slug, description) VALUES 
('Uncategorized', 'uncategorized', 'Default category'),
('Technology', 'technology', 'Technology related articles'),
('Programming', 'programming', 'Programming related articles'),
('Web Development', 'web-development', 'Web development related articles');

-- Insert sample projects
INSERT INTO projects (title, slug, short_description, long_description, image, github_url, demo_url, technologies, status, featured) VALUES 
('UniSAST Platform', 'unisast-platform', 'A web-based platform integrating open-source SAST tools for automated code security analysis and DevSecOps support in SMEs.', 'UniSAST Platform là một nền tảng web tích hợp các công cụ SAST mã nguồn mở để phân tích bảo mật mã tự động và hỗ trợ DevSecOps trong các doanh nghiệp vừa và nhỏ. Nền tảng này được xây dựng với React, Node.js và Docker để đảm bảo khả năng mở rộng và dễ dàng triển khai.', '/assets/images/projects/unisast.jpg', 'https://github.com/serein/unisast-platform', 'https://unisast-demo.serein.dev', '["React", "Node.js", "Docker", "Security", "DevSecOps"]', 'active', TRUE),
('Network Security Monitor', 'network-security-monitor', 'A network security monitoring tool that analyzes traffic patterns and detects potential security threats using machine learning algorithms.', 'Network Security Monitor là một công cụ giám sát bảo mật mạng sử dụng thuật toán machine learning để phân tích các mẫu lưu lượng mạng và phát hiện các mối đe dọa bảo mật tiềm ẩn. Công cụ này được phát triển bằng Python và tích hợp với các thư viện ML hiện đại.', '/assets/images/projects/network-monitor.jpg', 'https://github.com/serein/network-security-monitor', NULL, '["Python", "Machine Learning", "Network Security"]', 'active', FALSE),
('Learning Blog', 'learning-blog', 'A personal blog platform for sharing technology and security knowledge. Built with PHP and modern frontend technologies.', 'Learning Blog là một nền tảng blog cá nhân để chia sẻ kiến thức về công nghệ và bảo mật. Được xây dựng với PHP và các công nghệ frontend hiện đại như Tailwind CSS, JavaScript ES6+. Hệ thống hỗ trợ quản lý bài viết, danh mục, bình luận và người dùng.', '/assets/images/projects/learning-blog.jpg', 'https://github.com/serein/learning-blog', 'https://blog.serein.dev', '["PHP", "JavaScript", "MySQL", "Tailwind CSS"]', 'active', FALSE);