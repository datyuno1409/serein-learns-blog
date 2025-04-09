
import React, { createContext, useContext, useState, useEffect } from "react";

type Language = "en" | "vi";

interface LanguageContextType {
  language: Language;
  setLanguage: (language: Language) => void;
  translations: Record<string, Record<string, string>>;
  t: (key: string) => string;
}

const translations = {
  en: {
    // Header
    "nav.home": "Home",
    "nav.articles": "Articles",
    "nav.about": "About",
    "nav.createArticle": "Create Article",
    "nav.search": "Search",
    
    // Homepage
    "hero.title": "Learn about Technology and Security",
    "hero.subtitle": "Discover in-depth articles on web development, cybersecurity, cryptography, and more. Stay up to date with the latest technology trends and security best practices.",
    "hero.browseArticles": "Browse Articles",
    "hero.aboutSerein": "About Serein",
    "popularTopics": "Popular Topics",
    "featuredArticle": "Featured Article",
    "latestArticles": "Latest Articles",
    "viewAll": "View All",
    "newsletterTitle": "Subscribe to our Newsletter",
    "newsletterSubtitle": "Stay updated with the latest articles and insights. No spam, just valuable content.",
    "newsletterPlaceholder": "Your email address",
    "subscribe": "Subscribe",
    
    // Footer
    "footer.rights": "All rights reserved",
    
    // Language
    "language": "Language",
    "language.english": "English",
    "language.vietnamese": "Vietnamese",
  },
  vi: {
    // Header
    "nav.home": "Trang chủ",
    "nav.articles": "Bài viết",
    "nav.about": "Giới thiệu",
    "nav.createArticle": "Tạo bài viết",
    "nav.search": "Tìm kiếm",
    
    // Homepage
    "hero.title": "Tìm hiểu về Công nghệ và Bảo mật",
    "hero.subtitle": "Khám phá các bài viết chuyên sâu về phát triển web, bảo mật mạng, mật mã học và nhiều chủ đề khác. Cập nhật các xu hướng công nghệ mới nhất và thực hành bảo mật tốt nhất.",
    "hero.browseArticles": "Xem bài viết",
    "hero.aboutSerein": "Về Serein",
    "popularTopics": "Chủ đề phổ biến",
    "featuredArticle": "Bài viết nổi bật",
    "latestArticles": "Bài viết mới nhất",
    "viewAll": "Xem tất cả",
    "newsletterTitle": "Đăng ký nhận bản tin",
    "newsletterSubtitle": "Cập nhật những bài viết và kiến thức mới nhất. Không spam, chỉ có nội dung giá trị.",
    "newsletterPlaceholder": "Địa chỉ email của bạn",
    "subscribe": "Đăng ký",
    
    // Footer
    "footer.rights": "Đã đăng ký bản quyền",
    
    // Language
    "language": "Ngôn ngữ",
    "language.english": "Tiếng Anh",
    "language.vietnamese": "Tiếng Việt",
  }
};

const LanguageContext = createContext<LanguageContextType | undefined>(undefined);

export const LanguageProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  // Try to get the language from localStorage, default to English
  const [language, setLanguageState] = useState<Language>(() => {
    const savedLanguage = localStorage.getItem("language");
    return (savedLanguage === "vi" ? "vi" : "en") as Language;
  });

  // Update localStorage when language changes
  const setLanguage = (newLanguage: Language) => {
    setLanguageState(newLanguage);
    localStorage.setItem("language", newLanguage);
  };

  // Translate function
  const t = (key: string): string => {
    return translations[language][key] || key;
  };

  // Save language to localStorage
  useEffect(() => {
    localStorage.setItem("language", language);
  }, [language]);

  return (
    <LanguageContext.Provider value={{ language, setLanguage, translations, t }}>
      {children}
    </LanguageContext.Provider>
  );
};

export const useLanguage = (): LanguageContextType => {
  const context = useContext(LanguageContext);
  if (!context) {
    throw new Error("useLanguage must be used within a LanguageProvider");
  }
  return context;
};
