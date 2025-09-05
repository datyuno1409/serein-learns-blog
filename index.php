<?php
// Session settings must be set before session_start
require_once 'config/config.php';
session_start();
require_once 'config/database.php';
require_once 'helpers/auth_helper.php';

// Initialize database connection
$db = null;
$db_error = null;
try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    $db_error = $e->getMessage();
    error_log('DB connection failed: ' . $db_error);
}

// Simple router
$request = $_SERVER['REQUEST_URI'];
$route = parse_url($request, PHP_URL_PATH);

// Debug logging
file_put_contents('debug.log', "Route debug: $route\n", FILE_APPEND);

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
            $controller = new AdminController();
            $controller->categoriesCreate();
        }
        break;
    
    case '/admin/categories/update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require 'controllers/AdminController.php';
            $controller = new AdminController();
            $controller->categoriesUpdate();
        }
        break;
    
    case '/admin/categories/save':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require 'controllers/AdminController.php';
            $controller = new AdminController();
            $controller->categoriesDelete();
        }
        break;
    
    case '/admin/comments':
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->comments();
        break;
    
    case '/admin/users':
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->users();
        break;
    
    case '/admin/users/add':
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->usersAdd();
        break;
    
    case '/admin/users/edit':
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->usersEdit();
        break;
    
    case '/admin/users/delete':
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->usersDelete();
        break;
    
    case '/admin/settings':
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->settings();
        break;
    
    case '/admin/settings/save':
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->settingsSave();
        break;
    
    case '/admin/users/delete':
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->usersDelete();
        break;
    
    case '/admin/search':
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->search();
        break;
    
    case '/admin/media':
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->media();
        break;
    
    case '/admin/media/upload':
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->mediaUpload();
        break;
    
    case '/admin/media/delete':
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->mediaDelete();
        break;
    
    case '/admin/settings':
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->settings();
        break;
    
    case '/admin/statistics':
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->statistics();
        break;
    
    case '/admin/backup':
        require 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->backup();
        break;
        
    case '/admin/articles':
        require 'controllers/ArticleController.php';
        $controller = new ArticleController();
        $controller->index();
        break;
        
    case '/admin/articles/create':
        require 'controllers/ArticleController.php';
        $controller = new ArticleController();
        $controller->create();
        break;
        
    case '/admin/articles/edit':
        require 'controllers/ArticleController.php';
        $controller = new ArticleController();
        $controller->edit();
        break;
        
    case '/admin/articles/delete':
        require 'controllers/ArticleController.php';
        $controller = new ArticleController();
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
        require_once 'includes/Language.php';
        $page_title = 'My Projects - Learning with Serein';
        $content = 'views/myprojects/index.php';
        require 'views/layouts/frontend.php';
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
        
    case '/logout':
        require 'controllers/AuthController.php';
        $controller = new AuthController($db);
        $controller->logout();
        break;
        
    default:
        http_response_code(404);
        require 'views/404.php';
        break;
}
?>