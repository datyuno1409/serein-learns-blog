
import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import * as z from "zod";
import { toast } from "sonner";
import { useLanguage } from "@/contexts/LanguageContext";
import { addArticle } from "@/data/articles";

// Define the form schema using zod
const formSchema = z.object({
  title: z.string().min(1, { message: "Title is required" }).max(100),
  image: z.string().min(1, { message: "Image is required" }),
  description: z.string().min(10, { message: "Description must be at least 10 characters" }),
  body: z.string().min(50, { message: "Content must be at least 50 characters" }),
  tags: z.string().optional(),
});

export type ArticleFormValues = z.infer<typeof formSchema>;

export const useArticleForm = () => {
  const navigate = useNavigate();
  const { t } = useLanguage();
  const [isSubmitting, setIsSubmitting] = useState(false);

  // Initialize the form with react-hook-form and zod validation
  const form = useForm<ArticleFormValues>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      title: "",
      image: "",
      description: "",
      body: "",
      tags: "",
    },
  });

  const handleSubmitArticle = async (values: ArticleFormValues) => {
    try {
      setIsSubmitting(true);
      
      // Simulate API call with a timeout
      await new Promise(resolve => setTimeout(resolve, 1000));
      
      // Process tags
      const tagsList = values.tags ? values.tags.split(',').map(tag => tag.trim()) : [];
      
      // Create a new article object with the correct structure matching the Article interface
      const articleData = {
        title: values.title,
        excerpt: values.description,
        content: values.body,
        coverImage: values.image,
        author: "Serein",
        authorId: "callmeserein",
        authorImage: "/profile.jpg",
        category: "Web Development", // Default category, ideally should come from a form field
        tags: tagsList,
        publishedAt: new Date().toISOString(),
        readTime: Math.ceil(values.body.length / 1000) // Rough estimate: 1000 chars ≈ 1 min
      };
      
      // Add the new article using the data service function
      addArticle(articleData);
      
      // Show success toast and navigate to the articles page
      toast.success(String(t("createArticle.success")));
      navigate("/articles");
    } catch (error) {
      // Show error toast if something goes wrong
      toast.error("Failed to create article");
      console.error("Error creating article:", error);
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
