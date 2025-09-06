#!/usr/bin/env python3
import mysql.connector
from mysql.connector import Error
import os

def setup_database():
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
            
            # Read SQL file
            with open('setup_blog_database.sql', 'r', encoding='utf-8') as file:
                sql_content = file.read()
            
            # Split by semicolon and execute each command
            commands = sql_content.split(';')
            
            for command in commands:
                command = command.strip()
                if command and not command.startswith('--'):
                    try:
                        cursor.execute(command)
                        print(f"Executed: {command[:50]}...")
                    except Error as e:
                        if "already exists" in str(e) or "Duplicate entry" in str(e):
                            print(f"Skipped (already exists): {command[:50]}...")
                        else:
                            print(f"Error executing command: {e}")
                            print(f"Command: {command[:100]}...")
            
            connection.commit()
            print("\nDatabase setup completed successfully!")
            
    except Error as e:
        print(f"Database Error: {e}")
        
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()
            print("MySQL connection closed.")

if __name__ == "__main__":
    setup_database()