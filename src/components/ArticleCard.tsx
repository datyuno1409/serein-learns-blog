
import { Link } from "react-router-dom";
import { CalendarDays, Clock, MoreVertical, Edit, Trash } from "lucide-react";
import { Article } from "@/data/articles";
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
import { useState } from "react";
import { useToast } from "@/hooks/use-toast";
import { useTranslation } from 'react-i18next';

interface ArticleCardProps {
  article: Article;
  onDelete?: (id: string) => void;
}

const ArticleCard = ({ article, onDelete }: ArticleCardProps) => {
  const { language, t } = useLanguage();
  const { user } = useAuth();
  const { toast } = useToast();
  const [showDeleteDialog, setShowDeleteDialog] = useState(false);

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    }).format(date);
  };

  const handleDelete = async () => {
    try {
      // In a real application, you would call your API here
      // await deleteArticle(article.id);
      
      if (onDelete) {
        onDelete(article.id);
      }
      
      toast({
        title: t('articles.deleteSuccess') as string,
        description: t('articles.deleteSuccessMessage') as string,
      });
    } catch (error) {
      toast({
        title: t('articles.deleteError') as string,
        description: t('articles.deleteErrorMessage') as string,
        variant: 'destructive',
      });
    }
    setShowDeleteDialog(false);
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
                    <span>{language === 'vi' ? 'Sửa' : 'Edit'}</span>
                  </Link>
                </DropdownMenuItem>
                <AlertDialog>
                  <AlertDialogTrigger asChild>
                    <DropdownMenuItem onSelect={(e) => e.preventDefault()} className="flex items-center justify-between w-full text-red-600 dark:text-red-400">
                      <div className="flex items-center w-full">
                        <Trash className="mr-2 h-4 w-4" />
                        <span>{language === 'vi' ? 'Xóa' : 'Delete'}</span>
                      </div>
                    </DropdownMenuItem>
                  </AlertDialogTrigger>
                  <AlertDialogContent>
                    <AlertDialogHeader>
                      <AlertDialogTitle>{t('articles.deleteConfirmTitle') as string}</AlertDialogTitle>
                      <AlertDialogDescription>
                        {t('articles.deleteConfirmMessage') as string}
                      </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                      <AlertDialogCancel>{t('common.cancel') as string}</AlertDialogCancel>
                      <AlertDialogAction onClick={handleDelete} className="bg-red-600 hover:bg-red-700">
                        {t('common.delete') as string}
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
                {new Date(article.publishedAt).toLocaleDateString()}
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
