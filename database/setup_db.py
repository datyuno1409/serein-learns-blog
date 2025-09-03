import mysql.connector
from mysql.connector import Error
import os

def setup_database():
    try:
        # Connect to MySQL server
        connection = mysql.connector.connect(
            host="localhost",
            user="root",
            password=""
        )
        
        if connection.is_connected():
            cursor = connection.cursor()
            
            # Read SQL file
            script_dir = os.path.dirname(os.path.abspath(__file__))
            schema_path = os.path.join(script_dir, 'schema.sql')
            
            with open(schema_path, 'r') as file:
                sql_commands = file.read()
            
            # Execute SQL commands
            for command in sql_commands.split(';'):
                if command.strip():
                    cursor.execute(command + ';')
            
            connection.commit()
            print("Database setup completed successfully!")
            
    except Error as e:
        print(f"Error: {e}")
        
    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()

if __name__ == "__main__":
    setup_database()