
import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useToast } from "@/hooks/use-toast";
import { useLanguage } from "@/contexts/LanguageContext";
import { z } from "zod";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";

// Define the validation schema using zod
export const articleFormSchema = z.object({
  title: z.string().min(5, { message: "Title must be at least 5 characters" }).max(100, { message: "Title must be less than 100 characters" }),
  excerpt: z.string().max(200, { message: "Excerpt must be less than 200 characters" }).optional().or(z.literal("")),
  content: z.string().min(50, { message: "Content must be at least 50 characters" }),
  category: z.string().min(1, { message: "Please select a category" }),
  tags: z.string().optional().or(z.literal("")),
  coverImageUrl: z.string().url({ message: "Please enter a valid URL" }).optional().or(z.literal(""))
});

// Define the form data type based on the schema
export type ArticleFormData = z.infer<typeof articleFormSchema>;

export function useArticleForm() {
  const navigate = useNavigate();
  const { toast } = useToast();
  const { t } = useLanguage();
  const [isSubmitting, setIsSubmitting] = useState(false);
  
  const form = useForm<ArticleFormData>({
    resolver: zodResolver(articleFormSchema),
    defaultValues: {
      title: "",
      excerpt: "",
      content: "",
      category: "",
      tags: "",
      coverImageUrl: ""
    }
  });
  
  const handleSubmitArticle = async (formData: ArticleFormData) => {
    setIsSubmitting(true);
    
    try {
      // Simulate API call with timeout
      await new Promise(resolve => setTimeout(resolve, 1500));
      
      toast({
        title: t("createArticle.success"),
        description: t("createArticle.successMsg"),
      });
      
      navigate("/articles");
    } catch (error) {
      toast({
        title: t("createArticle.error"),
        description: t("createArticle.errorMsg"),
        variant: "destructive",
      });
    } finally {
      setIsSubmitting(false);
    }
  };

  return {
    form,
    isSubmitting,
    handleSubmitArticle
  };
}
