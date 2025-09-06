#!/usr/bin/env python3
import mysql.connector
from mysql.connector import Error

def fix_comments_column():
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
            
            print("Fixing comments table structure...")
            
            # First, drop any existing foreign key constraints on post_id
            try:
                cursor.execute("""
                    ALTER TABLE comments 
                    DROP FOREIGN KEY fk_comments_post
                """)
                print("Dropped existing foreign key constraint.")
            except Error as e:
                print(f"No existing foreign key to drop: {e}")
            
            # Rename post_id column to article_id
            try:
                cursor.execute("""
                    ALTER TABLE comments 
                    CHANGE COLUMN post_id article_id INT NOT NULL
                """)
                print("Renamed post_id to article_id successfully!")
            except Error as e:
                print(f"Error renaming column: {e}")
            
            # Add foreign key constraint for article_id
            try:
                cursor.execute("""
                    ALTER TABLE comments 
                    ADD CONSTRAINT fk_comments_article 
                    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
                """)
                print("Added foreign key constraint for article_id.")
            except Error as e:
                print(f"Error adding foreign key: {e}")
            
            # Also add user_id column if it doesn't exist
            cursor.execute("DESCRIBE comments")
            columns = [col[0] for col in cursor.fetchall()]
            
            if 'user_id' not in columns:
                print("Adding user_id column...")
                cursor.execute("""
                    ALTER TABLE comments 
                    ADD COLUMN user_id INT DEFAULT 1 AFTER article_id
                """)
                
                cursor.execute("""
                    ALTER TABLE comments 
                    ADD CONSTRAINT fk_comments_user 
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                """)
                print("Added user_id column and constraint.")
            
            connection.commit()
            print("\nComments table structure fixed successfully!")
            
            # Show final structure
            cursor.execute("DESCRIBE comments")
            columns = cursor.fetchall()
            print("\nFinal table structure:")
            for col in columns:
                print(f"  - {col[0]} ({col[1]})")
            
    except Error as e:
        print(f"Database Error: {e}")
        
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()
            print("MySQL connection closed.")

if __name__ == "__main__":
    fix_comments_column()