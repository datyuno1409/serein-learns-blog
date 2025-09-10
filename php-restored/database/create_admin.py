#!/usr/bin/env python3
import mysql.connector
from mysql.connector import Error
import hashlib
import bcrypt

def create_admin_user():
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
            
            # Hash password using bcrypt (compatible with PHP password_hash)
            password = 'Fpt1409!@'
            # Using bcrypt to create hash compatible with PHP password_hash
            hashed_password = bcrypt.hashpw(password.encode('utf-8'), bcrypt.gensalt()).decode('utf-8')
            
            # Check if admin user already exists
            cursor.execute("SELECT COUNT(*) FROM users WHERE username = 'admin'")
            if cursor.fetchone()[0] > 0:
                print("Admin user already exists. Updating password...")
                # Update existing admin user
                cursor.execute("""
                    UPDATE users 
                    SET password = %s, role = 'admin', is_active = TRUE, updated_at = NOW()
                    WHERE username = 'admin'
                """, (hashed_password,))
            else:
                print("Creating new admin user...")
                # Create new admin user
                cursor.execute("""
                    INSERT INTO users (username, email, password, full_name, role, is_active, created_at, updated_at) 
                    VALUES (%s, %s, %s, %s, %s, %s, NOW(), NOW())
                """, ('admin', 'admin@sereinblog.com', hashed_password, 'Administrator', 'admin', True))
            
            connection.commit()
            print("Admin user created/updated successfully!")
            print("Username: admin")
            print("Password: Fpt1409!@")
            print("Email: admin@sereinblog.com")
            
    except Error as e:
        print(f"Error: {e}")
        
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()
            print("MySQL connection closed.")

if __name__ == "__main__":
    create_admin_user()