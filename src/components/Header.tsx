
import { useState } from "react";
import { Link, useLocation } from "react-router-dom";
import { Menu, X, Search } from "lucide-react";
import { Button } from "@/components/ui/button";

const Header = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const location = useLocation();

  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen);
  };

  const isActive = (path: string) => {
    return location.pathname === path;
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
              Home
            </Link>
            <Link 
              to="/articles" 
              className={`text-base font-medium transition-colors duration-200 hover:text-serein-500 ${isActive('/articles') ? 'text-serein-500' : 'text-gray-700'}`}
            >
              Articles
            </Link>
            <Link 
              to="/about" 
              className={`text-base font-medium transition-colors duration-200 hover:text-serein-500 ${isActive('/about') ? 'text-serein-500' : 'text-gray-700'}`}
            >
              About
            </Link>
          </nav>

          {/* Action Buttons */}
          <div className="flex items-center gap-4">
            <Link to="/articles" className="hidden md:block">
              <Button variant="ghost" size="icon">
                <Search className="h-5 w-5" />
              </Button>
            </Link>
            <Link to="/create-article">
              <Button 
                variant="default" 
                className="hidden md:inline-flex bg-serein-500 hover:bg-serein-600"
              >
                Create Article
              </Button>
            </Link>
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
                Home
              </Link>
              <Link 
                to="/articles" 
                className={`text-base font-medium transition-colors duration-200 px-2 py-1 rounded-md ${
                  isActive('/articles') ? 'bg-serein-50 text-serein-500' : 'text-gray-700 hover:bg-gray-50'
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                Articles
              </Link>
              <Link 
                to="/about" 
                className={`text-base font-medium transition-colors duration-200 px-2 py-1 rounded-md ${
                  isActive('/about') ? 'bg-serein-50 text-serein-500' : 'text-gray-700 hover:bg-gray-50'
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                About
              </Link>
              <Link 
                to="/create-article" 
                className={`text-base font-medium transition-colors duration-200 px-2 py-1 rounded-md ${
                  isActive('/create-article') ? 'bg-serein-50 text-serein-500' : 'text-gray-700 hover:bg-gray-50'
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                Create Article
              </Link>
            </nav>
          </div>
        )}
      </div>
    </header>
  );
};

export default Header;
