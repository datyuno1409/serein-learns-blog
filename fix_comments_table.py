#!/usr/bin/env python3
import mysql.connector
from mysql.connector import Error

def fix_comments_table():
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
            
            # Check if comments table exists and its structure
            cursor.execute("SHOW TABLES LIKE 'comments'")
            if cursor.fetchone():
                print("Comments table exists. Checking structure...")
                cursor.execute("DESCRIBE comments")
                columns = cursor.fetchall()
                print("Current columns:")
                for col in columns:
                    print(f"  - {col[0]} ({col[1]})")
                
                # Check if article_id column exists
                column_names = [col[0] for col in columns]
                if 'article_id' not in column_names:
                    print("\nAdding article_id column...")
                    cursor.execute("""
                        ALTER TABLE comments 
                        ADD COLUMN article_id INT NOT NULL AFTER content
                    """)
                    
                    # Add foreign key constraint
                    cursor.execute("""
                        ALTER TABLE comments 
                        ADD CONSTRAINT fk_comments_article 
                        FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
                    """)
                    print("article_id column added successfully!")
                else:
                    print("article_id column already exists.")
            else:
                print("Comments table doesn't exist. Creating it...")
                create_comments_sql = """
                CREATE TABLE comments (
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
                )
                """
                cursor.execute(create_comments_sql)
                print("Comments table created successfully!")
            
            connection.commit()
            print("\nComments table setup completed successfully!")
            
    except Error as e:
        print(f"Database Error: {e}")
        
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()
            print("MySQL connection closed.")

if __name__ == "__main__":
    fix_comments_table()