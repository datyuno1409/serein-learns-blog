<?php
require_once __DIR__ . '/../../includes/Language.php';
?>
<!DOCTYPE html>
<html lang="<?= Language::getCurrentLanguage() ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Learning with Serein</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Global CSS -->
  <link rel="stylesheet" href="/assets/css/global.css">
  <!-- Responsive CSS -->
  <link rel="stylesheet" href="/assets/css/responsive.css">
  
  <!-- Page-specific CSS -->
  <?php if (isset($page_css)): ?>
    <?php foreach ($page_css as $css_file): ?>
      <link rel="stylesheet" href="<?= $css_file ?>">
    <?php endforeach; ?>
  <?php endif; ?>
  
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'serein': {
              50: '#f0fdfa',
              100: '#ccfbf1',
              200: '#99f6e4',
              300: '#5eead4',
              400: '#2dd4bf',
              500: '#14b8a6',
              600: '#0d9488',
              700: '#0f766e',
              800: '#115e59',
              900: '#134e4a'
            }
          },
          fontFamily: {
            'sans': ['Inter', 'system-ui', 'sans-serif'],
            'heading': ['Inter', 'system-ui', 'sans-serif']
          },
          container: {
            center: true,
            padding: '1rem',
            screens: {
              'sm': '640px',
              'md': '768px',
              'lg': '1024px',
              'xl': '1280px',
              '2xl': '1400px'
            }
          }
        }
      }
    }
  </script>
  
  <style>
    .article-content img {
      max-width: 100%;
      height: auto;
    }
    .animate-fade-in {
      animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body class="bg-white font-sans">
<div class="flex flex-col min-h-screen">
  <!-- Header -->
  <header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="container mx-auto py-4">
      <div class="flex items-center justify-between">
        <!-- Logo -->
        <a href="/" class="flex items-center gap-2">
          <span class="text-2xl font-bold font-heading text-serein-600">
            Learning with <span class="text-serein-500">Serein</span>
          </span>
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center space-x-8">
          <a href="/" class="text-base font-medium transition-colors duration-200 hover:text-serein-500 <?= $_SERVER['REQUEST_URI'] === '/' ? 'text-serein-500' : 'text-gray-700' ?>">
            <?= __('nav.home') ?>
          </a>
          <a href="/articles" class="text-base font-medium transition-colors duration-200 hover:text-serein-500 <?= strpos($_SERVER['REQUEST_URI'], '/articles') === 0 ? 'text-serein-500' : 'text-gray-700' ?>">
            <?= __('nav.articles') ?>
          </a>
          <a href="/myprojects" class="text-base font-medium transition-colors duration-200 hover:text-serein-500 <?= $_SERVER['REQUEST_URI'] === '/myprojects' ? 'text-serein-500' : 'text-gray-700' ?>">
            <?= __('projects.title') ?>
          </a>
          <a href="/about" class="text-base font-medium transition-colors duration-200 hover:text-serein-500 <?= $_SERVER['REQUEST_URI'] === '/about' ? 'text-serein-500' : 'text-gray-700' ?>">
            <?= __('nav.about') ?>
          </a>
        </nav>

        <!-- Action Buttons -->
        <div class="flex items-center gap-4">
          <!-- Language Switcher -->
          <div class="relative group">
            <!-- Removed language switcher - Vietnamese only -->
          </div>
          
          <!-- Search Button -->
          <button onclick="toggleSearch()" class="hidden md:flex items-center justify-center w-10 h-10 rounded-md hover:bg-gray-100 transition-colors">
            <i class="fas fa-search text-gray-600"></i>
          </button>
          
          <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])): ?>
            <!-- User Menu -->
            <div class="relative group">
              <button class="flex items-center justify-center w-10 h-10 rounded-md hover:bg-gray-100 transition-colors">
                <i class="fas fa-user text-gray-600"></i>
              </button>
              <div class="absolute top-full right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                  <a href="/admin/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-serein-500 transition-colors">
                    <i class="fas fa-tachometer-alt mr-2"></i> <?= __('nav.dashboard') ?>
                  </a>
                  <hr class="my-1">
                <?php endif; ?>
                <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-serein-500 transition-colors">
                  <i class="fas fa-user-circle mr-2"></i> <?= __('nav.profile') ?>
                </a>
                <hr class="my-1">
                <a href="/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-serein-500 transition-colors">
                  <i class="fas fa-sign-out-alt mr-2"></i> <?= __('nav.logout') ?>
                </a>
              </div>
            </div>
          <?php else: ?>
            <a href="/login" class="hidden md:inline-flex px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
              <?= __('nav.login') ?>
            </a>
          <?php endif; ?>
          
          <!-- Mobile Menu Button -->
          <button onclick="toggleMobileMenu()" class="md:hidden flex items-center justify-center w-10 h-10 rounded-md hover:bg-gray-100 transition-colors">
            <i id="mobile-menu-icon" class="fas fa-bars text-gray-600"></i>
          </button>
        </div>
      </div>

      <!-- Mobile Navigation -->
      <div id="mobile-menu" class="md:hidden mt-4 pb-2 hidden animate-fade-in">
        <nav class="flex flex-col space-y-4">
          <a href="/" class="text-base font-medium transition-colors duration-200 px-2 py-1 rounded-md <?= $_SERVER['REQUEST_URI'] === '/' ? 'bg-serein-50 text-serein-500' : 'text-gray-700 hover:bg-gray-50' ?>">
            <?= __('nav.home') ?>
          </a>
          <a href="/articles" class="text-base font-medium transition-colors duration-200 px-2 py-1 rounded-md <?= strpos($_SERVER['REQUEST_URI'], '/articles') === 0 ? 'bg-serein-50 text-serein-500' : 'text-gray-700 hover:bg-gray-50' ?>">
            <?= __('nav.articles') ?>
          </a>
          <a href="/myprojects" class="text-base font-medium transition-colors duration-200 px-2 py-1 rounded-md <?= $_SERVER['REQUEST_URI'] === '/myprojects' ? 'bg-serein-50 text-serein-500' : 'text-gray-700 hover:bg-gray-50' ?>">
            <?= __('projects.title') ?>
          </a>
          <a href="/about" class="text-base font-medium transition-colors duration-200 px-2 py-1 rounded-md <?= $_SERVER['REQUEST_URI'] === '/about' ? 'bg-serein-50 text-serein-500' : 'text-gray-700 hover:bg-gray-50' ?>">
            <?= __('nav.about') ?>
          </a>
          <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="/login" class="text-base font-medium transition-colors duration-200 px-2 py-1 rounded-md text-gray-700 hover:bg-gray-50">
              <?= __('nav.login') ?>
            </a>
          <?php endif; ?>
        </nav>
      </div>

      <!-- Search Bar (Hidden by default) -->
      <div id="search-bar" class="mt-4 hidden">
        <form action="/search" method="get" class="max-w-md mx-auto">
          <div class="relative">
            <input type="search" name="q" placeholder="<?= __('home.search_placeholder') ?>" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" class="w-full px-4 py-3 pr-12 text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-serein-500 focus:border-transparent">
            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-serein-500 transition-colors">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex-grow">
    <div class="container mx-auto">
      <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md mt-4 relative">
          <button type="button" onclick="this.parentElement.style.display='none'" class="absolute top-0 right-0 mt-3 mr-3 text-green-600 hover:text-green-800">
            <i class="fas fa-times"></i>
          </button>
          <?= $_SESSION['success'] ?>
          <?php unset($_SESSION['success']); ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md mt-4 relative">
          <button type="button" onclick="this.parentElement.style.display='none'" class="absolute top-0 right-0 mt-3 mr-3 text-red-600 hover:text-red-800">
            <i class="fas fa-times"></i>
          </button>
          <?= $_SESSION['error'] ?>
          <?php unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <?php 
      // Kiểm tra biến $content tồn tại trước khi sử dụng
      if (isset($content)) {
          require $content;
      } else {
          echo '<div class="py-8 text-center">Nội dung không được tìm thấy</div>';
      }
      ?>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-gray-900 text-white py-12">
    <div class="container mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- About Section -->
        <div class="col-span-1 md:col-span-2">
          <h3 class="text-xl font-bold mb-4">Learning with Serein</h3>
          <p class="text-gray-300 mb-4">
            A platform dedicated to sharing knowledge about cybersecurity, web development, and technology. 
            Join our community of learners and stay updated with the latest trends.
          </p>
          <div class="flex space-x-4">
            <a href="#" class="text-gray-400 hover:text-white transition-colors">
              <i class="fab fa-twitter text-xl"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-white transition-colors">
              <i class="fab fa-github text-xl"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-white transition-colors">
              <i class="fab fa-linkedin text-xl"></i>
            </a>
          </div>
        </div>
        
        <!-- Quick Links -->
        <div>
          <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
          <ul class="space-y-2">
            <li><a href="/" class="text-gray-300 hover:text-white transition-colors">Home</a></li>
            <li><a href="/articles" class="text-gray-300 hover:text-white transition-colors">Articles</a></li>
            <li><a href="/about" class="text-gray-300 hover:text-white transition-colors">About</a></li>
            <li><a href="/contact" class="text-gray-300 hover:text-white transition-colors">Contact</a></li>
          </ul>
        </div>
        
        <!-- Categories -->
        <div>
          <h4 class="text-lg font-semibold mb-4">Categories</h4>
          <ul class="space-y-2">
            <?php if (isset($categories) && is_array($categories)): ?>
            <?php foreach (array_slice($categories, 0, 5) as $category): ?>
              <li>
                <a href="/category/<?= $category['id'] ?>" class="text-gray-300 hover:text-white transition-colors">
                  <?= htmlspecialchars($category['name']) ?>
                </a>
              </li>
            <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        </div>
      </div>
      
      <hr class="border-gray-700 my-8">
      
      <div class="flex flex-col md:flex-row justify-between items-center">
        <p class="text-gray-400 text-sm">
          &copy; <?= date('Y') ?> Learning with Serein. All rights reserved.
        </p>
        <p class="text-gray-400 text-sm mt-2 md:mt-0">
          Built with ❤️ for the community
        </p>
      </div>
    </div>
  </footer>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>

<!-- Page-specific JS -->
<?php if (isset($page_js)): ?>
  <?php foreach ($page_js as $js_file): ?>
    <script src="<?= $js_file ?>"></script>
  <?php endforeach; ?>
<?php endif; ?>
</body>
</html>