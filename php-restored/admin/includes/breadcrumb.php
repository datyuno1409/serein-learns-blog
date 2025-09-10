<?php
// Breadcrumb helper function
function generateBreadcrumb($current_page, $parent_menu = null, $sub_menu = null) {
    $breadcrumbs = [];
    
    // Always start with Dashboard
    $breadcrumbs[] = [
        'title' => 'Dashboard',
        'url' => 'dashboard.php',
        'active' => false
    ];
    
    // Add parent menu if exists
    if ($parent_menu) {
        $breadcrumbs[] = [
            'title' => $parent_menu['title'],
            'url' => $parent_menu['url'] ?? null,
            'active' => false
        ];
    }
    
    // Add sub menu if exists
    if ($sub_menu) {
        $breadcrumbs[] = [
            'title' => $sub_menu,
            'url' => null,
            'active' => true
        ];
    } else {
        // Mark current page as active
        $breadcrumbs[] = [
            'title' => $current_page,
            'url' => null,
            'active' => true
        ];
    }
    
    return $breadcrumbs;
}

// Render breadcrumb HTML
function renderBreadcrumb($breadcrumbs) {
    echo '<nav aria-label="breadcrumb">';
    echo '<ol class="breadcrumb">';
    
    foreach ($breadcrumbs as $index => $crumb) {
        if ($crumb['active']) {
            echo '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($crumb['title']) . '</li>';
        } else {
            if ($crumb['url']) {
                echo '<li class="breadcrumb-item"><a href="' . htmlspecialchars($crumb['url']) . '">' . htmlspecialchars($crumb['title']) . '</a></li>';
            } else {
                echo '<li class="breadcrumb-item">' . htmlspecialchars($crumb['title']) . '</li>';
            }
        }
    }
    
    echo '</ol>';
    echo '</nav>';
}

// Auto-generate breadcrumb based on current file
function autoBreadcrumb() {
    $current_file = basename($_SERVER['PHP_SELF']);
    $breadcrumbs = [];
    
    switch ($current_file) {
        case 'dashboard.php':
            $breadcrumbs = generateBreadcrumb('Dashboard');
            break;
            
        case 'posts_list.php':
            $breadcrumbs = generateBreadcrumb('All Posts', ['title' => 'Posts']);
            break;
            
        case 'posts_add.php':
            $breadcrumbs = generateBreadcrumb('Add Post', ['title' => 'Posts'], 'Add Post');
            break;
            
        case 'posts_edit.php':
            $breadcrumbs = generateBreadcrumb('Edit Post', ['title' => 'Posts'], 'Edit Post');
            break;
            
        case 'categories_list.php':
            $breadcrumbs = generateBreadcrumb('Categories');
            break;
            
        case 'media_list.php':
            $breadcrumbs = generateBreadcrumb('Media');
            break;
            
        case 'users_list.php':
            $breadcrumbs = generateBreadcrumb('All Users', ['title' => 'Users']);
            break;
            
        case 'users_add.php':
            $breadcrumbs = generateBreadcrumb('Add User', ['title' => 'Users'], 'Add User');
            break;
            
        case 'users_edit.php':
            $breadcrumbs = generateBreadcrumb('Edit User', ['title' => 'Users'], 'Edit User');
            break;
            
        case 'comments_list.php':
            $breadcrumbs = generateBreadcrumb('Comments');
            break;
            
        case 'statistics.php':
            $breadcrumbs = generateBreadcrumb('Analytics');
            break;
            
        case 'settings.php':
            $breadcrumbs = generateBreadcrumb('Settings');
            break;
            
        case 'backup.php':
            $breadcrumbs = generateBreadcrumb('Backup');
            break;
            
        default:
            $breadcrumbs = generateBreadcrumb('Dashboard');
            break;
    }
    
    renderBreadcrumb($breadcrumbs);
}
?>