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
  
  // About/CV section
  "about.name": string;
  "about.title": string;
  "about.experience": string;
  "about.aboutMe": string;
  "about.aboutMeContent": string;
  "about.contactMe": string;
  "about.phone": string;
  "about.email": string;
  "about.address": string;
  "about.basicInfo": string;
  "about.birthday": string;
  "about.nationality": string;
  "about.maritalStatus": string;
  "about.gender": string;
  "about.skills": string;
  "about.education": string;
  "about.university": string;
  "about.degree": string;
  "about.period": string;
  "about.achievements": string[];
  "about.workHistory": string;
  "about.company": string;
  "about.position": string;
  "about.workPeriod": string;
  "about.responsibilities": string;
  "about.certifications": string;
  "about.certificationYear": string;
  "about.certificationProvider": string;
  
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

  // Articles page
  "articles.notFound": string;
  "articles.adjustSearch": string;
  "articles.minRead": string;
  "articles.share": string;
  "articles.copyLink": string;
  "articles.aboutAuthor": string;
  "articles.techWriter": string;
  "articles.related": string;

  // NotFound page
  "notFound.message": string;
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
  
  // About/CV section
  "about.name": "NGUYEN THANH DAT",
  "about.title": "TECHNICAL SUPPORT ENGINEER",
  "about.experience": "1 YEARS EXPERIENCE",
  "about.aboutMe": "ABOUT ME",
  "about.aboutMeContent": "I emerged from the Information Security program at FPT University, equipped with some experience in penetration testing and security project management. What drives me every day is the desire to learn and become a Pentest expert, helping businesses stand strong against all security challenges.",
  "about.contactMe": "CONTACT ME",
  "about.phone": "Phone",
  "about.email": "Email",
  "about.address": "Address",
  "about.basicInfo": "BASIC INFORMATION",
  "about.birthday": "Birthday",
  "about.nationality": "Nationality",
  "about.maritalStatus": "Marital status",
  "about.gender": "Gender",
  "about.skills": "SKILLS",
  "about.education": "EDUCATION",
  "about.university": "FPT University Da Nang",
  "about.degree": "Bachelors - Information Assurance",
  "about.period": "10/2020 - 12/2024 (4 years 2 months)",
  "about.achievements": [
    "Served as a member of the Security Research Club from 09/2022 to 12/2023.",
    "Led the club in participating in competitions such as Hackathon, Secathon, Bootcamp, and Secathon Asean, among others.",
    "Recognized as an Outstanding Student for one year.",
    "Contributed to organizing security-related events, helping the club earn the Outstanding Club Award.",
    "Achieved Runner-up position for the Graduation Project with the topic: 'Development of UniSAST: A Web-based Platform Integrating Open-source SAST Tools for Automated Code Security Analysis and DevSecOps Support in SMEs.'"
  ],
  "about.workHistory": "WORK HISTORY",
  "about.company": "Shilla Monogram",
  "about.position": "IT Office Trainee",
  "about.workPeriod": "08/2023 - 10/2024 (1 year 2 month)",
  "about.responsibilities": "Perform basic network system monitoring, configuration and management tasks.",
  "about.certifications": "CERTIFICATIONS",
  "about.certificationYear": "2023",
  "about.certificationProvider": "Coursera",
  
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

  // Articles page
  "articles.notFound": "No articles found",
  "articles.adjustSearch": "Try adjusting your search or filter criteria.",
  "articles.minRead": "min read",
  "articles.share": "Share this article",
  "articles.copyLink": "Copy Link",
  "articles.aboutAuthor": "About the Author",
  "articles.techWriter": "Technology Writer",
  "articles.related": "Related Articles",

  // NotFound page
  "notFound.message": "Oops! Page not found",
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
  
  // About/CV section
  "about.name": "NGUYỄN THÀNH ĐẠT",
  "about.title": "KỸ SƯ HỖ TRỢ KỸ THUẬT",
  "about.experience": "1 NĂM KINH NGHIỆM",
  "about.aboutMe": "VỀ TÔI",
  "about.aboutMeContent": "Tôi tốt nghiệp chương trình An toàn thông tin tại Đại học FPT, được trang bị kinh nghiệm về kiểm thử xâm nhập và quản lý dự án bảo mật. Điều thúc đẩy tôi mỗi ngày là mong muốn học hỏi và trở thành chuyên gia Pentest, giúp các doanh nghiệp đứng vững trước mọi thách thức về bảo mật.",
  "about.contactMe": "THÔNG TIN LIÊN HỆ",
  "about.phone": "Điện thoại",
  "about.email": "Email",
  "about.address": "Địa chỉ",
  "about.basicInfo": "THÔNG TIN CƠ BẢN",
  "about.birthday": "Ngày sinh",
  "about.nationality": "Quốc tịch",
  "about.maritalStatus": "Tình trạng hôn nhân",
  "about.gender": "Giới tính",
  "about.skills": "KỸ NĂNG",
  "about.education": "HỌC VẤN",
  "about.university": "Đại học FPT Đà Nẵng",
  "about.degree": "Cử nhân - An toàn thông tin",
  "about.period": "10/2020 - 12/2024 (4 năm 2 tháng)",
  "about.achievements": [
    "Là thành viên của Câu lạc bộ Nghiên cứu Bảo mật từ 09/2022 đến 12/2023.",
    "Dẫn dắt câu lạc bộ tham gia các cuộc thi như Hackathon, Secathon, Bootcamp và Secathon Asean.",
    "Được công nhận là Sinh viên Xuất sắc trong một năm.",
    "Đóng góp vào việc tổ chức các sự kiện về bảo mật, giúp câu lạc bộ đạt giải Câu lạc bộ Xuất sắc.",
    "Đạt vị trí Á quân cho Đồ án tốt nghiệp với chủ đề: 'Phát triển UniSAST: Nền tảng Web tích hợp các công cụ SAST mã nguồn mở cho Phân tích Bảo mật Mã nguồn Tự động và Hỗ trợ DevSecOps trong SMEs.'"
  ],
  "about.workHistory": "KINH NGHIỆM LÀM VIỆC",
  "about.company": "Shilla Monogram",
  "about.position": "Thực tập sinh IT",
  "about.workPeriod": "08/2023 - 10/2024 (1 năm 2 tháng)",
  "about.responsibilities": "Thực hiện các nhiệm vụ giám sát, cấu hình và quản lý hệ thống mạng cơ bản.",
  "about.certifications": "CHỨNG CHỈ",
  "about.certificationYear": "2023",
  "about.certificationProvider": "Coursera",
  
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
  "login.demoCredentials": "",
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

  // Articles page
  "articles.notFound": "Không tìm thấy bài viết nào",
  "articles.adjustSearch": "Hãy thử điều chỉnh tiêu chí tìm kiếm hoặc lọc của bạn.",
  "articles.minRead": "phút đọc",
  "articles.share": "Chia sẻ bài viết này",
  "articles.copyLink": "Sao chép liên kết",
  "articles.aboutAuthor": "Về tác giả",
  "articles.techWriter": "Người viết công nghệ",
  "articles.related": "Bài viết liên quan",

  // NotFound page
  "notFound.message": "Rất tiếc! Không tìm thấy trang",
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
