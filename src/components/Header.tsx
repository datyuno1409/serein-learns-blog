import { useState } from "react";
import { Link, useLocation, useNavigate } from "react-router-dom";
import { Menu, X, Search, LogOut } from "lucide-react";
import { Button } from "@/components/ui/button";
import { useLanguage } from "@/contexts/LanguageContext";
import { useAuth } from "@/contexts/AuthContext";
import LanguageSwitcher from "@/components/LanguageSwitcher";

const Header = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const location = useLocation();
  const navigate = useNavigate();
  const { t } = useLanguage();
  const { user, isAuthenticated, logout } = useAuth();

  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen);
  };

  const isActive = (path: string) => {
    return location.pathname === path;
  };

  const handleLogout = () => {
    logout();
    navigate("/");
  };

  return (
    <header className="bg-white shadow-sm sticky top-0 z-50">
      <div className="container py-4">
        <div className="flex items-center justify-between">
          {/* Logo */}
          <Link to="/" className="flex items-center gap-2">
            <span className="text-2xl font-bold font-heading text-serein-600">
              Learning with <span className="text-serein-500">Serein</span>
            </span>
          </Link>

          {/* Desktop Navigation */}
          <nav className="hidden md:flex items-center space-x-8">
            <Link 
              to="/" 
              className={`text-base font-medium transition-colors duration-200 hover:text-serein-500 ${isActive('/') ? 'text-serein-500' : 'text-gray-700'}`}
            >
              {t("nav.home")}
            </Link>
            <Link 
              to="/articles" 
              className={`text-base font-medium transition-colors duration-200 hover:text-serein-500 ${isActive('/articles') ? 'text-serein-500' : 'text-gray-700'}`}
            >
              {t("nav.articles")}
            </Link>
            <Link 
              to="/my-projects" 
              className={`text-base font-medium transition-colors duration-200 hover:text-serein-500 ${isActive('/my-projects') ? 'text-serein-500' : 'text-gray-700'}`}
            >
              {t("nav.myProjects")}
            </Link>
            <Link 
              to="/about" 
              className={`text-base font-medium transition-colors duration-200 hover:text-serein-500 ${isActive('/about') ? 'text-serein-500' : 'text-gray-700'}`}
            >
              {t("nav.about")}
            </Link>
          </nav>

          {/* Action Buttons */}
          <div className="flex items-center gap-4">
            <Link to="/articles" className="hidden md:block">
              <Button variant="ghost" size="icon">
                <Search className="h-5 w-5" />
                <span className="sr-only">{t("nav.search")}</span>
              </Button>
            </Link>
            <LanguageSwitcher />
            
            {isAuthenticated ? (
              <>
                {/* Only show Create Article and Manage Articles if user is authenticated */}
                <Link to="/create-article" className="hidden md:block">
                  <Button 
                    variant="default" 
                    className="hidden md:inline-flex bg-serein-500 hover:bg-serein-600"
                  >
                    {t("nav.createArticle")}
                  </Button>
                </Link>

                <Link to="/manage-articles" className="hidden md:block">
                  <Button 
                    variant="outline"
                    className="hidden md:inline-flex"
                  >
                    {t("nav.manageArticles")}
                  </Button>
                </Link>
                
                <Button 
                  variant="ghost" 
                  size="icon" 
                  onClick={handleLogout}
                  className="hidden md:flex"
                >
                  <LogOut className="h-5 w-5" />
                  <span className="sr-only">{t("nav.logout")}</span>
                </Button>
              </>
            ) : (
              <Link to="/login" className="hidden md:block">
                <Button 
                  variant="outline"
                >
                  {t("nav.login")}
                </Button>
              </Link>
            )}
            
            <button 
              className="md:hidden"
              onClick={toggleMenu}
              aria-label="Toggle menu"
            >
              {isMenuOpen ? (
                <X className="h-6 w-6 text-gray-700" />
              ) : (
                <Menu className="h-6 w-6 text-gray-700" />
              )}
            </button>
          </div>
        </div>

        {/* Mobile Navigation */}
        {isMenuOpen && (
          <div className="md:hidden mt-4 pb-2 animate-fade-in">
            <nav className="flex flex-col space-y-4">
              <Link 
                to="/" 
                className={`text-base font-medium transition-colors duration-200 px-2 py-1 rounded-md ${
                  isActive('/') ? 'bg-serein-50 text-serein-500' : 'text-gray-700 hover:bg-gray-50'
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                {t("nav.home")}
              </Link>
              <Link 
                to="/articles" 
                className={`text-base font-medium transition-colors duration-200 px-2 py-1 rounded-md ${
                  isActive('/articles') ? 'bg-serein-50 text-serein-500' : 'text-gray-700 hover:bg-gray-50'
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                {t("nav.articles")}
              </Link>
              <Link 
                to="/my-projects" 
                className={`text-base font-medium transition-colors duration-200 px-2 py-1 rounded-md ${
                  isActive('/my-projects') ? 'bg-serein-50 text-serein-500' : 'text-gray-700 hover:bg-gray-50'
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                {t("nav.myProjects")}
              </Link>
              <Link 
                to="/about" 
                className={`text-base font-medium transition-colors duration-200 px-2 py-1 rounded-md ${
                  isActive('/about') ? 'bg-serein-50 text-serein-500' : 'text-gray-700 hover:bg-gray-50'
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                {t("nav.about")}
              </Link>
              
              {isAuthenticated ? (
                <>
                  <Link 
                    to="/create-article" 
                    className={`text-base font-medium transition-colors duration-200 px-2 py-1 rounded-md ${
                      isActive('/create-article') ? 'bg-serein-50 text-serein-500' : 'text-gray-700 hover:bg-gray-50'
                    }`}
                    onClick={() => setIsMenuOpen(false)}
                  >
                    {t("nav.createArticle")}
                  </Link>
                  <Link 
                    to="/manage-articles" 
                    className={`text-base font-medium transition-colors duration-200 px-2 py-1 rounded-md ${
                      isActive('/manage-articles') ? 'bg-serein-50 text-serein-500' : 'text-gray-700 hover:bg-gray-50'
                    }`}
                    onClick={() => setIsMenuOpen(false)}
                  >
                    {t("nav.manageArticles")}
                  </Link>
                  <button
                    className="text-base font-medium transition-colors duration-200 px-2 py-1 rounded-md text-gray-700 hover:bg-gray-50 text-left"
                    onClick={() => {
                      setIsMenuOpen(false);
                      logout();
                      navigate("/");
                    }}
                  >
                    {t("nav.logout")}
                  </button>
                </>
              ) : (
                <Link 
                  to="/login" 
                  className={`text-base font-medium transition-colors duration-200 px-2 py-1 rounded-md ${
                    isActive('/login') ? 'bg-serein-50 text-serein-500' : 'text-gray-700 hover:bg-gray-50'
                  }`}
                  onClick={() => setIsMenuOpen(false)}
                >
                  {t("nav.login")}
                </Link>
              )}
            </nav>
          </div>
        )}
      </div>
    </header>
  );
};

export default Header;
