#!/usr/bin/env python3
import mysql.connector
from mysql.connector import Error

def sync_comments_data():
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
            
            print("Syncing comments data...")
            
            # Update article_id with post_id values
            cursor.execute("""
                UPDATE comments 
                SET article_id = post_id 
                WHERE article_id IS NULL OR article_id = 0
            """)
            print("Updated article_id with post_id values.")
            
            # Drop the old post_id column
            try:
                cursor.execute("""
                    ALTER TABLE comments 
                    DROP COLUMN post_id
                """)
                print("Dropped post_id column.")
            except Error as e:
                print(f"Error dropping post_id column: {e}")
            
            # Now try to add the foreign key constraint
            try:
                cursor.execute("""
                    ALTER TABLE comments 
                    ADD CONSTRAINT fk_comments_article 
                    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
                """)
                print("Added foreign key constraint for article_id.")
            except Error as e:
                print(f"Error adding foreign key: {e}")
                # If foreign key fails, it might be because there are orphaned records
                print("Checking for orphaned comments...")
                cursor.execute("""
                    SELECT COUNT(*) as count 
                    FROM comments c 
                    LEFT JOIN articles a ON c.article_id = a.id 
                    WHERE a.id IS NULL
                """)
                orphaned_count = cursor.fetchone()[0]
                if orphaned_count > 0:
                    print(f"Found {orphaned_count} orphaned comments. Deleting them...")
                    cursor.execute("""
                        DELETE c FROM comments c 
                        LEFT JOIN articles a ON c.article_id = a.id 
                        WHERE a.id IS NULL
                    """)
                    print("Deleted orphaned comments.")
                    
                    # Try adding foreign key again
                    try:
                        cursor.execute("""
                            ALTER TABLE comments 
                            ADD CONSTRAINT fk_comments_article 
                            FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
                        """)
                        print("Added foreign key constraint after cleanup.")
                    except Error as e2:
                        print(f"Still error adding foreign key: {e2}")
            
            connection.commit()
            print("\nComments data sync completed successfully!")
            
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
    sync_comments_data()