import { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import ArticleCard from "@/components/ArticleCard";
import FeaturedArticle from "@/components/FeaturedArticle";
import SearchBar from "@/components/SearchBar";
import { Button } from "@/components/ui/button";
import { useLanguage } from "@/contexts/LanguageContext";
import { articleAPI } from "@/services/api";
import { useToast } from "@/hooks/use-toast";

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

const Index = () => {
  const [featuredArticles, setFeaturedArticles] = useState<Article[]>([]);
  const [latestArticles, setLatestArticles] = useState<Article[]>([]);
  const [securityArticles, setSecurityArticles] = useState<Article[]>([]);
  const { t } = useLanguage();
  const { toast } = useToast();
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchArticles = async () => {
      try {
        setLoading(true);
        // Fetch all articles
        const response = await articleAPI.getAll(1, 100); // Get first 100 articles
        const articles: Article[] = response.data;

        // Set featured articles
        const featured = articles.filter((article: Article) => article.featured);
        setFeaturedArticles(featured);

        // Set latest articles
        const latest = [...articles].sort((a: Article, b: Article) => 
          new Date(b.publishedAt).getTime() - new Date(a.publishedAt).getTime()
        ).slice(0, 3);
        setLatestArticles(latest);

        // Set security articles
        const security = articles.filter((article: Article) => article.category === "Cybersecurity").slice(0, 3);
        setSecurityArticles(security);
      } catch (error) {
        console.error('Error fetching articles:', error);
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
  }, [toast]);

  if (loading) {
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
        <section className="bg-gradient-to-r from-gray-50 to-gray-100 py-16 md:py-24">
          <div className="container">
            <div className="flex flex-col md:flex-row gap-10 items-center">
              <div className="md:w-1/2 space-y-6">
                <h1 className="text-4xl md:text-5xl font-bold text-gray-900 leading-tight">
                  {t("hero.title")}
                </h1>
                <p className="text-lg text-gray-600 md:pr-10">
                  {t("hero.subtitle")}
                </p>
                <div className="flex flex-col sm:flex-row gap-4 pt-2">
                  <Link to="/articles">
                    <Button className="bg-serein-500 hover:bg-serein-600 w-full sm:w-auto">
                      {t("hero.browseArticles")}
                    </Button>
                  </Link>
                  <Link to="/about">
                    <Button variant="outline" className="w-full sm:w-auto">
                      {t("hero.aboutSerein")}
                    </Button>
                  </Link>
                </div>
              </div>
              <div className="md:w-1/2">
                <SearchBar className="max-w-md mx-auto md:mx-0 md:ml-auto" />
                
                <div className="mt-8 bg-white rounded-xl shadow-md p-6">
                  <h3 className="text-lg font-semibold mb-4">{t("popularTopics")}</h3>
                  <div className="flex flex-wrap gap-2">
                    <Link to="/articles?category=Cybersecurity">
                      <Button variant="secondary" size="sm">Cybersecurity</Button>
                    </Link>
                    <Link to="/articles?category=Web+Development">
                      <Button variant="secondary" size="sm">Web Development</Button>
                    </Link>
                    <Link to="/articles?category=Cryptography">
                      <Button variant="secondary" size="sm">Cryptography</Button>
                    </Link>
                    <Link to="/articles?category=Software+Architecture">
                      <Button variant="secondary" size="sm">Software Architecture</Button>
                    </Link>
                    <Link to="/articles?category=DevOps">
                      <Button variant="secondary" size="sm">DevOps</Button>
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Featured Article Section */}
        {featuredArticles.length > 0 && (
          <section className="py-16">
            <div className="container">
              <h2 className="text-3xl font-bold mb-10">{t("featuredArticle")}</h2>
              <FeaturedArticle article={featuredArticles[0]} />
            </div>
          </section>
        )}

        {/* Latest Articles Section */}
        <section className="py-16 bg-gray-50">
          <div className="container">
            <div className="flex items-center justify-between mb-10">
              <h2 className="text-3xl font-bold">{t("latestArticles")}</h2>
              <Link to="/articles">
                <Button variant="outline">{t("viewAll")}</Button>
              </Link>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              {latestArticles.map(article => (
                <ArticleCard key={article.id} article={article} />
              ))}
            </div>
          </div>
        </section>

        {/* Cybersecurity Section */}
        <section className="py-16">
          <div className="container">
            <div className="flex items-center justify-between mb-10">
              <h2 className="text-3xl font-bold">Cybersecurity</h2>
              <Link to="/articles?category=Cybersecurity">
                <Button variant="outline">{t("viewAll")}</Button>
              </Link>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              {securityArticles.slice(0, 3).map(article => (
                <ArticleCard key={article.id} article={article} />
              ))}
            </div>
          </div>
        </section>

        {/* Newsletter Section */}
        <section className="py-16 bg-serein-500 text-white">
          <div className="container">
            <div className="max-w-3xl mx-auto text-center">
              <h2 className="text-3xl font-bold mb-4">{t("newsletterTitle")}</h2>
              <p className="text-serein-100 mb-8">
                {t("newsletterSubtitle")}
              </p>
              <form className="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                <input
                  type="email"
                  placeholder={t("newsletterPlaceholder")}
                  className="px-4 py-3 flex-1 rounded-md focus:outline-none text-gray-900"
                />
                <Button className="bg-gray-900 hover:bg-gray-800 text-white sm:w-auto">
                  {t("subscribe")}
                </Button>
              </form>
            </div>
          </div>
        </section>
      </main>

      <Footer />
    </div>
  );
};

export default Index;
