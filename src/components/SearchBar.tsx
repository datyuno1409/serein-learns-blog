import { useState, useEffect } from "react";
import { Search } from "lucide-react";
import { useNavigate, useLocation } from "react-router-dom";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { useLanguage } from "@/contexts/LanguageContext";

interface SearchBarProps {
  className?: string;
  placeholder?: string;
  onSearch?: (query: string) => void;
}

const SearchBar = ({ className = "", placeholder, onSearch }: SearchBarProps) => {
  const [searchQuery, setSearchQuery] = useState("");
  const navigate = useNavigate();
  const location = useLocation();
  const { t } = useLanguage();

  // Initialize search query from URL if present
  useEffect(() => {
    const params = new URLSearchParams(location.search);
    const searchParam = params.get("search");
    if (searchParam) {
      setSearchQuery(searchParam);
    }
  }, [location.search]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (searchQuery.trim()) {
      // Call the onSearch prop if provided
      if (onSearch) {
        onSearch(searchQuery.trim());
      }
      
      // Navigate to articles with search parameter
      navigate(`/articles?search=${encodeURIComponent(searchQuery.trim())}`);
    }
  };

  // Safe conversion to string for placeholder
  const getPlaceholder = (): string => {
    const translatedText = t("nav.search");
    return placeholder || (typeof translatedText === 'string' ? translatedText : 'Search');
  };

  return (
    <form onSubmit={handleSubmit} className={`flex gap-2 w-full ${className}`}>
      <div className="relative flex-1">
        <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
        <Input
          type="text"
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
          placeholder={getPlaceholder()}
          className="pl-10 w-full"
        />
      </div>
      <Button 
        type="submit" 
        className="bg-serein-500 hover:bg-serein-600"
      >
        {typeof t("nav.search") === 'string' ? t("nav.search") : 'Search'}
      </Button>
    </form>
  );
};

export default SearchBar;
