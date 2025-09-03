<?php

class Language {
    private static $currentLanguage = 'vi';
    private static $translations = [];
    private static $fallbackLanguage = 'vi';
    
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
        return self::$currentLanguage;
    }
    
    public static function setLanguage($language) {
        if (in_array($language, ['vi', 'en'])) {
            self::$currentLanguage = $language;
            $_SESSION['language'] = $language;
            self::init($language);
        }
    }
    
    public static function getAvailableLanguages() {
        return [
            'vi' => 'Tiếng Việt',
            'en' => 'English'
        ];
    }
    
    public static function detectLanguage() {
        if (isset($_SESSION['language'])) {
            return $_SESSION['language'];
        }
        
        if (isset($_GET['lang']) && in_array($_GET['lang'], ['vi', 'en'])) {
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