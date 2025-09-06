<?php

class Language {
    private static $currentLanguage = 'vi';
    private static $translations = [];
    private static $fallbackLanguage = 'vi';
    private static $defaultLanguage = 'vi';
    
    public static function init($language = 'vi') {
        self::$currentLanguage = $language;
        self::loadTranslations($language);
        
        if ($language !== self::$fallbackLanguage) {
            self::loadTranslations(self::$fallbackLanguage, true);
        }
    }
    
    private static function loadTranslations($language, $fallback = false) {
        $filePath = __DIR__ . "/../lang/{$language}.php";
        
        if (file_exists($filePath)) {
            $translations = include $filePath;
            
            if ($fallback) {
                // Use array_merge instead of array_merge_recursive to avoid nested arrays
                self::$translations = array_merge($translations, self::$translations);
            } else {
                self::$translations = $translations;
            }
        }
    }
    
    public static function get($key, $params = []) {
        $keys = explode('.', $key);
        $value = self::$translations;
        
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $key;
            }
        }
        
        // Ensure we always return a string
        if (!is_string($value)) {
            return $key; // Return the key if value is not a string
        }
        
        if (!empty($params)) {
            foreach ($params as $param => $replacement) {
                $value = str_replace(":$param", $replacement, $value);
            }
        }
        
        return $value;
    }
    
    public static function getCurrentLanguage() {
        return self::$defaultLanguage;
    }
    
    public static function setLanguage($lang) {
        // Only Vietnamese is supported
        self::$currentLanguage = self::$defaultLanguage;
    }
    
    public static function getAvailableLanguages() {
        return ['vi' => 'Tiếng Việt'];
    }
    
    public static function detectLanguage() {
        if (isset($_SESSION['language'])) {
            return $_SESSION['language'];
        }
        
        if (isset($_GET['lang']) && array_key_exists($_GET['lang'], self::$availableLanguages)) {
            return $_GET['lang'];
        }
        
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        
        if (strpos($acceptLanguage, 'vi') !== false) {
            return 'vi';
        }
        
        if (strpos($acceptLanguage, 'en') !== false) {
            return 'en';
        }
        
        return self::$fallbackLanguage;
    }
    
    public static function translate($key, $lang = null) {
        $lang = $lang ?: self::getCurrentLanguage();
        
        // Simple translation array - in a real app, this would come from a database or translation files
        $translations = [
            'vi' => [
                'home' => 'Trang chủ',
                'articles' => 'Bài viết',
                'projects' => 'Dự án',
                'about' => 'Giới thiệu',
                'contact' => 'Liên hệ',
                'search' => 'Tìm kiếm',
                'login' => 'Đăng nhập',
                'logout' => 'Đăng xuất',
                'language' => 'Ngôn ngữ'
            ],
            'en' => [
                'home' => 'Home',
                'articles' => 'Articles', 
                'projects' => 'Projects',
                'about' => 'About',
                'contact' => 'Contact',
                'search' => 'Search',
                'login' => 'Login',
                'logout' => 'Logout',
                'language' => 'Language'
            ]
        ];
        
        return $translations[$lang][$key] ?? $key;
    }
}

function __($key, $params = []) {
    return Language::get($key, $params);
}

function lang($key, $params = []) {
    return Language::get($key, $params);
}

function getCurrentLanguage() {
    return Language::getCurrentLanguage();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    Language::setLanguage($_GET['lang']);
} else {
    $detectedLanguage = Language::detectLanguage();
    Language::init($detectedLanguage);
}