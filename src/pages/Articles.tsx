import { useState, useEffect, useCallback } from "react";
import { useLocation } from "react-router-dom";
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import ArticleCard from "@/components/ArticleCard";
import SearchBar from "@/components/SearchBar";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { useLanguage } from "@/contexts/LanguageContext";
import { Trash2 } from "lucide-react";
import { useToast } from "@/hooks/use-toast";
import { useAuth } from "@/contexts/AuthContext";
import { articleAPI } from "@/services/api";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import { useInView } from 'react-intersection-observer';
import { useTranslation } from 'react-i18next';

interface Article {
  id: string;
  title: string;
  excerpt: string;
  content: string;
  coverImage: string;
  author: string;
  authorId: string;
  authorImage: string;
  category: string;
  tags: string[];
  publishedAt: string;
  readTime: number;
  featured?: boolean;
}

const CATEGORIES = ["All", "Cybersecurity", "Web Development", "Cryptography", "Software Architecture", "DevOps"];
const ITEMS_PER_PAGE = 12;

const Articles = () => {
  const { t } = useTranslation();
  const { toast } = useToast();
  const { user } = useAuth();
  const location = useLocation();
  const [articles, setArticles] = useState<Article[]>([]);
  const [filteredArticles, setFilteredArticles] = useState<Article[]>([]);
  const [selectedCategory, setSelectedCategory] = useState("All");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [searchQuery, setSearchQuery] = useState("");

  const { ref, inView } = useInView({
    threshold: 0,
  });

  // Handle search query from URL when component mounts
  useEffect(() => {
    const query = new URLSearchParams(location.search);
    const categoryParam = query.get('category');
    const searchParam = query.get('search');
    
    if (categoryParam) {
      setSelectedCategory(categoryParam);
    }
    
    if (searchParam) {
      setSearchQuery(searchParam);
    }
  }, [location.search]);

  // Memoize the filter function
  const filterArticles = useCallback((category: string, query: string, items: Article[]) => {
    let filtered = items;
    
    if (category !== "All") {
      filtered = filtered.filter(article => article.category === category);
    }
    
    if (query) {
      const searchLower = query.toLowerCase();
      filtered = filtered.filter(article => 
        article.title.toLowerCase().includes(searchLower) ||
        article.excerpt.toLowerCase().includes(searchLower) ||
        article.tags.some(tag => tag.toLowerCase().includes(searchLower))
      );
    }
    
    return filtered;
  }, []);

  // Load more articles when scrolling
  useEffect(() => {
    if (inView && hasMore && !loading) {
      setPage(prev => prev + 1);
    }
  }, [inView, hasMore, loading]);

  // Fetch articles with pagination
  useEffect(() => {
    const fetchArticles = async () => {
      try {
        setLoading(true);
        console.log(`Articles: Fetching page ${page} with ${ITEMS_PER_PAGE} items per page`);
        
        const response = await articleAPI.getAll(page, ITEMS_PER_PAGE);
        console.log('Articles API response:', response);
        
        // Ensure we have an array of articles
        let newArticles: Article[] = [];
        
        if (response && response.data) {
          // Check if response.data is already an array
          if (Array.isArray(response.data)) {
            newArticles = response.data;
            console.log('Got array directly:', newArticles.length);
          } 
          // Check for nested data property
          else if (response.data.data && Array.isArray(response.data.data)) {
            newArticles = response.data.data;
            console.log('Got nested data array:', newArticles.length);
          }
          else {
            console.warn('Unexpected API response format:', response.data);
            newArticles = [];
          }
        }
        
        console.log('Articles parsed:', newArticles.length);
        console.log('Sample article:', newArticles.length > 0 ? newArticles[0] : 'No articles');
        
        if (page === 1) {
          // Reset articles list when loading first page
          setArticles(newArticles);
        } else {
          setArticles(prev => {
            const combined = [...prev, ...newArticles];
            // Remove duplicates based on id
            return Array.from(new Map(combined.map(item => [item.id, item])).values());
          });
        }
        
        setHasMore(newArticles.length === ITEMS_PER_PAGE);
        console.log(`Loaded ${newArticles.length} articles, hasMore: ${newArticles.length === ITEMS_PER_PAGE}`);
      } catch (error) {
        console.error('Error fetching articles:', error);
        setError('Failed to load articles. Please try again.');
        toast({
          title: "Error",
          description: "Failed to load articles",
          variant: "destructive",
        });
      } finally {
        setLoading(false);
      }
    };

    fetchArticles();
  }, [page, toast]);

  // Reset page when category or search changes
  useEffect(() => {
    setPage(1);
  }, [selectedCategory, searchQuery]);

  // Apply filters when category or search changes
  useEffect(() => {
    const filtered = filterArticles(selectedCategory, searchQuery, articles);
    setFilteredArticles(filtered);
  }, [selectedCategory, searchQuery, articles, filterArticles]);

  const handleSearch = (query: string) => {
    setSearchQuery(query);
    setPage(1); // Reset pagination when search changes
  };

  const handleCategoryChange = (category: string) => {
    setSelectedCategory(category);
    setPage(1); // Reset pagination when category changes
  };

  const handleClearAllArticles = async () => {
    try {
      await articleAPI.clearAll();
      setArticles([]);
      setFilteredArticles([]);
      toast({
        title: String(t('articles.clearSuccess')),
        description: String(t('articles.clearSuccessMessage')),
      });
    } catch (error) {
      toast({
        title: String(t('articles.clearError')),
        description: String(t('articles.clearErrorMessage')),
        variant: 'destructive',
      });
    }
  };

  const handleCreateSampleArticles = async () => {
    try {
      setLoading(true);
      const result = await articleAPI.createSamples();
      toast({
        title: "Thành công",
        description: `Đã tạo ${result.articles?.length || 0} bài viết mẫu`,
      });
      // Fetch articles again
      setPage(1);
      const response = await articleAPI.getAll(1, ITEMS_PER_PAGE);
      let newArticles: Article[] = [];
      
      if (response && response.data) {
        if (Array.isArray(response.data)) {
          newArticles = response.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
          newArticles = response.data.data;
        }
      }
      
      setArticles(newArticles);
      const filtered = filterArticles(selectedCategory, searchQuery, newArticles);
      setFilteredArticles(filtered);
    } catch (error) {
      console.error('Error creating sample articles:', error);
      toast({
        title: "Lỗi",
        description: "Không thể tạo bài viết mẫu. Vui lòng thử lại.",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  const handleArticleDelete = async (id: string) => {
    try {
      await articleAPI.delete(id);
      setArticles(prevArticles => prevArticles.filter(article => article.id !== id));
      setFilteredArticles(prevArticles => prevArticles.filter(article => article.id !== id));
      toast({
        title: "Success",
        description: "Article deleted successfully",
      });
    } catch (error) {
      toast({
        title: "Error",
        description: "Failed to delete article",
        variant: "destructive",
      });
    }
  };

  // Get all unique tags from articles with safe check
  const allTags = Array.from(
    new Set(Array.isArray(articles) ? articles.flatMap(article => article.tags || []) : [])
  ).sort();

  // Check if user has admin privileges
  const isAdmin = user && user.role === 'admin';

  if (loading && page === 1) {
    return (
      <div className="flex flex-col min-h-screen">
        <Header />
        <main className="flex-grow flex items-center justify-center">
          <div className="text-center">
            <p className="text-lg">Loading articles...</p>
          </div>
        </main>
        <Footer />
      </div>
    );
  }

  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow">
        {/* Hero Section */}
        <section className="bg-gray-50 py-12">
          <div className="container">
            <div className="flex justify-between items-center mb-6">
              <h1 className="text-3xl md:text-4xl font-bold">{t('nav.articles')}</h1>
              
              <div className="flex gap-2">
                {isAdmin && (
                  <>
                    <Button 
                      variant="outline" 
                      className="flex items-center gap-2"
                      onClick={handleCreateSampleArticles}
                    >
                      <span>Tạo bài mẫu</span>
                    </Button>
                    
                    <AlertDialog>
                      <AlertDialogTrigger asChild>
                        <Button variant="destructive" className="flex items-center gap-2">
                          <Trash2 className="h-4 w-4" />
                          {String(t('articles.clearAll'))}
                        </Button>
                      </AlertDialogTrigger>
                      <AlertDialogContent>
                        <AlertDialogHeader>
                          <AlertDialogTitle>{String(t('articles.clearConfirmTitle'))}</AlertDialogTitle>
                          <AlertDialogDescription>
                            {String(t('articles.clearConfirmMessage'))}
                          </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                          <AlertDialogCancel>{String(t('common.cancel'))}</AlertDialogCancel>
                          <AlertDialogAction onClick={handleClearAllArticles} className="bg-red-600 hover:bg-red-700">
                            {String(t('common.delete'))}
                          </AlertDialogAction>
                        </AlertDialogFooter>
                      </AlertDialogContent>
                    </AlertDialog>
                  </>
                )}
              </div>
            </div>
            <p className="text-lg text-gray-600 mb-8 max-w-3xl">
              {t('hero.subtitle')}
            </p>
            <SearchBar className="max-w-2xl" />
          </div>
        </section>

        {/* Categories Section */}
        <section className="py-6 border-b">
          <div className="container">
            <div className="flex flex-wrap gap-3">
              {CATEGORIES.map((category) => (
                <Button
                  key={category}
                  variant={selectedCategory === category ? "default" : "outline"}
                  className={selectedCategory === category ? "bg-serein-500 hover:bg-serein-600" : ""}
                  onClick={() => handleCategoryChange(category)}
                >
                  {category}
                </Button>
              ))}
            </div>
          </div>
        </section>

        {/* Articles Grid */}
        <section className="py-12">
          <div className="container">
            {filteredArticles.length > 0 ? (
              <>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                  {filteredArticles.map(article => (
                    <ArticleCard key={article.id} article={article} onDelete={handleArticleDelete} />
                  ))}
                </div>
                {/* Loading indicator */}
                <div ref={ref} className="flex justify-center mt-8">
                  {hasMore && <p className="text-gray-500">Loading more articles...</p>}
                </div>
              </>
            ) : (
              <div className="text-center py-16">
                <h3 className="text-2xl font-bold mb-4">{t('articles.notFound')}</h3>
                <p className="text-gray-600 mb-6">
                  {t('articles.adjustSearch')}
                </p>
                <Button 
                  onClick={() => handleCategoryChange("All")} 
                  className="bg-serein-500 hover:bg-serein-600"
                >
                  {t('viewAll')}
                </Button>
              </div>
            )}
          </div>
        </section>

        {/* Popular Tags */}
        <section className="py-12 bg-gray-50">
          <div className="container">
            <h2 className="text-2xl font-bold mb-6">{t('popularTopics')}</h2>
            <div className="flex flex-wrap gap-2">
              {allTags.map(tag => (
                <Badge 
                  key={tag} 
                  variant="secondary"
                  className="px-3 py-1 text-sm cursor-pointer hover:bg-gray-200"
                  onClick={() => {
                    const filtered = articles.filter(article =>
                      article.tags.includes(tag)
                    );
                    setFilteredArticles(filtered);
                    setSelectedCategory("All");
                  }}
                >
                  {tag}
                </Badge>
              ))}
            </div>
          </div>
        </section>
      </main>

      <Footer />
    </div>
  );
};

export default Articles;
