-- Tạo cơ sở dữ liệu blogdb
CREATE DATABASE IF NOT EXISTS blogdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Sử dụng cơ sở dữ liệu blogdb
USE blogdb;

-- Tạo bảng posts
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Thêm dữ liệu mẫu
INSERT INTO posts (title, content) VALUES 
('Bài viết đầu tiên', 'Đây là nội dung của bài viết đầu tiên trong hệ thống quản lý blog.'),
('Hướng dẫn sử dụng AdminLTE', 'AdminLTE là một template admin miễn phí dựa trên Bootstrap 4 và jQuery.'),
('Giới thiệu về PHP và MySQL', 'PHP và MySQL là combo hoàn hảo để phát triển các ứng dụng web động.');