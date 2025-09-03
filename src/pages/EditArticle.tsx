import { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { useLanguage } from "@/contexts/LanguageContext";
import { useAuth } from "@/contexts/AuthContext";
import { useToast } from "@/hooks/use-toast";
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Article } from "@/data/articles";
import { articleAPI } from "@/services/api";

const EditArticle = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { t } = useLanguage();
  const { user } = useAuth();
  const { toast } = useToast();
  
  const [article, setArticle] = useState<Article | null>(null);
  const [title, setTitle] = useState("");
  const [excerpt, setExcerpt] = useState("");
  const [content, setContent] = useState("");
  const [coverImage, setCoverImage] = useState("");
  const [category, setCategory] = useState("");
  const [tags, setTags] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);

  useEffect(() => {
    if (id) {
      articleAPI.getById(id).then(fetchedArticle => {
        if (fetchedArticle) {
          setArticle(fetchedArticle);
          setTitle(fetchedArticle.title);
          setExcerpt(fetchedArticle.excerpt);
          setContent(fetchedArticle.content);
          setCoverImage(fetchedArticle.coverImage);
          setCategory(fetchedArticle.category);
          setTags((fetchedArticle.tags || []).join(', '));
        }
      }).catch(() => setArticle(null));
    }
  }, [id]);

  // Check if user has permission to edit
  useEffect(() => {
    if (article && user) {
      const canEdit = user.role === 'admin' || (user.role === 'author' && article.authorId === user.username);
      if (!canEdit) {
        toast({
          title: t('auth.unauthorized') as string,
          description: t('auth.loginRequired') as string,
          variant: "destructive",
        });
        navigate('/');
      }
    }
  }, [article, user, navigate, toast, t]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);

    try {
      // Validate required fields
      if (!title || !excerpt || !content || !coverImage || !category) {
        toast({
          title: t('createArticle.missingFields') as string,
          description: t('createArticle.fillRequiredFields') as string,
          variant: "destructive",
        });
        return;
      }

      const formData = new FormData();
      formData.append('title', title);
      formData.append('excerpt', excerpt);
      formData.append('content', content);
      formData.append('category', category);
      formData.append('tags', tags);
      if (coverImage) formData.append('coverImage', coverImage);
      await articleAPI.update(id!, formData);

      toast({
        title: "Success",
        description: "Article updated successfully",
      });

      navigate(`/article/${id}`);
    } catch (error) {
      toast({
        title: "Error",
        description: "Failed to update article",
        variant: "destructive",
      });
    } finally {
      setIsSubmitting(false);
    }
  };

  if (!article) {
    return (
      <div className="flex flex-col min-h-screen">
        <Header />
        <main className="flex-grow flex items-center justify-center">
          <div className="text-center">
            <h1 className="text-3xl font-bold mb-4">{t('articles.notFound') as string}</h1>
            <p className="text-gray-600 mb-6">{t('articles.adjustSearch') as string}</p>
            <Button 
              onClick={() => navigate('/articles')}
              className="bg-serein-500 hover:bg-serein-600"
            >
              {t('viewAll') as string}
            </Button>
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
        <section className="bg-gray-50 py-8">
          <div className="container">
            <h1 className="text-3xl font-bold mb-2">{t('createArticle.title') as string}</h1>
            <p className="text-gray-600 mb-0">
              {t('createArticle.subtitle') as string}
            </p>
          </div>
        </section>

        <section className="py-12">
          <div className="container">
            <div className="max-w-3xl mx-auto">
              <form onSubmit={handleSubmit} className="space-y-8">
                <div className="space-y-2">
                  <Label htmlFor="title">{t('createArticle.formTitle') as string}</Label>
                  <Input
                    id="title"
                    value={title}
                    onChange={(e) => setTitle(e.target.value)}
                    placeholder={t('createArticle.titlePlaceholder') as string}
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="excerpt">{t('createArticle.excerpt') as string}</Label>
                  <p className="text-sm text-gray-500">{t('createArticle.briefSummary') as string}</p>
                  <Textarea
                    id="excerpt"
                    value={excerpt}
                    onChange={(e) => setExcerpt(e.target.value)}
                    placeholder={t('createArticle.excerptPlaceholder') as string}
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="content">{t('createArticle.content') as string}</Label>
                  <Textarea
                    id="content"
                    value={content}
                    onChange={(e) => setContent(e.target.value)}
                    placeholder={t('createArticle.contentPlaceholder') as string}
                    className="min-h-[300px]"
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="coverImage">{t('createArticle.coverImage') as string}</Label>
                  <Input
                    id="coverImage"
                    value={coverImage}
                    onChange={(e) => setCoverImage(e.target.value)}
                    placeholder={t('createArticle.coverImagePlaceholder') as string}
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="category">{t('createArticle.category') as string}</Label>
                  <Input
                    id="category"
                    value={category}
                    onChange={(e) => setCategory(e.target.value)}
                    placeholder={t('createArticle.selectCategory') as string}
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="tags">
                    {t('createArticle.tags') as string}
                    <span className="text-sm text-gray-500 ml-2">
                      ({t('createArticle.commaSeparated') as string})
                    </span>
                  </Label>
                  <Input
                    id="tags"
                    value={tags}
                    onChange={(e) => setTags(e.target.value)}
                    placeholder={t('createArticle.tagsPlaceholder') as string}
                  />
                </div>

                <div className="flex gap-4">
                  <Button 
                    type="submit" 
                    className="bg-serein-500 hover:bg-serein-600"
                    disabled={isSubmitting}
                  >
                    {isSubmitting ? t('createArticle.publishing') as string : t('createArticle.publish') as string}
                  </Button>
                  <Button 
                    type="button"
                    variant="outline"
                    onClick={() => navigate(`/article/${id}`)}
                  >
                    {t('createArticle.cancel') as string}
                  </Button>
                </div>
              </form>
            </div>
          </div>
        </section>
      </main>

      <Footer />
    </div>
  );
};

export default EditArticle; 