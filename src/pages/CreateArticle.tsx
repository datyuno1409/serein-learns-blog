
import { useEffect } from "react";
import { useNavigate } from "react-router-dom";
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import { useToast } from "@/hooks/use-toast";
import { useAuth } from "@/contexts/AuthContext";
import { useLanguage } from "@/contexts/LanguageContext";
import { ArticleForm } from "@/components/article/ArticleForm";
import { useArticleForm } from "@/hooks/useArticleForm";

const CreateArticle = () => {
  const navigate = useNavigate();
  const { toast } = useToast();
  const { isAuthenticated } = useAuth();
  const { t } = useLanguage();
  const { form, isSubmitting, handleSubmitArticle, setUploadedFile } = useArticleForm();
  
  useEffect(() => {
    if (!isAuthenticated) {
      toast({
        title: t("auth.unauthorized") as string,
        description: t("auth.loginRequired") as string,
        variant: "destructive",
      });
      navigate("/login");
    }
  }, [isAuthenticated, navigate, toast, t]);

  if (!isAuthenticated) {
    return null;
  }

  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow">
        <section className="bg-gray-50 py-8">
          <div className="container">
            <h1 className="text-3xl font-bold mb-2">{t("createArticle.title") as string}</h1>
            <p className="text-gray-600 mb-0">
              {t("createArticle.subtitle") as string}
            </p>
          </div>
        </section>

        <section className="py-12">
          <div className="container">
            <div className="max-w-3xl mx-auto">
              <ArticleForm 
                form={form} 
                onSubmit={handleSubmitArticle} 
                isSubmitting={isSubmitting}
                onImageSelected={(file) => setUploadedFile(file)}
              />
            </div>
          </div>
        </section>
      </main>

      <Footer />
    </div>
  );
};

export default CreateArticle;
