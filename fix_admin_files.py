#!/usr/bin/env python3
import os
import re

# Danh sách các file cần sửa
files_to_fix = [
    'admin/posts_delete.php',
    'admin/categories_edit.php', 
    'admin/users_delete.php',
    'admin/categories_delete.php',
    'admin/posts_add.php',
    'admin/categories_add.php',
    'admin/users_add.php',
    'admin/search.php',
    'admin/dashboard.php',
    'admin/users_edit.php',
    'admin/posts_edit.php'
]

def fix_file(file_path):
    """Sửa file để sử dụng biến $pdo từ controller"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Thay thế require config/database.php
        patterns = [
            r"require_once __DIR__ \. '/../config/database\.php';",
            r"require_once '../config/database\.php';"
        ]
        
        for pattern in patterns:
            content = re.sub(pattern, '', content)
        
        # Thêm kiểm tra biến $pdo ở đầu file sau <?php
        if '// $pdo variable is passed from AdminController' not in content:
            # Tìm vị trí sau <?php
            php_match = re.search(r'<\?php\s*\n', content)
            if php_match:
                insert_pos = php_match.end()
                pdo_check = """// $pdo variable is passed from AdminController
if (!isset($pdo)) {
    die('Database connection not available');
}

"""
                content = content[:insert_pos] + pdo_check + content[insert_pos:]
        
        # Ghi lại file
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        
        print(f"✓ Fixed: {file_path}")
        return True
        
    except Exception as e:
        print(f"✗ Error fixing {file_path}: {e}")
        return False

def main():
    print("Fixing admin files to use $pdo variable from controller...")
    
    fixed_count = 0
    for file_path in files_to_fix:
        if os.path.exists(file_path):
            if fix_file(file_path):
                fixed_count += 1
        else:
            print(f"✗ File not found: {file_path}")
    
    print(f"\nFixed {fixed_count}/{len(files_to_fix)} files.")

if __name__ == '__main__':
    main()