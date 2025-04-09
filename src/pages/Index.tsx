
import { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import { getFeaturedArticles, getLatestArticles, getArticlesByCategory } from "@/data/articles";
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import ArticleCard from "@/components/ArticleCard";
import FeaturedArticle from "@/components/FeaturedArticle";
import SearchBar from "@/components/SearchBar";
import { Button } from "@/components/ui/button";

const Index = () => {
  const [featuredArticles, setFeaturedArticles] = useState(getFeaturedArticles());
  const [latestArticles, setLatestArticles] = useState(getLatestArticles(3));
  const [securityArticles, setSecurityArticles] = useState(getArticlesByCategory("Cybersecurity"));

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
                  Learn about <span className="text-serein-500">Technology</span> and <span className="text-serein-500">Security</span>
                </h1>
                <p className="text-lg text-gray-600 md:pr-10">
                  Discover in-depth articles on web development, cybersecurity, cryptography, and more. Stay up to date with the latest technology trends and security best practices.
                </p>
                <div className="flex flex-col sm:flex-row gap-4 pt-2">
                  <Link to="/articles">
                    <Button className="bg-serein-500 hover:bg-serein-600 w-full sm:w-auto">
                      Browse Articles
                    </Button>
                  </Link>
                  <Link to="/about">
                    <Button variant="outline" className="w-full sm:w-auto">
                      About Serein
                    </Button>
                  </Link>
                </div>
              </div>
              <div className="md:w-1/2">
                <SearchBar className="max-w-md mx-auto md:mx-0 md:ml-auto" />
                
                <div className="mt-8 bg-white rounded-xl shadow-md p-6">
                  <h3 className="text-lg font-semibold mb-4">Popular Topics</h3>
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
              <h2 className="text-3xl font-bold mb-10">Featured Article</h2>
              <FeaturedArticle article={featuredArticles[0]} />
            </div>
          </section>
        )}

        {/* Latest Articles Section */}
        <section className="py-16 bg-gray-50">
          <div className="container">
            <div className="flex items-center justify-between mb-10">
              <h2 className="text-3xl font-bold">Latest Articles</h2>
              <Link to="/articles">
                <Button variant="outline">View All</Button>
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
                <Button variant="outline">View All</Button>
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
              <h2 className="text-3xl font-bold mb-4">Subscribe to our Newsletter</h2>
              <p className="text-serein-100 mb-8">
                Stay updated with the latest articles and insights. No spam, just valuable content.
              </p>
              <form className="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                <input
                  type="email"
                  placeholder="Your email address"
                  className="px-4 py-3 flex-1 rounded-md focus:outline-none text-gray-900"
                />
                <Button className="bg-gray-900 hover:bg-gray-800 text-white sm:w-auto">
                  Subscribe
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
