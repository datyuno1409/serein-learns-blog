import { useState } from "react";
import { useForm } from "react-hook-form";
import { useNavigate } from "react-router-dom";
import { useToast } from "@/hooks/use-toast";
import { useLanguage } from "@/contexts/LanguageContext";
import { zodResolver } from "@hookform/resolvers/zod";
import * as z from "zod";
import { addArticle } from "@/data/articles";

// Define the validation schema using zod
const articleFormSchema = z.object({
  title: z.string().min(1, "Title is required"),
  excerpt: z.string().optional(),
  content: z.string().min(1, "Content is required"),
  category: z.string().min(1, "Category is required"),
  tags: z.string().optional(),
  coverImageUrl: z.string().optional(),
});

// Define the form data type based on the schema
export type ArticleFormData = z.infer<typeof articleFormSchema>;

export const useArticleForm = () => {
  const [isSubmitting, setIsSubmitting] = useState(false);
  const navigate = useNavigate();
  const { toast } = useToast();
  const { t } = useLanguage();

  const form = useForm<ArticleFormData>({
    resolver: zodResolver(articleFormSchema),
    defaultValues: {
      title: "",
      excerpt: "",
      content: "",
      category: "",
      tags: "",
      coverImageUrl: "",
    },
  });

  const handleSubmitArticle = async (data: ArticleFormData) => {
    try {
      setIsSubmitting(true);

      // Process tags
      const tags = data.tags
        ? data.tags.split(",").map(tag => tag.trim()).filter(Boolean)
        : [];

      // Create article object
      const articleData = {
        title: data.title,
        excerpt: data.excerpt || "",
        content: data.content,
        coverImage: data.coverImageUrl || "/placeholder-cover.jpg",
        category: data.category,
        tags: tags,
        author: "Serein", // TODO: Get from auth context
        authorId: "callmeserein",
        authorImage: "/avatar.jpg",
        publishedAt: new Date().toISOString(),
        readTime: Math.ceil(data.content.split(" ").length / 200), // Estimate read time based on word count
      };

      // Add the article
      const newArticle = addArticle(articleData);

      if (newArticle) {
        toast({
          title: t("createArticle.success") as string,
          description: t("createArticle.successMsg") as string,
        });
        navigate("/articles");
      } else {
        throw new Error("Failed to create article");
      }
    } catch (error) {
      console.error("Error saving article:", error);
      toast({
        title: "Error",
        description: "Failed to save article",
        variant: "destructive",
      });
    } finally {
      setIsSubmitting(false);
    }
  };

  return {
    form,
    isSubmitting,
    handleSubmitArticle,
  };
};
