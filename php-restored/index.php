<?php
// Set UTF-8 encoding
header('Content-Type: text/html; charset=utf-8');

// Session settings must be set before session_start
require_once 'config/config.php';
session_start();
require_once 'config/database.php';
require_once 'helpers/auth_helper.php';

// Initialize database connection
$db = null;
$db_error = null;
try {
    $database = new Database();
    $db = $database->connect();
} catch (PDOException $e) {
    $db_error = $e->getMessage();
    error_log('DB connection failed: ' . $db_error);
    
    // Show user-friendly error page if database is not available
    if (!$db) {
        http_response_code(503);
        echo '<h1>Service Temporarily Unavailable</h1>';
        echo '<p>We are experiencing technical difficulties. Please try again later.</p>';
        exit;
    }
}

// Simple router
$request = $_SERVER['REQUEST_URI'];
$route = parse_url($request, PHP_URL_PATH);

// Controller cache to avoid multiple requires
$loaded_controllers = [];

// Debug logging
if (!file_exists('logs')) {
    mkdir('logs', 0755, true);
}
file_put_contents('logs/debug.log', "Route debug: $route\n", FILE_APPEND);

// Routes configuration
switch ($route) {
    case '/':
    case '':
        require 'controllers/HomeController.php';
        $controller = new HomeController($db);
        $controller->index();
        break;
        
    case '/admin':
        header('Location: /admin/dashboard');
        exit;
        
    case '/admin/dashboard':
        error_log("Admin dashboard route accessed");
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->dashboard();
        break;
        
    case '/admin/posts':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->posts();
        break;
    
    case '/admin/posts_list':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->posts();
        break;
    
    case '/admin/posts/add':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->postsAdd();
        break;
    
    case '/admin/posts/edit':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->postsEdit();
        break;
    
    case '/admin/posts/delete':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->postsDelete();
        break;
    
    case '/admin/categories':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->categories();
        break;
    
    case '/admin/categories/add':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->categoriesAdd();
        break;
    
    case '/admin/categories/edit':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->categoriesEdit();
        break;
    
    case '/admin/categories/delete':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->categoriesDelete();
        break;
    
    case '/admin/categories/create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require 'controllers/AdminController.php';
            $controller = new AdminController($db);
            $controller->categoriesCreate();
        }
        break;
    
    case '/admin/categories/update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require 'controllers/AdminController.php';
            $controller = new AdminController($db);
            $controller->categoriesUpdate();
        }
        break;
    
    case '/admin/categories/save':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require 'controllers/AdminController.php';
            $controller = new AdminController($db);
            $controller->categoriesSave();
        }
        break;
    
    case '/admin/comments':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->comments();
        break;
    
    case '/admin/comments/delete':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->commentsDelete();
        break;
    
    case '/admin/users':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->users();
        break;
    
    case '/admin/users/add':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->usersAdd();
        break;
    
    case '/admin/users/edit':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->usersEdit();
        break;
    
    case '/admin/users/delete':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->usersDelete();
        break;
    
    case '/admin/settings':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->settings();
        break;
    
    case '/admin/settings/save':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->settingsSave();
        break;
    
    case '/admin/search':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->search();
        break;
    
    case '/admin/media':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->media();
        break;
    
    case '/admin/media/upload':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->mediaUpload();
        break;
    
    case '/admin/media/delete':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->mediaDelete();
        break;
    
    case '/admin/statistics':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->statistics();
        break;
    
    case '/admin/analytics':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->analytics();
        break;
        
    case '/admin/backup':
        require 'controllers/AdminController.php';
        $controller = new AdminController($db);
        $controller->backup();
        break;
        
    case '/admin/articles':
        require 'controllers/ArticleController.php';
        $controller = new ArticleController($db);
        $controller->index();
        break;
        
    case '/admin/articles/create':
        require 'controllers/ArticleController.php';
        $controller = new ArticleController($db);
        $controller->create();
        break;
        
    case '/admin/articles/edit':
        require 'controllers/ArticleController.php';
        $controller = new ArticleController($db);
        $controller->edit();
        break;
        
    case '/admin/articles/delete':
        require 'controllers/ArticleController.php';
        $controller = new ArticleController($db);
        $controller->delete();
        break;
        
    case '/articles':
        require 'controllers/ArticlesController.php';
        $controller = new ArticlesController($db);
        $controller->index();
        break;
        
    case '/articles/create':
        require 'controllers/ArticlesController.php';
        $controller = new ArticlesController($db);
        $controller->create();
        break;
        
    case '/articles/edit':
        require 'controllers/ArticlesController.php';
        $controller = new ArticlesController($db);
        $controller->edit();
        break;
        
    case '/articles/delete':
        require 'controllers/ArticlesController.php';
        $controller = new ArticlesController($db);
        $controller->delete();
        break;
        
    case '/articles/my':
        require 'controllers/ArticlesController.php';
        $controller = new ArticlesController($db);
        $controller->myArticles();
        break;
        
    case '/about':
        require_once 'includes/Language.php';
        $page_title = 'About - Learning with Serein';
        $content = 'views/about/index.php';
        require 'views/layouts/frontend.php';
        break;
        
    case '/profile':
        require 'controllers/ProfileController.php';
        $controller = new ProfileController($db);
        $controller->index();
        break;
        
    case '/myprojects':
        require 'controllers/ProjectsController.php';
        $controller = new ProjectsController($db);
        $controller->index();
        break;
        
    case '/projects/create':
        require 'controllers/ProjectsController.php';
        $controller = new ProjectsController($db);
        $controller->create();
        break;
        
    case (preg_match('/^\/projects\/edit\/(\d+)$/', $route, $matches) ? true : false):
        require 'controllers/ProjectsController.php';
        $controller = new ProjectsController($db);
        $controller->edit($matches[1]);
        break;
        
    case (preg_match('/^\/projects\/delete\/(\d+)$/', $route, $matches) ? true : false):
        require 'controllers/ProjectsController.php';
        $controller = new ProjectsController($db);
        $controller->delete($matches[1]);
        break;
        
    case (preg_match('/^\/project\/(\d+)$/', $route, $matches) ? true : false):
        require 'controllers/ProjectsController.php';
        $controller = new ProjectsController($db);
        $controller->show($matches[1]);
        break;
        
    case '/tags':
        require 'controllers/TagController.php';
        $controller = new TagController($db);
        $controller->index();
        break;
        
    case '/login':
        require 'controllers/AuthController.php';
        $controller = new AuthController($db);
        $controller->login();
        break;
        
    case '/admin/login':
        require 'controllers/AuthController.php';
        $controller = new AuthController($db);
        $controller->adminLogin();
        break;
        
    case '/logout':
        require 'controllers/AuthController.php';
        $controller = new AuthController($db);
        $controller->logout();
        break;
        
    default:
        // Handle dynamic routes like /article/{id} and /project/{id}
        if (preg_match('/^\/article\/(\d+)$/', $route, $matches)) {
            require 'controllers/ArticlesController.php';
            $controller = new ArticlesController($db);
            $controller->view($matches[1]);
        } elseif (preg_match('/^\/project\/(\d+)$/', $route, $matches)) {
            require 'controllers/ProjectsController.php';
            $controller = new ProjectsController($db);
            $controller->detail($matches[1]);
        } else {
            http_response_code(404);
            require 'views/404.php';
        }
        break;
}
?>