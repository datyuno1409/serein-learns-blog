
import { useState, useEffect } from "react";
import { useLocation } from "react-router-dom";
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import ArticleCard from "@/components/ArticleCard";
import SearchBar from "@/components/SearchBar";
import { articles, Article, searchArticles, getArticlesByCategory, getArticlesByTag, clearAllArticles } from "@/data/articles";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { useLanguage } from "@/contexts/LanguageContext";
import { Trash2 } from "lucide-react";
import { useToast } from "@/hooks/use-toast";
import { useAuth } from "@/contexts/AuthContext";
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

const CATEGORIES = ["All", "Cybersecurity", "Web Development", "Cryptography", "Software Architecture", "DevOps"];

const Articles = () => {
  const { t } = useLanguage();
  const { toast } = useToast();
  const { user } = useAuth();
  const [filteredArticles, setFilteredArticles] = useState<Article[]>(articles);
  const [selectedCategory, setSelectedCategory] = useState("All");
  const location = useLocation();

  useEffect(() => {
    const query = new URLSearchParams(location.search);
    const searchParam = query.get("search");
    const categoryParam = query.get("category");
    const tagParam = query.get("tag");

    if (searchParam) {
      setFilteredArticles(searchArticles(searchParam));
      setSelectedCategory("All");
    } else if (categoryParam) {
      const category = categoryParam.replace("+", " ");
      setFilteredArticles(getArticlesByCategory(category));
      setSelectedCategory(category);
    } else if (tagParam) {
      setFilteredArticles(getArticlesByTag(tagParam));
      setSelectedCategory("All");
    } else {
      setFilteredArticles(articles);
      setSelectedCategory("All");
    }
  }, [location.search]);

  const handleCategoryChange = (category: string) => {
    setSelectedCategory(category);
    if (category === "All") {
      setFilteredArticles(articles);
    } else {
      setFilteredArticles(getArticlesByCategory(category));
    }
  };

  const handleClearAllArticles = () => {
    const success = clearAllArticles();
    if (success) {
      setFilteredArticles([]);
      toast({
        title: t('articles.clearSuccess') as string,
        description: t('articles.clearSuccessMessage') as string,
      });
    } else {
      toast({
        title: t('articles.clearError') as string,
        description: t('articles.clearErrorMessage') as string,
        variant: 'destructive',
      });
    }
  };

  const handleArticleDelete = (id: string) => {
    setFilteredArticles(prevArticles => prevArticles.filter(article => article.id !== id));
  };

  // Get all unique tags from articles
  const allTags = Array.from(
    new Set(filteredArticles.flatMap(article => article.tags))
  ).sort();

  // Check if user has admin privileges
  const isAdmin = user && user.role === 'admin';

  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow">
        {/* Hero Section */}
        <section className="bg-gray-50 py-12">
          <div className="container">
            <div className="flex justify-between items-center mb-6">
              <h1 className="text-3xl md:text-4xl font-bold">{t('nav.articles')}</h1>
              
              {isAdmin && (
                <AlertDialog>
                  <AlertDialogTrigger asChild>
                    <Button variant="destructive" className="flex items-center gap-2">
                      <Trash2 className="h-4 w-4" />
                      {t('articles.clearAll') as string}
                    </Button>
                  </AlertDialogTrigger>
                  <AlertDialogContent>
                    <AlertDialogHeader>
                      <AlertDialogTitle>{t('articles.clearConfirmTitle') as string}</AlertDialogTitle>
                      <AlertDialogDescription>
                        {t('articles.clearConfirmMessage') as string}
                      </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                      <AlertDialogCancel>{t('common.cancel') as string}</AlertDialogCancel>
                      <AlertDialogAction onClick={handleClearAllArticles} className="bg-red-600 hover:bg-red-700">
                        {t('common.confirm') as string}
                      </AlertDialogAction>
                    </AlertDialogFooter>
                  </AlertDialogContent>
                </AlertDialog>
              )}
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
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {filteredArticles.map(article => (
                  <ArticleCard key={article.id} article={article} onDelete={handleArticleDelete} />
                ))}
              </div>
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
                    setFilteredArticles(getArticlesByTag(tag));
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
