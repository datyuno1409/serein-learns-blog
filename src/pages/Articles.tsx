
import { useState, useEffect } from "react";
import { useLocation } from "react-router-dom";
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import ArticleCard from "@/components/ArticleCard";
import SearchBar from "@/components/SearchBar";
import { articles, Article, searchArticles, getArticlesByCategory, getArticlesByTag } from "@/data/articles";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";

const CATEGORIES = ["All", "Cybersecurity", "Web Development", "Cryptography", "Software Architecture", "DevOps"];

const Articles = () => {
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

  // Get all unique tags from articles
  const allTags = Array.from(
    new Set(articles.flatMap(article => article.tags))
  ).sort();

  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow">
        {/* Hero Section */}
        <section className="bg-gray-50 py-12">
          <div className="container">
            <h1 className="text-3xl md:text-4xl font-bold mb-6">Articles</h1>
            <p className="text-lg text-gray-600 mb-8 max-w-3xl">
              Explore our collection of articles on technology, security, and software development. 
              Use the search bar or filter by category to find exactly what you're looking for.
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
                  <ArticleCard key={article.id} article={article} />
                ))}
              </div>
            ) : (
              <div className="text-center py-16">
                <h3 className="text-2xl font-bold mb-4">No articles found</h3>
                <p className="text-gray-600 mb-6">
                  Try adjusting your search or filter criteria.
                </p>
                <Button 
                  onClick={() => handleCategoryChange("All")} 
                  className="bg-serein-500 hover:bg-serein-600"
                >
                  View All Articles
                </Button>
              </div>
            )}
          </div>
        </section>

        {/* Popular Tags */}
        <section className="py-12 bg-gray-50">
          <div className="container">
            <h2 className="text-2xl font-bold mb-6">Popular Tags</h2>
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
