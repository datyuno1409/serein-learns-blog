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
  "nav.manageArticles": string;
  "nav.login": string;
  "nav.logout": string;
  "nav.myProjects": string;
  
  // Admin
  "admin.dashboard": string;
  "admin.articles": string;
  "admin.projects": string;
  "admin.settings": string;
  
  // Articles
  "articles.edit": string;
  "articles.delete": string;
  "articles.deleteConfirmTitle": string;
  "articles.deleteConfirmMessage": string;
  "articles.deleteSuccess": string;
  "articles.deleteSuccessMessage": string;
  "articles.deleteError": string;
  "articles.deleteErrorMessage": string;
  "articles.minuteRead": string;
  "articles.notFound": string;
  "articles.adjustSearch": string;
  "articles.minRead": string;
  "articles.share": string;
  "articles.copyLink": string;
  "articles.aboutAuthor": string;
  "articles.techWriter": string;
  "articles.related": string;
  "articles.view": string;

  // Common
  "common.cancel": string;
  "common.delete": string;
  "common.actions": string;
  "common.verify": string;
  "common.viewAll": string;
  
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

  // NotFound page
  "notFound.message": string;

  // About section
  "about.name": string;
  "about.title": string;
  "about.experience": string;
  "about.description": string;
  "about.skills": string;
  "about.certifications": string;
  "about.education": string;
  "about.achievements": string;
  "about.contact": string;
  "about.downloadCV": string;
  "about.phone": string;
  "about.email": string;
  "about.address": string;
  "about.addressValue": string;
  "about.aboutMe": string;
  "about.aboutMeContent": string;
  "about.workHistory": string;
  "about.company": string;
  "about.position": string;
  "about.workPeriod": string;
  "about.responsibilities": string;
  "about.certificationLink": string;

  // Basic Info
  "basicInfo.title": string;
  "basicInfo.birthday": string;
  "basicInfo.birthdayValue": string;
  "basicInfo.nationality": string;
  "basicInfo.nationalityValue": string;
  "basicInfo.maritalStatus": string;
  "basicInfo.maritalStatusValue": string;
  "basicInfo.gender": string;
  "basicInfo.genderValue": string;

  // Education
  "education.university": string;
  "education.degree": string;
  "education.period": string;
  "education.achievements": string[];

  // MyProjects section
  "myProjects.title": string;
  "myProjects.subtitle": string;
  "myProjects.create": string;
  "myProjects.edit": string;
  "myProjects.delete": string;
  "myProjects.deleteConfirmTitle": string;
  "myProjects.deleteConfirmMessage": string;
  "myProjects.deleteSuccess": string;
  "myProjects.deleteError": string;
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
  "nav.manageArticles": "Manage Articles",
  "nav.login": "Login",
  "nav.logout": "Logout",
  "nav.myProjects": "My Projects",
  
  // Admin
  "admin.dashboard": "Dashboard",
  "admin.articles": "Articles", 
  "admin.projects": "Projects",
  "admin.settings": "Settings",
  
  // Articles
  "articles.edit": "Edit",
  "articles.delete": "Delete",
  "articles.deleteConfirmTitle": "Delete Confirmation",
  "articles.deleteConfirmMessage": "Are you sure you want to delete this article?",
  "articles.deleteSuccess": "Delete Successful",
  "articles.deleteSuccessMessage": "The article has been successfully deleted.",
  "articles.deleteError": "Delete Error",
  "articles.deleteErrorMessage": "An error occurred while deleting the article.",
  "articles.minuteRead": "minute read",
  "articles.notFound": "No articles found",
  "articles.adjustSearch": "Try adjusting your search or filter criteria.",
  "articles.minRead": "min read",
  "articles.share": "Share this article",
  "articles.copyLink": "Copy Link",
  "articles.aboutAuthor": "About the Author",
  "articles.techWriter": "Technology Writer",
  "articles.related": "Related Articles",
  "articles.view": "View",

  // Common
  "common.cancel": "Cancel",
  "common.delete": "Delete",
  "common.actions": "Actions",
  "common.verify": "Verify",
  "common.viewAll": "View All Articles",
  
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
  
  // Create Article
  "createArticle.title": "Create New Article",
  "createArticle.subtitle": "Share your knowledge and insights with the world",
  "createArticle.formTitle": "Title",
  "createArticle.titlePlaceholder": "Enter article title",
  "createArticle.excerpt": "Excerpt",
  "createArticle.briefSummary": "Brief summary",
  "createArticle.excerptPlaceholder": "Enter a brief summary of your article",
  "createArticle.content": "Content",
  "createArticle.contentPlaceholder": "Write your article content here",
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

  // NotFound page
  "notFound.message": "Oops! Page not found",

  // About section
  "about.name": "NGUYEN THANH DAT",
  "about.title": "TECHNICAL SUPPORT ENGINEER",
  "about.experience": "1 YEAR OF EXPERIENCE",
  "about.description": "Learn more about my background, skills and experience",
  "about.skills": "Skills",
  "about.certifications": "Certifications",
  "about.education": "Education",
  "about.achievements": "Key Achievements",
  "about.contact": "CONTACT",
  "about.downloadCV": "Download CV",
  "about.phone": "Phone",
  "about.email": "Email",
  "about.address": "Address",
  "about.addressValue": "Truong Tho Ward, Thu Duc City, Ho Chi Minh City, Vietnam",
  "about.aboutMe": "ABOUT ME",
  "about.aboutMeContent": "I emerged from the Information Security program at FPT University, equipped with some experience in penetration testing and security project management.",
  "about.workHistory": "WORK HISTORY",
  "about.company": "Shilla Monogram",
  "about.position": "IT Office Trainee",
  "about.workPeriod": "08/2023 - 10/2024 (1 year 2 month)",
  "about.responsibilities": "Perform basic network system monitoring, configuration and management tasks.",
  "about.certificationLink": "Verify",

  // Basic Info
  "basicInfo.title": "Basic Information",
  "basicInfo.birthday": "Birthday",
  "basicInfo.birthdayValue": "14/09/2002",
  "basicInfo.nationality": "Nationality", 
  "basicInfo.nationalityValue": "Vietnamese",
  "basicInfo.maritalStatus": "Marital Status",
  "basicInfo.maritalStatusValue": "Single",
  "basicInfo.gender": "Gender",
  "basicInfo.genderValue": "Male",

  // Education
  "education.university": "FPT University Da Nang",
  "education.degree": "Bachelors - Information Assurance", 
  "education.period": "10/2020 - 12/2024 (4 years 2 months)",
  "education.achievements": [
    "Served as a member of the Security Research Club from 09/2022 to 12/2023.",
    "Led the club in participating in competitions such as Hackathon, Secathon, Bootcamp, and Secathon Asean, among others.",
    "Recognized as an Outstanding Student for one year.",
    "Contributed to organizing security-related events, helping the club earn the Outstanding Club Award.",
    "Achieved Runner-up position for the Graduation Project with the topic: 'Development of UniSAST: A Web-based Platform Integrating Open-source SAST Tools for Automated Code Security Analysis and DevSecOps Support in SMEs.'"
  ],

  // MyProjects section
  "myProjects.title": "My Projects",
  "myProjects.subtitle": "A collection of my personal projects and contributions to open-source software",
  "myProjects.create": "Create Project",
  "myProjects.edit": "Edit Project",
  "myProjects.delete": "Delete Project",
  "myProjects.deleteConfirmTitle": "Delete Project?",
  "myProjects.deleteConfirmMessage": "This action cannot be undone. This will permanently delete the project.",
  "myProjects.deleteSuccess": "Project deleted successfully",
  "myProjects.deleteError": "Failed to delete project",
};

