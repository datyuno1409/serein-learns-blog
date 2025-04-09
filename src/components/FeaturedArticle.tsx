
import { Link } from "react-router-dom";
import { Article } from "@/data/articles";
import { CalendarDays, Clock } from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";

interface FeaturedArticleProps {
  article: Article;
}

const FeaturedArticle = ({ article }: FeaturedArticleProps) => {
  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
      year: 'numeric', 
      month: 'long', 
      day: 'numeric'
    }).format(date);
  };

  return (
    <div className="relative w-full bg-gradient-to-r from-gray-900 to-gray-800 rounded-xl overflow-hidden shadow-xl">
      <div className="absolute inset-0 z-0 opacity-40">
        <img 
          src={article.coverImage} 
          alt={article.title} 
          className="w-full h-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-r from-gray-900 to-transparent"></div>
      </div>
      
      <div className="relative z-10 p-8 md:p-12 flex flex-col h-full">
        <div className="flex flex-col md:flex-row items-start md:items-center gap-4 mb-6">
          <Badge className="bg-serein-500 hover:bg-serein-600">
            {article.category}
          </Badge>
          <div className="flex items-center space-x-4 text-gray-300 text-sm">
            <div className="flex items-center">
              <CalendarDays className="h-4 w-4 mr-1" />
              <span>{formatDate(article.publishedAt)}</span>
            </div>
            <div className="flex items-center">
              <Clock className="h-4 w-4 mr-1" />
              <span>{article.readTime} min read</span>
            </div>
          </div>
        </div>

        <h2 className="text-3xl md:text-4xl font-bold text-white mb-4 md:mb-6">
          {article.title}
        </h2>
        
        <p className="text-gray-300 mb-8 md:max-w-2xl">
          {article.excerpt}
        </p>
        
        <div className="mt-auto flex flex-col sm:flex-row items-start sm:items-center gap-6">
          <div className="flex items-center gap-3">
            <div className="h-10 w-10 rounded-full overflow-hidden border-2 border-white/20">
              <img 
                src={article.authorImage} 
                alt={article.author} 
                className="h-full w-full object-cover"
              />
            </div>
            <span className="text-white font-medium">
              {article.author}
            </span>
          </div>
          
          <Link to={`/article/${article.id}`}>
            <Button 
              className="bg-serein-500 hover:bg-serein-600 text-white"
            >
              Read Article
            </Button>
          </Link>
        </div>
      </div>
    </div>
  );
};

export default FeaturedArticle;
