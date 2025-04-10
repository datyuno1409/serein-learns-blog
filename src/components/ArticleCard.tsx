
import { Link } from "react-router-dom";
import { MoreVertical, Edit, Trash } from "lucide-react";
import { Article, deleteArticle } from "@/data/articles";
import { useLanguage } from "@/contexts/LanguageContext";
import { useAuth } from "@/contexts/AuthContext";
import { Button } from "@/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
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
import { useToast } from "@/hooks/use-toast";

interface ArticleCardProps {
  article: Article;
  onDelete?: (id: string) => void;
}

const ArticleCard = ({ article, onDelete }: ArticleCardProps) => {
  const { language, t } = useLanguage();
  const { user } = useAuth();
  const { toast } = useToast();

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    }).format(date);
  };

  const handleDelete = () => {
    try {
      // Call the deleteArticle function from data/articles.ts
      const success = deleteArticle(article.id);
      
      if (success) {
        // If the parent component provides an onDelete callback, call it
        if (onDelete) {
          onDelete(article.id);
        }
        
        toast({
          title: String(t('articles.deleteSuccess')),
          description: String(t('articles.deleteSuccessMessage')),
        });
      } else {
        throw new Error("Failed to delete article");
      }
    } catch (error) {
      toast({
        title: String(t('articles.deleteError')),
        description: String(t('articles.deleteErrorMessage')),
        variant: 'destructive',
      });
    }
  };

  const canModify = user && (user.role === 'admin' || (user.role === 'author' && article.authorId === user.username));

  return (
    <div className="group relative flex flex-col overflow-hidden rounded-lg border bg-white shadow-sm transition-all hover:shadow-lg dark:bg-gray-800/40">
      <div className="relative h-48 overflow-hidden">
        <img
          src={article.coverImage}
          alt={article.title}
          className="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
        />
        {canModify && (
          <div className="absolute right-2 top-2">
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button variant="ghost" size="icon" className="h-8 w-8 bg-white/80 hover:bg-white dark:bg-gray-800/80 dark:hover:bg-gray-800">
                  <MoreVertical className="h-4 w-4" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end">
                <DropdownMenuItem asChild className="flex items-center justify-between w-full">
                  <Link to={`/articles/edit/${article.id}`} className="flex items-center w-full">
                    <Edit className="mr-2 h-4 w-4" />
                    <span>{String(t('articles.edit'))}</span>
                  </Link>
                </DropdownMenuItem>
                <AlertDialog>
                  <AlertDialogTrigger asChild>
                    <DropdownMenuItem onSelect={(e) => e.preventDefault()} className="flex items-center justify-between w-full text-red-600 dark:text-red-400">
                      <div className="flex items-center w-full">
                        <Trash className="mr-2 h-4 w-4" />
                        <span>{String(t('articles.delete'))}</span>
                      </div>
                    </DropdownMenuItem>
                  </AlertDialogTrigger>
                  <AlertDialogContent>
                    <AlertDialogHeader>
                      <AlertDialogTitle>{String(t('articles.deleteConfirmTitle'))}</AlertDialogTitle>
                      <AlertDialogDescription>
                        {String(t('articles.deleteConfirmMessage'))}
                      </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                      <AlertDialogCancel>{String(t('common.cancel'))}</AlertDialogCancel>
                      <AlertDialogAction onClick={handleDelete} className="bg-red-600 hover:bg-red-700">
                        {String(t('common.delete'))}
                      </AlertDialogAction>
                    </AlertDialogFooter>
                  </AlertDialogContent>
                </AlertDialog>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        )}
      </div>
      <div className="flex flex-1 flex-col justify-between gap-4 p-4">
        <div>
          <Link to={`/article/${article.id}`} className="block">
            <h3 className="line-clamp-2 text-xl font-semibold text-gray-900 hover:text-primary dark:text-gray-100 dark:hover:text-primary">
              {article.title}
            </h3>
          </Link>
          <p className="mt-2 line-clamp-3 text-sm text-gray-600 dark:text-gray-300">
            {article.excerpt}
          </p>
        </div>
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-2">
            <img
              src={article.authorImage}
              alt={article.author}
              className="h-8 w-8 rounded-full object-cover"
            />
            <div>
              <p className="text-sm font-medium text-gray-900 dark:text-gray-100">
                {article.author}
              </p>
              <p className="text-xs text-gray-500 dark:text-gray-400">
                {formatDate(article.publishedAt)}
              </p>
            </div>
          </div>
          <span className="text-xs text-gray-500 dark:text-gray-400">
            {article.readTime} {language === 'vi' ? 'phút đọc' : 'min read'}
          </span>
        </div>
      </div>
    </div>
  );
};

export default ArticleCard;
