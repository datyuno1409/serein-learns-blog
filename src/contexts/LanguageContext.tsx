import React, { createContext, useContext, useState, ReactNode } from 'react';

type Language = 'en' | 'vi';

// Define the translation structure
type TranslationKeys = {
  language: string;
  "language.english": string;
  "language.vietnamese": string;
  
  // Navigation
  "nav.home": string;
  "nav.articles": string;
  "nav.about": string;
  "nav.search": string;
  "nav.createArticle": string;
  "nav.login": string;
  "nav.logout": string;
  
  // Hero section
  "hero.title": string;
  "hero.subtitle": string;
  "hero.browseArticles": string;
  "hero.aboutSerein": string;
  
  // Homepage
  "popularTopics": string;
  "featuredArticle": string;
  "latestArticles": string;
  "viewAll": string;
  "newsletterTitle": string;
  "newsletterSubtitle": string;
  "newsletterPlaceholder": string;
  "subscribe": string;
  
  // Login page
  "login.title": string;
  "login.subtitle": string;
  "login.username": string;
  "login.usernamePlaceholder": string;
  "login.password": string;
  "login.passwordPlaceholder": string;
  "login.loginButton": string;
  "login.loggingIn": string;
  "login.error": string;
  "login.success": string;
  "login.fillFields": string;
  "login.invalidCredentials": string;
  "login.welcomeBack": string;
  "login.demoCredentials": string;
  "login.or": string;
  
  // Create Article
  "createArticle.title": string;
  "createArticle.subtitle": string;
  "createArticle.formTitle": string;
  "createArticle.titlePlaceholder": string;
  "createArticle.excerpt": string;
  "createArticle.briefSummary": string;
  "createArticle.excerptPlaceholder": string;
  "createArticle.content": string;
  "createArticle.contentPlaceholder": string;
  "createArticle.coverImage": string;
  "createArticle.coverImagePlaceholder": string;
  "createArticle.category": string;
  "createArticle.selectCategory": string;
  "createArticle.tags": string;
  "createArticle.commaSeparated": string;
  "createArticle.tagsPlaceholder": string;
  "createArticle.publish": string;
  "createArticle.publishing": string;
  "createArticle.cancel": string;
  "createArticle.missingFields": string;
  "createArticle.fillRequiredFields": string;
  "createArticle.success": string;
  "createArticle.successMsg": string;
  
  // Authentication
  "auth.unauthorized": string;
  "auth.loginRequired": string;
};

// English translations
const enTranslations: TranslationKeys = {
  language: "Language",
  "language.english": "English",
  "language.vietnamese": "Vietnamese",
  
  // Navigation
  "nav.home": "Home",
  "nav.articles": "Articles",
  "nav.about": "About",
  "nav.search": "Search",
  "nav.createArticle": "Create Article",
  "nav.login": "Login",
  "nav.logout": "Logout",
  
  // Hero section
  "hero.title": "Learn and Grow with Tech & Security Insights",
  "hero.subtitle": "Discover in-depth articles on cybersecurity, web development, and more to enhance your technical knowledge.",
  "hero.browseArticles": "Browse Articles",
  "hero.aboutSerein": "About Serein",
  
  // Homepage
  "popularTopics": "Popular Topics",
  "featuredArticle": "Featured Article",
  "latestArticles": "Latest Articles",
  "viewAll": "View All",
  "newsletterTitle": "Subscribe to Our Newsletter",
  "newsletterSubtitle": "Get the latest articles and news delivered to your inbox every week.",
  "newsletterPlaceholder": "Your email address",
  "subscribe": "Subscribe",
  
  // Login page
  "login.title": "Login",
  "login.subtitle": "Sign in to your account to create and manage articles",
  "login.username": "Username",
  "login.usernamePlaceholder": "Enter your username",
  "login.password": "Password",
  "login.passwordPlaceholder": "Enter your password",
  "login.loginButton": "Login",
  "login.loggingIn": "Logging in...",
  "login.error": "Login Error",
  "login.success": "Login Successful",
  "login.fillFields": "Please fill in all fields",
  "login.invalidCredentials": "Invalid username or password",
  "login.welcomeBack": "Welcome back!",
  "login.demoCredentials": "Demo Credentials:",
  "login.or": "or",
  
  // Create Article
  "createArticle.title": "Create New Article",
  "createArticle.subtitle": "Share your knowledge and insights with the world",
  "createArticle.formTitle": "Title",
  "createArticle.titlePlaceholder": "Enter article title",
  "createArticle.excerpt": "Excerpt",
  "createArticle.briefSummary": "Brief summary",
  "createArticle.excerptPlaceholder": "Enter a brief summary of your article",
  "createArticle.content": "Content",
  "createArticle.contentPlaceholder": "Write your article content here (HTML formatting supported)",
  "createArticle.coverImage": "Cover Image URL",
  "createArticle.coverImagePlaceholder": "Enter URL for cover image",
  "createArticle.category": "Category",
  "createArticle.selectCategory": "Select a category",
  "createArticle.tags": "Tags",
  "createArticle.commaSeparated": "comma separated",
  "createArticle.tagsPlaceholder": "e.g. JavaScript, Security, React",
  "createArticle.publish": "Publish Article",
  "createArticle.publishing": "Publishing...",
  "createArticle.cancel": "Cancel",
  "createArticle.missingFields": "Missing required fields",
  "createArticle.fillRequiredFields": "Please fill in all required fields.",
  "createArticle.success": "Article created!",
  "createArticle.successMsg": "Your article has been successfully published.",
  
  // Authentication
  "auth.unauthorized": "Unauthorized",
  "auth.loginRequired": "You need to login to access this page.",
};

