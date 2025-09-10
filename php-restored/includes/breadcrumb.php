<?php
function generateBreadcrumb($current_page, $params = []) {
    $breadcrumbs = [];
    
    $breadcrumbs[] = [
        'title' => 'Trang chủ',
        'url' => '/admin',
        'icon' => 'fas fa-home'
    ];
    
    switch ($current_page) {
        case 'dashboard':
            $breadcrumbs[] = [
                'title' => 'Dashboard',
                'url' => null,
                'icon' => 'fas fa-tachometer-alt'
            ];
            break;
            
        case 'posts_list':
            $breadcrumbs[] = [
                'title' => 'Quản lý bài viết',
                'url' => null,
                'icon' => 'fas fa-newspaper'
            ];
            break;
            
        case 'posts_add':
            $breadcrumbs[] = [
                'title' => 'Quản lý bài viết',
                'url' => '/admin/posts',
                'icon' => 'fas fa-newspaper'
            ];
            $breadcrumbs[] = [
                'title' => 'Thêm bài viết',
                'url' => null,
                'icon' => 'fas fa-plus'
            ];
            break;
            
        case 'posts_edit':
            $breadcrumbs[] = [
                'title' => 'Quản lý bài viết',
                'url' => '/admin/posts',
                'icon' => 'fas fa-newspaper'
            ];
            $breadcrumbs[] = [
                'title' => 'Sửa bài viết',
                'url' => null,
                'icon' => 'fas fa-edit'
            ];
            if (isset($params['post_title'])) {
                $breadcrumbs[count($breadcrumbs) - 1]['title'] .= ': ' . $params['post_title'];
            }
            break;
            
        case 'categories_list':
            $breadcrumbs[] = [
                'title' => 'Quản lý danh mục',
                'url' => null,
                'icon' => 'fas fa-tags'
            ];
            break;
            
        case 'categories_add':
            $breadcrumbs[] = [
                'title' => 'Quản lý danh mục',
                'url' => '/admin/categories',
                'icon' => 'fas fa-tags'
            ];
            $breadcrumbs[] = [
                'title' => 'Thêm danh mục',
                'url' => null,
                'icon' => 'fas fa-plus'
            ];
            break;
            
        case 'categories_edit':
            $breadcrumbs[] = [
                'title' => 'Quản lý danh mục',
                'url' => '/admin/categories',
                'icon' => 'fas fa-tags'
            ];
            $breadcrumbs[] = [
                'title' => 'Sửa danh mục',
                'url' => null,
                'icon' => 'fas fa-edit'
            ];
            if (isset($params['category_name'])) {
                $breadcrumbs[count($breadcrumbs) - 1]['title'] .= ': ' . $params['category_name'];
            }
            break;
            
        case 'users_list':
            $breadcrumbs[] = [
                'title' => 'Quản lý người dùng',
                'url' => null,
                'icon' => 'fas fa-users'
            ];
            break;
            
        case 'users_add':
            $breadcrumbs[] = [
                'title' => 'Quản lý người dùng',
                'url' => '/admin/users',
                'icon' => 'fas fa-users'
            ];
            $breadcrumbs[] = [
                'title' => 'Thêm người dùng',
                'url' => null,
                'icon' => 'fas fa-plus'
            ];
            break;
            
        case 'users_edit':
            $breadcrumbs[] = [
                'title' => 'Quản lý người dùng',
                'url' => '/admin/users',
                'icon' => 'fas fa-users'
            ];
            $breadcrumbs[] = [
                'title' => 'Sửa người dùng',
                'url' => null,
                'icon' => 'fas fa-edit'
            ];
            if (isset($params['username'])) {
                $breadcrumbs[count($breadcrumbs) - 1]['title'] .= ': ' . $params['username'];
            }
            break;
    }
    
    return $breadcrumbs;
}

function renderBreadcrumb($breadcrumbs) {
    echo '<div class="content-header">';
    echo '<div class="container-fluid">';
    echo '<div class="row mb-2">';
    echo '<div class="col-sm-12">';
    echo '<ol class="breadcrumb float-sm-right">';
    
    foreach ($breadcrumbs as $index => $breadcrumb) {
        if ($index === count($breadcrumbs) - 1) {
            echo '<li class="breadcrumb-item active">';
            if (isset($breadcrumb['icon'])) {
                echo '<i class="' . $breadcrumb['icon'] . '"></i> ';
            }
            echo $breadcrumb['title'];
            echo '</li>';
        } else {
            echo '<li class="breadcrumb-item">';
            if ($breadcrumb['url']) {
                echo '<a href="' . $breadcrumb['url'] . '">';
                if (isset($breadcrumb['icon'])) {
                    echo '<i class="' . $breadcrumb['icon'] . '"></i> ';
                }
                echo $breadcrumb['title'];
                echo '</a>';
            } else {
                if (isset($breadcrumb['icon'])) {
                    echo '<i class="' . $breadcrumb['icon'] . '"></i> ';
                }
                echo $breadcrumb['title'];
            }
            echo '</li>';
        }
    }
    
    echo '</ol>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
?>