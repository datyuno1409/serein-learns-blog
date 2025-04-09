
import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useToast } from "@/hooks/use-toast";
import { useLanguage } from "@/contexts/LanguageContext";

export interface ArticleFormData {
  title: string;
  excerpt: string;
  content: string;
  category: string;
  tags: string;
  coverImageUrl: string;
}

export function useArticleForm() {
  const navigate = useNavigate();
  const { toast } = useToast();
  const { t } = useLanguage();
  
  const handleSubmitArticle = async (formData: ArticleFormData) => {
    if (!formData.title || !formData.content || !formData.category) {
      toast({
        title: t("createArticle.missingFields"),
        description: t("createArticle.fillRequiredFields"),
        variant: "destructive",
      });
      return;
    }

    // Simulate API call with timeout
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    toast({
      title: t("createArticle.success"),
      description: t("createArticle.successMsg"),
    });
    
    navigate("/articles");
  };

  return {
    handleSubmitArticle
  };
}
