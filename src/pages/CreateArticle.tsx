import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import Header from "@/components/Header";
import Footer from "@/components/Footer";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { 
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue 
} from "@/components/ui/select";
import { useToast } from "@/hooks/use-toast";
import { useAuth } from "@/contexts/AuthContext";
import { useLanguage } from "@/contexts/LanguageContext";

const CATEGORIES = ["Cybersecurity", "Web Development", "Cryptography", "Software Architecture", "DevOps"];

const CreateArticle = () => {
  const navigate = useNavigate();
  const { toast } = useToast();
  const { isAuthenticated } = useAuth();
  const { t } = useLanguage();
  
  const [formData, setFormData] = useState({
    title: "",
    excerpt: "",
    content: "",
    category: "",
    tags: "",
    coverImageUrl: "",
  });
  const [isSubmitting, setIsSubmitting] = useState(false);

  useEffect(() => {
    if (!isAuthenticated) {
      toast({
        title: t("auth.unauthorized"),
        description: t("auth.loginRequired"),
        variant: "destructive",
      });
      navigate("/login");
    }
  }, [isAuthenticated, navigate, toast, t]);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleCategoryChange = (value: string) => {
    setFormData(prev => ({ ...prev, category: value }));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);

    if (!formData.title || !formData.content || !formData.category) {
      toast({
        title: t("createArticle.missingFields"),
        description: t("createArticle.fillRequiredFields"),
        variant: "destructive",
      });
      setIsSubmitting(false);
      return;
    }

    setTimeout(() => {
      toast({
        title: t("createArticle.success"),
        description: t("createArticle.successMsg"),
      });
      setIsSubmitting(false);
      navigate("/articles");
    }, 1500);
  };

  if (!isAuthenticated) {
    return null;
  }

  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      
      <main className="flex-grow">
        <section className="bg-gray-50 py-8">
          <div className="container">
            <h1 className="text-3xl font-bold mb-2">{t("createArticle.title")}</h1>
            <p className="text-gray-600 mb-0">
              {t("createArticle.subtitle")}
            </p>
          </div>
        </section>

        <section className="py-12">
          <div className="container">
            <div className="max-w-3xl mx-auto">
              <form onSubmit={handleSubmit} className="space-y-8">
                <div className="space-y-2">
                  <Label htmlFor="title" className="text-base">
                    {t("createArticle.formTitle")} <span className="text-red-500">*</span>
                  </Label>
                  <Input
                    id="title"
                    name="title"
                    value={formData.title}
                    onChange={handleChange}
                    placeholder={t("createArticle.titlePlaceholder")}
                    className="text-lg"
                    required
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="excerpt" className="text-base">
                    {t("createArticle.excerpt")} <span className="text-gray-500 text-sm">({t("createArticle.briefSummary")})</span>
                  </Label>
                  <Textarea
                    id="excerpt"
                    name="excerpt"
                    value={formData.excerpt}
                    onChange={handleChange}
                    placeholder={t("createArticle.excerptPlaceholder")}
                    className="resize-none"
                    rows={3}
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="content" className="text-base">
                    {t("createArticle.content")} <span className="text-red-500">*</span>
                  </Label>
                  <Textarea
                    id="content"
                    name="content"
                    value={formData.content}
                    onChange={handleChange}
                    placeholder={t("createArticle.contentPlaceholder")}
                    className="min-h-[300px]"
                    required
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="coverImageUrl" className="text-base">
                    {t("createArticle.coverImage")}
                  </Label>
                  <Input
                    id="coverImageUrl"
                    name="coverImageUrl"
                    value={formData.coverImageUrl}
                    onChange={handleChange}
                    placeholder={t("createArticle.coverImagePlaceholder")}
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="category" className="text-base">
                    {t("createArticle.category")} <span className="text-red-500">*</span>
                  </Label>
                  <Select 
                    value={formData.category} 
                    onValueChange={handleCategoryChange}
                  >
                    <SelectTrigger id="category">
                      <SelectValue placeholder={t("createArticle.selectCategory")} />
                    </SelectTrigger>
                    <SelectContent>
                      {CATEGORIES.map(category => (
                        <SelectItem key={category} value={category}>
                          {category}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="tags" className="text-base">
                    {t("createArticle.tags")} <span className="text-gray-500 text-sm">({t("createArticle.commaSeparated")})</span>
                  </Label>
                  <Input
                    id="tags"
                    name="tags"
                    value={formData.tags}
                    onChange={handleChange}
                    placeholder={t("createArticle.tagsPlaceholder")}
                  />
                </div>

                <div className="pt-4 flex gap-4">
                  <Button
                    type="submit"
                    disabled={isSubmitting}
                    className="bg-serein-500 hover:bg-serein-600"
                  >
                    {isSubmitting ? t("createArticle.publishing") : t("createArticle.publish")}
                  </Button>
                  <Button
                    type="button"
                    variant="outline"
                    onClick={() => navigate("/articles")}
                  >
                    {t("createArticle.cancel")}
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

export default CreateArticle;