// Vietnamese translations
const viTranslations: TranslationKeys = {
  language: "Ngôn ngữ",
  "language.english": "English",
  "language.vietnamese": "Vietnamese",
  
  // Navigation
  "nav.home": "Trang chủ",
  "nav.articles": "Bài viết",
  "nav.about": "Giới thiệu",
  "nav.search": "Tìm kiếm",
  "nav.createArticle": "Tạo bài viết",
  "nav.manageArticles": "Quản lý bài viết",
  "nav.login": "Đăng nhập",
  "nav.logout": "Đăng xuất",
  "nav.myProjects": "Dự án của tôi",
  
  // Admin
  "admin.dashboard": "Bảng điều khiển",
  "admin.articles": "Bài viết",
  "admin.projects": "Dự án", 
  "admin.settings": "Cài đặt",
  
  // Articles
  "articles.edit": "Sửa",
  "articles.delete": "Xóa",
  "articles.deleteConfirmTitle": "Xác nhận xóa",
  "articles.deleteConfirmMessage": "Bạn có chắc chắn muốn xóa bài viết này?",
  "articles.deleteSuccess": "Xóa thành công",
  "articles.deleteSuccessMessage": "Bài viết đã được xóa thành công.",
  "articles.deleteError": "Lỗi xóa",
  "articles.deleteErrorMessage": "Đã xảy ra lỗi khi xóa bài viết.",
  "articles.minuteRead": "phút đọc",
  "articles.notFound": "Không tìm thấy bài viết nào",
  "articles.adjustSearch": "Hãy thử điều chỉnh tiêu chí tìm kiếm hoặc lọc của bạn.",
  "articles.minRead": "phút đọc",
  "articles.share": "Chia sẻ bài viết này",
  "articles.copyLink": "Sao chép liên kết",
  "articles.aboutAuthor": "Về tác giả",
  "articles.techWriter": "Người viết công nghệ",
  "articles.related": "Bài viết liên quan",
  "articles.view": "Lượt xem",

  // Common
  "common.cancel": "Hủy",
  "common.delete": "Xóa",
  "common.actions": "Hành động",
  "common.verify": "Xác thực",
  "common.viewAll": "Xem tất cả bài viết",
  
  // Login page
  "login.title": "Đăng nhập",
  "login.subtitle": "Đăng nhập vào tài khoản của bạn để tạo và quản lý bài viết",
  "login.username": "Tên người dùng",
  "login.usernamePlaceholder": "Nhập tên người dùng",
  "login.password": "Mật khẩu",
  "login.passwordPlaceholder": "Nhập mật khẩu",
  "login.loginButton": "Đăng nhập",
  "login.loggingIn": "Đang đăng nhập...",
  "login.error": "Lỗi đăng nhập",
  "login.success": "Đăng nhập thành công",
  "login.fillFields": "Vui lòng điền đầy đủ thông tin",
  "login.invalidCredentials": "Tên người dùng hoặc mật khẩu không đúng",
  "login.welcomeBack": "Chào mừng trở lại!",
  "login.demoCredentials": "Thông tin demo:",
  "login.or": "hoặc",
  
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
  "newsletterTitle": "Đăng ký nhận tin",
  "newsletterSubtitle": "Nhận các bài viết và tin tức mới nhất qua email hàng tuần.",
  "newsletterPlaceholder": "Địa chỉ email của bạn",
  "subscribe": "Đăng ký",
  
  // Create Article
  "createArticle.title": "Tạo bài viết mới",
  "createArticle.subtitle": "Chia sẻ kiến thức và hiểu biết của bạn với thế giới",
  "createArticle.formTitle": "Tiêu đề",
  "createArticle.titlePlaceholder": "Nhập tiêu đề bài viết",
  "createArticle.excerpt": "Tóm tắt",
  "createArticle.briefSummary": "Tóm tắt ngắn",
  "createArticle.excerptPlaceholder": "Nhập tóm tắt ngắn về bài viết của bạn",
  "createArticle.content": "Nội dung",
  "createArticle.contentPlaceholder": "Viết nội dung bài viết của bạn",
  "createArticle.coverImage": "URL ảnh bìa",
  "createArticle.coverImagePlaceholder": "Nhập URL cho ảnh bìa",
  "createArticle.category": "Danh mục",
  "createArticle.selectCategory": "Chọn danh mục",
  "createArticle.tags": "Thẻ",
  "createArticle.commaSeparated": "phân cách bằng dấu phẩy",
  "createArticle.tagsPlaceholder": "Ví dụ: JavaScript, Bảo mật, React",
  "createArticle.publish": "Xuất bản bài viết",
  "createArticle.publishing": "Đang xuất bản...",
  "createArticle.cancel": "Hủy",
  "createArticle.missingFields": "Thiếu thông tin bắt buộc",
  "createArticle.fillRequiredFields": "Vui lòng điền đầy đủ thông tin bắt buộc.",
  "createArticle.success": "Đã tạo bài viết!",
  "createArticle.successMsg": "Bài viết của bạn đã được xuất bản thành công.",
  
  // Authentication
  "auth.unauthorized": "Không được phép",
  "auth.loginRequired": "Bạn cần đăng nhập để truy cập trang này.",

  // NotFound page
  "notFound.message": "Ôi! Không tìm thấy trang",

  // About section
  "about.name": "NGUYỄN THÀNH ĐẠT",
  "about.title": "KỸ SƯ HỖ TRỢ KỸ THUẬT",
  "about.experience": "1 NĂM KINH NGHIỆM",
  "about.description": "Tìm hiểu thêm về nền tảng, kỹ năng và kinh nghiệm của tôi",
  "about.skills": "Kỹ Năng",
  "about.certifications": "Chứng Chỉ",
  "about.education": "Học Vấn",
  "about.achievements": "Thành Tích Nổi Bật",
  "about.contact": "LIÊN HỆ",
  "about.downloadCV": "Tải CV",
  "about.phone": "Số điện thoại",
  "about.email": "Email",
  "about.address": "Địa chỉ",
  "about.addressValue": "Phường Trường Thọ, Thành phố Thủ Đức, Thành phố Hồ Chí Minh, Việt Nam",

  // MyProjects section
  "myProjects.title": "Dự Án Của Tôi",
  "myProjects.subtitle": "Bộ sưu tập các dự án cá nhân và đóng góp cho phần mềm mã nguồn mở",
  "myProjects.create": "Tạo Dự Án",
  "myProjects.edit": "Chỉnh Sửa",
  "myProjects.delete": "Xóa",
  "myProjects.deleteConfirmTitle": "Xóa Dự Án?",
  "myProjects.deleteConfirmMessage": "Hành động này không thể hoàn tác. Dự án sẽ bị xóa vĩnh viễn.",
  "myProjects.deleteSuccess": "Xóa dự án thành công",
  "myProjects.deleteError": "Xóa dự án thất bại",
};

interface LanguageContextType {
  language: Language;
  setLanguage: (lang: Language) => void;
  t: (key: keyof TranslationKeys) => string;
  translations: TranslationKeys;
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

  const handleLanguageChange = (lang: Language) => {
    setLanguage(lang);
    localStorage.setItem('language', lang);
  };

  const translations = language === 'vi' ? viTranslations : enTranslations;

  const t = (key: keyof TranslationKeys): string => {
    const translation = translations[key];
    return typeof translation === 'string' ? translation : key as string;
  };

  return (
    <LanguageContext.Provider value={{ 
      language, 
      setLanguage: handleLanguageChange, 
      t,
      translations 
    }}>
      {children}
    </LanguageContext.Provider>
  );
};