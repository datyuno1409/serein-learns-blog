
import { Link } from "react-router-dom";
import { Article } from "@/data/articles";
import { CalendarDays, Clock } from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardFooter } from "@/components/ui/card";

interface ArticleCardProps {
  article: Article;
}

const ArticleCard = ({ article }: ArticleCardProps) => {
  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
      year: 'numeric', 
      month: 'long', 
      day: 'numeric'
    }).format(date);
  };

  return (
    <Card className="overflow-hidden h-full flex flex-col hover:shadow-md transition-shadow duration-200">
      <Link to={`/article/${article.id}`} className="flex-1 flex flex-col">
        <div className="relative h-48 w-full overflow-hidden">
          <img 
            src={article.coverImage} 
            alt={article.title} 
            className="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
          />
          <div className="absolute top-4 left-4">
            <Badge className="bg-serein-500 hover:bg-serein-600">
              {article.category}
            </Badge>
          </div>
        </div>
        <CardContent className="pt-6 flex-1">
          <h3 className="text-xl font-bold mb-2 line-clamp-2 group-hover:text-serein-500 transition-colors duration-200">
            {article.title}
          </h3>
          <p className="text-gray-600 line-clamp-3 mb-4">
            {article.excerpt}
          </p>
        </CardContent>
      </Link>
      <CardFooter className="border-t border-gray-100 p-4 flex items-center justify-between">
        <div className="flex items-center space-x-2">
          <div className="h-8 w-8 rounded-full overflow-hidden">
            <img 
              src={article.authorImage} 
              alt={article.author} 
              className="h-full w-full object-cover"
            />
          </div>
          <span className="text-sm font-medium text-gray-700">
            {article.author}
          </span>
        </div>
        <div className="flex items-center space-x-4 text-gray-500 text-xs">
          <div className="flex items-center">
            <CalendarDays className="h-3 w-3 mr-1" />
            <span>{formatDate(article.publishedAt)}</span>
          </div>
          <div className="flex items-center">
            <Clock className="h-3 w-3 mr-1" />
            <span>{article.readTime} min read</span>
          </div>
        </div>
      </CardFooter>
    </Card>
  );
};

export default ArticleCard;
