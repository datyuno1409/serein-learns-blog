<?php
function performGlobalSearch($query, $limit = 10) {
    global $pdo;
    
    $results = [
        'posts' => [],
        'categories' => [],
        'users' => [],
        'total' => 0
    ];
    
    if (empty(trim($query))) {
        return $results;
    }
    
    $searchTerm = '%' . $query . '%';
    
    try {
        $stmt = $pdo->prepare("
            SELECT id, title, slug, status, created_at, 'post' as type
            FROM posts 
            WHERE title LIKE ? OR content LIKE ? OR excerpt LIKE ?
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $limit]);
        $results['posts'] = $stmt->fetchAll();
        
        $stmt = $pdo->prepare("
            SELECT id, name, slug, created_at, 'category' as type
            FROM categories 
            WHERE name LIKE ? OR description LIKE ?
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$searchTerm, $searchTerm, $limit]);
        $results['categories'] = $stmt->fetchAll();
        
        $stmt = $pdo->prepare("
            SELECT id, username, email, full_name, role, created_at, 'user' as type
            FROM users 
            WHERE username LIKE ? OR email LIKE ? OR full_name LIKE ?
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $limit]);
        $results['users'] = $stmt->fetchAll();
        
        $results['total'] = count($results['posts']) + count($results['categories']) + count($results['users']);
        
    } catch (Exception $e) {
        error_log('Global search error: ' . $e->getMessage());
    }
    
    return $results;
}

function renderSearchResults($results, $query) {
    if ($results['total'] === 0) {
        echo '<div class="search-no-results">';
        echo '<p class="text-muted">Không tìm thấy kết quả nào cho "' . htmlspecialchars($query) . '"</p>';
        echo '</div>';
        return;
    }
    
    echo '<div class="search-results">';
    echo '<div class="search-summary mb-3">';
    echo '<p class="text-muted">Tìm thấy ' . $results['total'] . ' kết quả cho "' . htmlspecialchars($query) . '"</p>';
    echo '</div>';
    
    if (!empty($results['posts'])) {
        echo '<div class="search-section mb-4">';
        echo '<h5><i class="fas fa-newspaper"></i> Bài viết (' . count($results['posts']) . ')</h5>';
        echo '<div class="list-group">';
        foreach ($results['posts'] as $post) {
            $statusClass = $post['status'] === 'published' ? 'success' : 'warning';
            $statusText = $post['status'] === 'published' ? 'Đã xuất bản' : 'Nháp';
            echo '<a href="/admin/posts/edit?id=' . $post['id'] . '" class="list-group-item list-group-item-action">';
            echo '<div class="d-flex w-100 justify-content-between">';
            echo '<h6 class="mb-1">' . htmlspecialchars($post['title']) . '</h6>';
            echo '<small><span class="badge badge-' . $statusClass . '">' . $statusText . '</span></small>';
            echo '</div>';
            echo '<small class="text-muted">' . date('d/m/Y H:i', strtotime($post['created_at'])) . '</small>';
            echo '</a>';
        }
        echo '</div>';
        echo '</div>';
    }
    
    if (!empty($results['categories'])) {
        echo '<div class="search-section mb-4">';
        echo '<h5><i class="fas fa-tags"></i> Danh mục (' . count($results['categories']) . ')</h5>';
        echo '<div class="list-group">';
        foreach ($results['categories'] as $category) {
            echo '<a href="/admin/categories/edit?id=' . $category['id'] . '" class="list-group-item list-group-item-action">';
            echo '<div class="d-flex w-100 justify-content-between">';
            echo '<h6 class="mb-1">' . htmlspecialchars($category['name']) . '</h6>';
            echo '</div>';
            echo '<small class="text-muted">' . date('d/m/Y H:i', strtotime($category['created_at'])) . '</small>';
            echo '</a>';
        }
        echo '</div>';
        echo '</div>';
    }
    
    if (!empty($results['users'])) {
        echo '<div class="search-section mb-4">';
        echo '<h5><i class="fas fa-users"></i> Người dùng (' . count($results['users']) . ')</h5>';
        echo '<div class="list-group">';
        foreach ($results['users'] as $user) {
            $roleClass = $user['role'] === 'admin' ? 'danger' : 'primary';
            $roleText = $user['role'] === 'admin' ? 'Admin' : 'Người dùng';
            echo '<a href="/admin/users/edit?id=' . $user['id'] . '" class="list-group-item list-group-item-action">';
            echo '<div class="d-flex w-100 justify-content-between">';
            echo '<h6 class="mb-1">' . htmlspecialchars($user['username']) . '</h6>';
            echo '<small><span class="badge badge-' . $roleClass . '">' . $roleText . '</span></small>';
            echo '</div>';
            echo '<p class="mb-1">' . htmlspecialchars($user['full_name'] ?: $user['email']) . '</p>';
            echo '<small class="text-muted">' . date('d/m/Y H:i', strtotime($user['created_at'])) . '</small>';
            echo '</a>';
        }
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>';
}
?>