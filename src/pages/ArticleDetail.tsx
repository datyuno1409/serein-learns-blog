
import { useState, useEffect } from "react";
import { useParams, Link } from "react-router-dom";
import { getArticleById, getLatestArticles, Article } from "@/data/articles";
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import ArticleCard from "@/components/ArticleCard";
import { CalendarDays, Clock, ArrowLeft } from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";

const ArticleDetail = () => {
  const { id } = useParams<{ id: string }>();
  const [article, setArticle] = useState<Article | undefined>(undefined);
  const [relatedArticles, setRelatedArticles] = useState<Article[]>([]);

  useEffect(() => {
    if (id) {
      const fetchedArticle = getArticleById(id);
      setArticle(fetchedArticle);

      // Get related articles (excluding current article)
      if (fetchedArticle) {
        const latestArticles = getLatestArticles(4);
        setRelatedArticles(
          latestArticles.filter(a => a.id !== id).slice(0, 3)
        );
      }
    }
  }, [id]);

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
      year: 'numeric', 
      month: 'long', 
      day: 'numeric'
    }).format(date);
  };

  if (!article) {
    return (
      <div className="flex flex-col min-h-screen">
        <Header />
        <main className="flex-grow flex items-center justify-center">
          <div className="text-center">
            <h1 className="text-3xl font-bold mb-4">Article Not Found</h1>
            <p className="text-gray-600 mb-6">The article you're looking for doesn't exist or has been removed.</p>
            <Link to="/articles">
              <Button className="bg-serein-500 hover:bg-serein-600">
                Browse All Articles
              </Button>
            </Link>
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
        <section className="relative bg-gray-900 text-white">
          <div className="absolute inset-0 opacity-40">
            <img 
              src={article.coverImage} 
              alt={article.title} 
              className="w-full h-full object-cover"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/80 to-transparent"></div>
          </div>
          
          <div className="container relative z-10 py-20">
            <Link to="/articles" className="inline-flex items-center text-white/80 hover:text-white mb-6 transition-colors">
              <ArrowLeft className="h-4 w-4 mr-2" />
              Back to Articles
            </Link>
            
            <Badge className="mb-4 bg-serein-500 hover:bg-serein-600">
              {article.category}
            </Badge>
            
            <h1 className="text-3xl md:text-4xl lg:text-5xl font-bold mb-6 max-w-4xl">
              {article.title}
            </h1>
            
            <div className="flex flex-wrap items-center gap-6 mb-8">
              <div className="flex items-center space-x-3">
                <div className="h-10 w-10 rounded-full overflow-hidden border-2 border-white/20">
                  <img 
                    src={article.authorImage} 
                    alt={article.author} 
                    className="h-full w-full object-cover"
                  />
                </div>
                <span className="font-medium">
                  {article.author}
                </span>
              </div>
              
              <div className="flex items-center text-white/80 text-sm">
                <CalendarDays className="h-4 w-4 mr-1" />
                <span>{formatDate(article.publishedAt)}</span>
              </div>
              
              <div className="flex items-center text-white/80 text-sm">
                <Clock className="h-4 w-4 mr-1" />
                <span>{article.readTime} min read</span>
              </div>
            </div>
          </div>
        </section>

        {/* Article Content */}
        <section className="py-12">
          <div className="container">
            <div className="grid grid-cols-1 lg:grid-cols-4 gap-12">
              <div className="lg:col-span-3">
                <div 
                  className="prose prose-lg max-w-none"
                  dangerouslySetInnerHTML={{ __html: article.content }}
                />
                
                {/* Tags */}
                <div className="mt-12 pt-8 border-t">
                  <h3 className="text-lg font-medium mb-4">Tags</h3>
                  <div className="flex flex-wrap gap-2">
                    {article.tags.map(tag => (
                      <Link key={tag} to={`/articles?tag=${tag}`}>
                        <Badge variant="outline" className="hover:bg-gray-100">
                          {tag}
                        </Badge>
                      </Link>
                    ))}
                  </div>
                </div>
                
                {/* Share */}
                <div className="mt-8">
                  <h3 className="text-lg font-medium mb-4">Share this article</h3>
                  <div className="flex gap-4">
                    <Button variant="outline" size="sm">Twitter</Button>
                    <Button variant="outline" size="sm">LinkedIn</Button>
                    <Button variant="outline" size="sm">Facebook</Button>
                    <Button variant="outline" size="sm">Copy Link</Button>
                  </div>
                </div>
              </div>
              
              {/* Sidebar */}
              <div className="lg:col-span-1">
                <div className="sticky top-24">
                  <div className="bg-gray-50 rounded-xl p-6 mb-8">
                    <h3 className="text-lg font-bold mb-4">About the Author</h3>
                    <div className="flex items-center gap-4 mb-4">
                      <img 
                        src={article.authorImage} 
                        alt={article.author} 
                        className="h-16 w-16 rounded-full object-cover"
                      />
                      <div>
                        <h4 className="font-medium">{article.author}</h4>
                        <p className="text-sm text-gray-600">Technology Writer</p>
                      </div>
                    </div>
                    <p className="text-gray-600 text-sm mb-4">
                      Technology enthusiast, security advocate, and lifelong learner sharing knowledge and insights with the world.
                    </p>
                    <Link to="/about">
                      <Button variant="link" className="text-serein-500 p-0">
                        Read more about Serein →
                      </Button>
                    </Link>
                  </div>
                  
                  {relatedArticles.length > 0 && (
                    <div>
                      <h3 className="text-lg font-bold mb-4">Related Articles</h3>
                      <div className="space-y-4">
                        {relatedArticles.map(relatedArticle => (
                          <Link 
                            key={relatedArticle.id} 
                            to={`/article/${relatedArticle.id}`}
                            className="block group"
                          >
                            <div className="flex gap-3">
                              <div className="w-16 h-16 rounded overflow-hidden shrink-0">
                                <img 
                                  src={relatedArticle.coverImage} 
                                  alt={relatedArticle.title} 
                                  className="w-full h-full object-cover"
                                />
                              </div>
                              <div>
                                <h4 className="font-medium text-sm line-clamp-2 group-hover:text-serein-500 transition-colors">
                                  {relatedArticle.title}
                                </h4>
                                <p className="text-xs text-gray-500 mt-1">
                                  {formatDate(relatedArticle.publishedAt)}
                                </p>
                              </div>
                            </div>
                          </Link>
                        ))}
                      </div>
                    </div>
                  )}
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>

      <Footer />
    </div>
  );
};

export default ArticleDetail;
