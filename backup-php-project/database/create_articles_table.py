#!/usr/bin/env python3
import mysql.connector
from mysql.connector import Error

def create_articles_table():
    try:
        # Connect to MySQL server
        connection = mysql.connector.connect(
            host='localhost',
            user='root',
            password='',
            database='blogdb'
        )
        
        if connection.is_connected():
            cursor = connection.cursor()
            
            # Create articles table (similar to posts but with different structure)
            create_articles_sql = """
            CREATE TABLE IF NOT EXISTS articles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(500) NOT NULL,
                slug VARCHAR(500) NOT NULL UNIQUE,
                content LONGTEXT NOT NULL,
                image VARCHAR(500),
                category_id INT,
                user_id INT NOT NULL,
                status ENUM('draft', 'published') DEFAULT 'draft',
                views INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
            """
            
            cursor.execute(create_articles_sql)
            print("Articles table created successfully!")
            
            # Create article_tags table
            create_article_tags_sql = """
            CREATE TABLE IF NOT EXISTS article_tags (
                article_id INT NOT NULL,
                tag_id INT NOT NULL,
                PRIMARY KEY (article_id, tag_id),
                FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
                FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
            )
            """
            
            cursor.execute(create_article_tags_sql)
            print("Article_tags table created successfully!")
            
            # Insert sample articles
            insert_articles_sql = """
            INSERT IGNORE INTO articles (title, slug, content, user_id, category_id, status, views) VALUES 
            ('Hướng dẫn tạo blog với PHP và MySQL', 'huong-dan-tao-blog-voi-php-va-mysql', 
             '<h2>Giới thiệu</h2><p>Trong bài viết này, chúng ta sẽ học cách tạo một blog đơn giản sử dụng PHP và MySQL...</p><h2>Chuẩn bị</h2><p>Trước khi bắt đầu, bạn cần chuẩn bị...</p>', 
             1, 2, 'published', 150),
             
            ('AdminLTE3 - Framework quản trị đẹp cho PHP', 'adminlte3-framework-quan-tri-dep-cho-php',
             '<h2>AdminLTE3 là gì?</h2><p>AdminLTE3 là phiên bản mới nhất của AdminLTE...</p><h2>Tính năng nổi bật</h2><ul><li>Responsive design</li><li>Nhiều component</li></ul>',
             1, 2, 'published', 89),
             
            ('JavaScript ES6+ - Những tính năng mới cần biết', 'javascript-es6-nhung-tinh-nang-moi-can-biet',
             '<h2>Arrow Functions</h2><p>Arrow functions là một cách viết function ngắn gọn hơn...</p><h2>Destructuring</h2><p>Destructuring cho phép bạn trích xuất dữ liệu từ array hoặc object...</p>',
             1, 2, 'draft', 0),
             
            ('Thiết kế UI/UX hiện đại cho website', 'thiet-ke-ui-ux-hien-dai-cho-website',
             '<h2>Nguyên tắc thiết kế</h2><p>Thiết kế UI/UX tốt cần tuân theo các nguyên tắc...</p><h2>Xu hướng 2024</h2><p>Năm 2024, các xu hướng thiết kế nổi bật...</p>',
             1, 3, 'published', 234)
            """
            
            cursor.execute(insert_articles_sql)
            print("Sample articles inserted successfully!")
            
            connection.commit()
            print("\nArticles table setup completed successfully!")
            
    except Error as e:
        print(f"Database Error: {e}")
        
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()
            print("MySQL connection closed.")

if __name__ == "__main__":
    create_articles_table()