// Vietnamese translations
const viTranslations: TranslationKeys = {
  language: "Ngôn ngữ",
  "language.english": "Tiếng Anh",
  "language.vietnamese": "Tiếng Việt",
  
  // Navigation
  "nav.home": "Trang chủ",
  "nav.articles": "Bài viết",
  "nav.about": "Giới thiệu",
  "nav.search": "Tìm kiếm",
  "nav.createArticle": "Tạo bài viết",
  "nav.login": "Đăng nhập",
  "nav.logout": "Đăng xuất",
  
  // Hero section
  "hero.title": "Học và phát triển với kiến thức Công nghệ & Bảo mật",
  "hero.subtitle": "Khám phá các bài viết chuyên sâu về bảo mật mạng, phát triển web và nhiều nội dung khác để nâng cao kiến thức kỹ thuật của bạn.",
  "hero.browseArticles": "Xem các bài viết",
  "hero.aboutSerein": "Về Serein",
  
  // Homepage
  "popularTopics": "Chủ đề phổ biến",
  "featuredArticle": "Bài viết nổi bật",
  "latestArticles": "Bài viết mới nhất",
  "viewAll": "Xem tất cả",
  "newsletterTitle": "Đăng ký nhận bản tin",
  "newsletterSubtitle": "Nhận các bài viết và tin tức mới nhất được gửi đến hộp thư của bạn mỗi tuần.",
  "newsletterPlaceholder": "Địa chỉ email của bạn",
  "subscribe": "Đăng ký",
  
  // Login page
  "login.title": "Đăng nhập",
  "login.subtitle": "Đăng nhập vào tài khoản của bạn để tạo và quản lý bài viết",
  "login.username": "Tên đăng nhập",
  "login.usernamePlaceholder": "Nhập tên đăng nhập",
  "login.password": "Mật khẩu",
  "login.passwordPlaceholder": "Nhập mật khẩu",
  "login.loginButton": "Đăng nhập",
  "login.loggingIn": "Đang đăng nhập...",
  "login.error": "Lỗi đăng nhập",
  "login.success": "Đăng nhập thành công",
  "login.fillFields": "Vui lòng điền đầy đủ thông tin",
  "login.invalidCredentials": "Tên đăng nhập hoặc mật khẩu không đúng",
  "login.welcomeBack": "Chào mừng trở lại!",
  "login.demoCredentials": "Thông tin đăng nhập demo:",
  "login.or": "hoặc",
  
  // Create Article
  "createArticle.title": "Tạo bài viết mới",
  "createArticle.subtitle": "Chia sẻ kiến thức và hiểu biết của bạn với thế giới",
  "createArticle.formTitle": "Tiêu đề",
  "createArticle.titlePlaceholder": "Nhập tiêu đề bài viết",
  "createArticle.excerpt": "Tóm tắt",
  "createArticle.briefSummary": "Tóm tắt ngắn",
  "createArticle.excerptPlaceholder": "Nhập tóm tắt ngắn về bài viết của bạn",
  "createArticle.content": "Nội dung",
  "createArticle.contentPlaceholder": "Viết nội dung bài viết của bạn ở đây (hỗ trợ định dạng HTML)",
  "createArticle.coverImage": "URL ảnh bìa",
  "createArticle.coverImagePlaceholder": "Nhập URL cho ảnh bìa",
  "createArticle.category": "Danh mục",
  "createArticle.selectCategory": "Chọn danh mục",
  "createArticle.tags": "Thẻ",
  "createArticle.commaSeparated": "phân cách bằng dấu phẩy",
  "createArticle.tagsPlaceholder": "Ví dụ: JavaScript, Bảo mật, React",
  "createArticle.publish": "Đăng bài viết",
  "createArticle.publishing": "Đang đăng...",
  "createArticle.cancel": "Hủy",
  "createArticle.missingFields": "Thiếu thông tin bắt buộc",
  "createArticle.fillRequiredFields": "Vui lòng điền đầy đủ thông tin bắt buộc.",
  "createArticle.success": "Đã tạo bài viết!",
  "createArticle.successMsg": "Bài viết của bạn đã được đăng thành công.",
  
  // Authentication
  "auth.unauthorized": "Không được phép",
  "auth.loginRequired": "Bạn cần đăng nhập để truy cập trang này.",
};

interface LanguageContextType {
  language: Language;
  setLanguage: (lang: Language) => void;
  t: (key: keyof TranslationKeys) => string;
}

const LanguageContext = createContext<LanguageContextType | undefined>(undefined);

export const useLanguage = () => {
  const context = useContext(LanguageContext);
  if (context === undefined) {
    throw new Error('useLanguage must be used within a LanguageProvider');
  }
  return context;
};

interface LanguageProviderProps {
  children: ReactNode;
}

export const LanguageProvider: React.FC<LanguageProviderProps> = ({ children }) => {
  const [language, setLanguage] = useState<Language>(() => {
    const savedLanguage = localStorage.getItem('language') as Language;
    return savedLanguage || 'en';
  });

  const changeLanguage = (lang: Language) => {
    setLanguage(lang);
    localStorage.setItem('language', lang);
  };

  const t = (key: keyof TranslationKeys): string => {
    const translations = language === 'en' ? enTranslations : viTranslations;
    return translations[key] || key;
  };

  return (
    <LanguageContext.Provider value={{ language, setLanguage: changeLanguage, t }}>
      {children}
    </LanguageContext.Provider>
  );
};
